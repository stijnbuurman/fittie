<?php

namespace FittieApplications\Withings\OAuth\Client;

use Exception;
use Fittie\Component\Analytics\DataSet;
use Fittie\Component\Analytics\Exception\InvalidMetricType;
use Fittie\Component\Analytics\Measurement;
use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Analytics\Metric;
use Fittie\Component\ApplicationAuth\OAuth2\Client\OAuthClientInterface;
use Fittie\Component\ApplicationAuth\OAuth2\Credentials\OAuth2Credentials;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\AccessToken;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\RefreshToken;
use Fittie\Component\User\Entity\UserID;
use Carbon\Carbon;
use Google_Exception;
use Illuminate\Http\Request;
use waytohealth\OAuth2\Client\Provider\Withings;

class WithingsOAuthClient implements OAuthClientInterface
{
    private \League\OAuth2\Client\Token\AccessToken $accessToken;
    private OAuth2Credentials $oauthConnection;
    private Withings $client;

    public function __construct(Withings $client, OAuth2Credentials $connection = null)
    {
        $this->client = $client;

        if ($connection !== null) {
            $this->setConnection($connection);
        }
    }

    /**
     * @throws Google_Exception
     */
    public static function makeClient(OAuth2Credentials $credentials = null): OAuthClientInterface
    {
        $client = new Withings([
            'clientId'          => env('WITHINGS_CLIENT_ID'),
            'clientSecret'      => env('WITHINGS_CLIENT_SECRET'),
            'redirectUri'       => env('WITHINGS_REDIRECT_URI')
        ]);

        return new WithingsOAuthClient($client, $credentials);
    }

    public function getAuthorizationUrl(array $scopes): string
    {
        return $this->client->getAuthorizationUrl();
    }

    public function handleAuthorizationResponse(UserID $userID, Request $request): OAuth2Credentials
    {
        $accessToken = $this->client->getAccessToken('authorization_code', [
            'code' => $request->get('code')
        ]);

        if ($accessToken->hasExpired()) {
            throw new Exception('Invalid access token');
        }

        $connection = new OAuth2Credentials(
            $userID,
            new AccessToken($accessToken->getToken(), Carbon::createFromTimestamp($accessToken->getExpires())),
            new RefreshToken($accessToken->getRefreshToken()),
            [
                'application_account_id' => $accessToken->getValues()['userid'],
            ],
        );

        $this->setConnection($connection);

        return $connection;
    }

    public function getMeasurements(MeasurementsRequest $request): DataSet
    {
        $start = $request->getStart()->getTimestamp();
        $end = $request->getEnd()->getTimestamp();

        switch ($request->getMetricType()->getName()) {
            case 'weight':
                $dataType = 1;
                $unit = 'kg';
                break;
            case 'fatFreeMass':
                $dataType = 5;
                $unit = 'kg';
                break;
            default:
                throw new InvalidMetricType();
        }

        $withingsRequest = $this->client->getAuthenticatedRequest(
            Withings::METHOD_GET,
            Withings::BASE_WITHINGS_API_URL . '/measure?action=getmeas&meastype=' . $dataType . '&category=1&startdate=' . $start . '&enddate=' . $end,
            $this->accessToken,
            ['headers' => [Withings::HEADER_ACCEPT_LANG => 'nl_NL'], [Withings::HEADER_ACCEPT_LOCALE => 'nl_NL']]
        );

        $response = $this->client->getParsedResponse($withingsRequest);
        $measurements = new DataSet(new Metric($request->getMetricType()->getName(), $unit));

        foreach($response['body']['measuregrps'] as $measure) {
            $start = Carbon::createFromTimestamp($measure['date'])->toDate();
            $value = $measure['measures'][0]['value'] / 1000;
            $measurements->addMeasurement(new Measurement($start, $value));
        }

        return $measurements;
    }

    public function setConnection(OAuth2Credentials $connection)
    {
        $this->accessToken = new \League\OAuth2\Client\Token\AccessToken($connection->toArray());
        $this->oauthConnection = $connection;
    }

    public function isTokenExpired()
    {
        return $this->accessToken->hasExpired();
    }

    public function refreshToken(): OAuth2Credentials
    {
        if (!$this->accessToken) {
            throw new Exception('No refresh token found');
        }

        $accessToken = $this->client->getAccessToken('refresh_token', [
            'refresh_token' => $this->accessToken->getRefreshToken()
        ]);

        $newConnection = new OAuth2Credentials(
            $this->oauthConnection->getUserID(),
            new AccessToken($accessToken->getToken(), Carbon::createFromTimestamp($accessToken->getExpires())),
            new RefreshToken($accessToken->getRefreshToken()),
            [
                'application_account_id' => $accessToken->getValues()['userid'],
            ],
        );

        $this->setConnection($newConnection);

        return $newConnection;
    }
}
