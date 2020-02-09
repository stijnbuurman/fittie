<?php


namespace Fittie\Component\Application\Mapper;


use Exception;
use Fittie\Component\ApplicationAuth\OAuth2\AuthFlow\OAuth2AuthFlow;
use Fittie\Component\ApplicationAuth\UserPassword\AuthFlow\UserPasswordAuthFlow;
use Fittie\Component\ApplicationAuth\UserPassword\Credentials\UserPasswordCredentials;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\Application\Registrar\ApplicationRegistrar;
use Fittie\Component\ApplicationAuth\OAuth2\Credentials\OAuth2Credentials;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\AccessToken;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\RefreshToken;
use Fittie\Component\User\Entity\UserID;
use Carbon\Carbon;

class ApplicationConnectionDBToEntityMapper
{
    private ApplicationRegistrar $dataProviderRegistrar;

    public function __construct(ApplicationRegistrar $dataProviderRegistrar)
    {
        $this->dataProviderRegistrar = $dataProviderRegistrar;
    }

    public function map($model)
    {
        $id = new ApplicationConnectionID($model->id);
        $userID = new UserID($model->user_id);
        $application = $this->dataProviderRegistrar->get($model->application);

        $credentialData = json_decode($model->credentials, true);

        $authFlow = $application->getAuthFlow();
        if ($authFlow instanceof OAuth2AuthFlow) {
            $credentials = new OAuth2Credentials(
                $userID,
                new AccessToken($credentialData['access_token'], Carbon::createFromTimestamp($credentialData['expires_in'])),
                new RefreshToken($credentialData['refresh_token']),
            );
        } else if ($authFlow instanceof UserPasswordAuthFlow) {
            $credentials = new UserPasswordCredentials($credentialData['username'], $credentialData['password']);
        } else {
            throw new Exception('Invalid credentials');
        }

        return new ApplicationConnection(
            $id,
            $userID,
            $application,
            $model->application_account_id,
            $model->application_account_name,
            $credentials
        );
    }
}
