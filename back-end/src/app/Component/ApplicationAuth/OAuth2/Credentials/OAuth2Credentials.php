<?php

namespace Fittie\Component\ApplicationAuth\OAuth2\Credentials;

use Fittie\Component\ApplicationAuth\Credentials;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\AccessToken;
use Fittie\Component\ApplicationAuth\OAuth2\ValueObject\RefreshToken;
use Fittie\Component\User\Entity\UserID;

class OAuth2Credentials implements Credentials
{
    private UserID $userID;
    private AccessToken $accessToken;
    private RefreshToken $refreshToken;
    private array $vendorData;

    public function __construct(UserID $userID, AccessToken $accessToken, RefreshToken $refreshToken, array $vendorData = [])
    {
        $this->userID = $userID;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->vendorData = $vendorData;
    }

    public function getUserID(): UserID
    {
        return $this->userID;
    }

    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): RefreshToken
    {
        return $this->refreshToken;
    }

    public function getVendorData(): array
    {
        return $this->vendorData;
    }

    public function toArray(): array
    {
        return array_merge([
            'access_token' => $this->accessToken->getToken(),
            'expires_in' => $this->accessToken->getExpirationDate()->getTimestamp(),
            'refresh_token' => $this->refreshToken->getToken(),
        ], $this->vendorData);
    }
}
