<?php

namespace FittieApplications\Google\OAuth\Client;

use Exception;
use Fittie\Component\Analytics\DataSet;
use Fittie\Component\Analytics\Exception\InvalidMetricType;
use Fittie\Component\Analytics\Measurement;
use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Analytics\MetricOverTime;
use Fittie\Component\ApplicationAuth\OAuth2\Client\OAuthClientInterface;
use Fittie\Component\ApplicationAuth\OAuth2\Credentials\OAuth2Credentials;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\AccessToken;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\RefreshToken;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\Scope;
use Fittie\Component\User\Entity\UserID;
use Carbon\Carbon;
use Google_Client;
use Google_Exception;
use Google_Service_Fitness;
use Google_Service_Fitness_AggregateBy;
use Google_Service_Fitness_AggregateRequest;
use Google_Service_Fitness_BucketByTime;
use Google_Service_Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GoogleOAuthClient implements OAuthClientInterface
{
    private OAuth2Credentials $connection;
    private Google_Client $client;

    public function __construct(Google_Client $client, OAuth2Credentials $credentials = null)
    {
        $this->client = $client;

        if ($credentials !== null) {
            $this->connection = $credentials;
            $this->setConnection($credentials);
        }
    }

    /**
     * @throws Google_Exception
     */
    public static function makeClient(OAuth2Credentials $credentials = null): OAuthClientInterface
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('secrets/client_secret.json'));
        $client->setRedirectUri(url('/oauth/google/redirect'));
        $client->setAccessType('offline');

        return new GoogleOAuthClient($client, $credentials);
    }

    /**
     * @param Scope[] $scopes
     * @return string
     */
    public function getAuthorizationUrl(array $scopes): string
    {
        return $this->client->createAuthUrl(array_map(
            fn($scope) => $scope->getKey(),
            $scopes
        ));
    }

    public function handleAuthorizationResponse(UserID $userID, Request $request): OAuth2Credentials
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($request->get('code'));

        if (!Arr::has($accessToken, 'refresh_token')) {
            throw new Exception('Missing refresh token');
        }

        if (!Arr::has($accessToken, 'access_token')) {
            throw new Exception('Missing access token');
        }

        $connection = $this->mapAccessTokenToCredentials($userID, $accessToken);

        $this->setConnection($connection);

        return $connection;
    }

    public function getUserInfo()
    {
        $oauth2Service = new Google_Service_Oauth2($this->client);
        return $oauth2Service->userinfo_v2_me->get();
    }

    public function getMeasurements(MeasurementsRequest $request): DataSet
    {
        $fitnessClient = new Google_Service_Fitness($this->client);

        switch ($request->getMetricType()->getName()) {
            case 'steps':
                $dataType = 'com.google.step_count.delta';
                break;
            case 'weight':
                $dataType = 'com.google.weight';
                break;
            default:
                throw new InvalidMetricType();
        }

        $aggregateRequest = new Google_Service_Fitness_AggregateRequest();
        $aggregateBy = new Google_Service_Fitness_AggregateBy();
        $aggregateBy->setDataTypeName($dataType);
        $aggregateRequest->setAggregateBy([$aggregateBy]);
        $aggregateRequest->setStartTimeMillis($request->getStart()->getTimestamp() * 1000);
        $aggregateRequest->setEndTimeMillis($request->getEnd()->getTimestamp() * 1000);

        $bucket = new Google_Service_Fitness_BucketByTime();
        $bucket->setDurationMillis(60*60*1000);
        $aggregateRequest->setBucketByTime($bucket);

        $result = $fitnessClient->users_dataset->aggregate('me', $aggregateRequest, [
            'fields' => 'bucket(startTimeMillis, endTimeMillis,dataset(point))',
        ]);

        switch ($request->getMetricType()->getName()) {
            case 'steps':
                $unit = 'steps';
                break;
            case 'weight':
                $unit = 'kg';
                break;
        }

        $measurements = new DataSet(new MetricOverTime($request->getMetricType()->getName(), $unit, 60*60));
        /** @var \Google_Service_Fitness_AggregateBucket $bucket */
        foreach($result->getBucket() as $bucket) {
            if (count($bucket->getDataset()) === 0) {
                continue;
            }

            if (count($bucket->getDataset()[0]->getPoint()) === 0) {
                continue;
            }

            $measurement = new Measurement(
                Carbon::createFromTimestampMs($bucket->getStartTimeMillis())->toDate(),
                $bucket->getDataset()[0]->getPoint()[0]->getValue()[0]->getIntVal()
            );

            $measurements->addMeasurement($measurement);
        }

        return $measurements;
    }

    public function setConnection(OAuth2Credentials $connection)
    {
        $expiresIn = Carbon::instance($connection->getAccessToken()->getExpirationDate())->diffInSeconds(Carbon::now());

        $this->client->setAccessToken([
            'access_token' => (string)$connection->getAccessToken(),
            'refresh_token' => (string)$connection->getRefreshToken(),
            'created' => time(),
            'expires_in' => $expiresIn,
        ]);
    }

    public function isTokenExpired()
    {
        return !$this->client->isAccessTokenExpired();
    }

    public function refreshToken(): OAuth2Credentials
    {
        $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken();

        $credentials = $this->mapAccessTokenToCredentials($this->connection->getUserID(), $newAccessToken);

        $this->connection = $credentials;
        return $credentials;
    }

    private function mapAccessTokenToCredentials(UserID $userID, $accessToken) {

        if (!Arr::has($accessToken, 'refresh_token')) {
            throw new Exception('Missing refresh token');
        }

        if (!Arr::has($accessToken, 'access_token')) {
            throw new Exception('Missing access token');
        }

        $expirationDate = Carbon::now()->addSeconds($accessToken['expires_in']);
        $userInfo = $this->getUserInfo();

        return new OAuth2Credentials(
            $userID,
            new AccessToken($accessToken['access_token'], $expirationDate),
            new RefreshToken($accessToken['refresh_token']),
            [
                'application_account_id' => $userInfo->id,
                'application_account_name' => $userInfo->name,
            ]
        );
    }
}
