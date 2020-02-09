<?php


namespace Fittie\Component\ApplicationAuth\UserPassword\AuthFlow;


use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\ApplicationAuth\AuthFlow;

class UserPasswordAuthFlow implements AuthFlow
{
    public function authenticate(ApplicationInterface $application)
    {
        return redirect(route('auth-user-password', ['application' => $application->getSlug()]));
    }

    public function getAuthenticatedClient(ApplicationConnection $applicationConnection): ApplicationClientInterface
    {
        return $applicationConnection->getApplication()->makeClient($applicationConnection->getCredentials());
    }
}
