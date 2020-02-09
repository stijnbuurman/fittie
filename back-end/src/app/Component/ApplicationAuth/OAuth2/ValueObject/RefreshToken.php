<?php
namespace Fittie\Component\ApplicationAuth\OAuth2\ValueObject;


class RefreshToken
{
    private string $refreshToken;

    public function __construct(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function getToken(): string {
        return $this->refreshToken;
    }

    public function __toString()
    {
        return $this->refreshToken;
    }
}
