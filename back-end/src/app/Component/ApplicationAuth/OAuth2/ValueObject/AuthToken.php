<?php
namespace Fittie\Component\ApplicationAuth\OAuth2\ValueObject;

interface AuthToken
{
    public function getToken(): string;
}
