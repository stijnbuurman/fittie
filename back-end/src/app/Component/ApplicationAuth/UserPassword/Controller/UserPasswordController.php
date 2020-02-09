<?php

namespace Fittie\Component\ApplicationAuth\UserPassword\Controller;

use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\Application\Event\ApplicationConnectionCreated;
use Fittie\Component\ApplicationAuth\UserPassword\Credentials\UserPasswordCredentials;
use Fittie\Component\User\Entity\UserID;
use Fittie\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createUserPassword(ApplicationInterface $application)
    {
        return view('components.application-auth.user-password.create')
            ->with('application', $application);
    }

    public function postCreateUserPassword(ApplicationInterface $application, Request $request)
    {
        if (!$request->has(['username', 'password'])) {
            return redirect(route('auth-user-password', ['application' => $application->getSlug()]));
        }

        $username = $request->get('username');
        $password = $request->get('password');


        $credentials = new UserPasswordCredentials($username, $password);

        try {
            $client = $application->makeClient($credentials);
        } catch(\Exception $exception) {
            return redirect(route('auth-user-password', ['application' => $application->getSlug()]));
        }

        $applicationAuth = new ApplicationConnection(new ApplicationConnectionID(), new UserID(auth()->id()), $application, $credentials->getUsername(), $credentials->getUsername(), $credentials);
        event(new ApplicationConnectionCreated($applicationAuth));

        return redirect(route('applications'));
    }
}
