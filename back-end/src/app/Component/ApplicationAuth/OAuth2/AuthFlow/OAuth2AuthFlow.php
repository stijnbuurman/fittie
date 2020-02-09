<?php


namespace Fittie\Component\ApplicationAuth\OAuth2\AuthFlow;


use Exception;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\Application\Event\ApplicationConnectionUpdated;
use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\ApplicationAuth\OAuth2\Client\OAuthClientInterface;

class OAuth2AuthFlow implements AuthFlow
{

    public function authenticate(ApplicationInterface $application)
    {
        return redirect(route('auth-oauth2', ['application' => $application->getSlug()]));
    }

    public function getAuthenticatedClient(ApplicationConnection $applicationConnection): ApplicationClientInterface
    {
        $client = $applicationConnection->getApplication()->makeClient($applicationConnection->getCredentials());

        if (!($client instanceof OAuthClientInterface)) {
            throw new Exception('Incompatible client in authentication flow');
        }

        if (!$client->isTokenExpired()) {
            return $client;
        }

        $newCredentials = $client->refreshToken();

        event(new ApplicationConnectionUpdated(ApplicationConnection::createFrom($applicationConnection, $newCredentials)));

        return $client;
    }
}
