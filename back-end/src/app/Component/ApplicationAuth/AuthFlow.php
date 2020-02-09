<?php


namespace Fittie\Component\ApplicationAuth;


use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationInterface;

interface AuthFlow
{
    public function authenticate(ApplicationInterface $application);

    public function getAuthenticatedClient(ApplicationConnection $applicationConnection): ApplicationClientInterface;
}
