<?php

namespace Fittie\Component\ApplicationAuth\UserPassword\Client;

use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\ApplicationAuth\UserPassword\Credentials\UserPasswordCredentials;

interface UserPasswordClientInterface extends ApplicationClientInterface
{
    static public function makeClient(UserPasswordCredentials $credentials): UserPasswordClientInterface;
}
