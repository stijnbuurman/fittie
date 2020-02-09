<?php
namespace Fittie\Component\ApplicationAuth\OAuth2\ValueObject;

use DateTime;

class AccessToken
{
    private string $token;
    private DateTime $expirationDate;

    public function __construct(string $token, DateTime $expirationDate)
    {
        $this->token = $token;
        $this->expirationDate = $expirationDate;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function getExpirationDate(): DateTime
    {
        return $this->expirationDate;
    }

    public function __toString()
    {
        return $this->token;
    }
}
