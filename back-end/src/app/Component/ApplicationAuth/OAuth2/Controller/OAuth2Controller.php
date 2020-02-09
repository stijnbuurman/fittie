<?php

namespace Fittie\Component\ApplicationAuth\OAuth2\Controller;

use Fittie\Component\Application\Event\ApplicationConnectionCreated;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\ApplicationAuth\OAuth2\Credentials\OAuth2Credentials;
use Fittie\Component\User\Entity\UserID;
use Fittie\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OAuth2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function requestConsent(ApplicationInterface $application)
    {
        $client = $application->makeClient();

        $consentUrl = $client->getAuthorizationUrl($application->getScopes());

        return redirect($consentUrl);
    }

    public function handleRedirect(Request $request, ApplicationInterface $application)
    {
        $client = $application->makeClient();
        $credentials = $client->handleAuthorizationResponse(new UserID(auth()->id()), $request);

        if (!$credentials instanceof OAuth2Credentials) {
            throw new \Exception('Incompatible credentials');
        }

        $applicationConnection = new ApplicationConnection(
            new ApplicationConnectionID(),
            new UserID(auth()->id()),
            $application,
            Arr::get($credentials->getVendorData(), 'application_account_id'),
            Arr::get($credentials->getVendorData(), 'application_account_name', ''),
            $credentials
        );

        event(new ApplicationConnectionCreated($applicationConnection));

        return redirect(route('applications'));
    }
}
