<?php
namespace Fittie\Component\ApplicationAuth\OAuth2\Client;

use Fittie\Component\Analytics\DataSet;
use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\ApplicationAuth\OAuth2\Credentials\OAuth2Credentials;
use Fittie\Component\User\Entity\UserID;
use Illuminate\Http\Request;

interface OAuthClientInterface extends ApplicationClientInterface
{
    public function isTokenExpired();

    public function getAuthorizationUrl(array $scopes): string;

    public function handleAuthorizationResponse(UserID $userID, Request $request);

    public function refreshToken(): OAuth2Credentials;

    public static function makeClient(OAuth2Credentials $credentials = null): OAuthClientInterface;

    public function getMeasurements(MeasurementsRequest $request): DataSet;
}
