<?php

namespace Fittie\Component\Application\Controller;

use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Registrar\ApplicationRegistrar;
use Fittie\Component\User\Entity\UserID;
use Fittie\Http\Controllers\Controller;
use Fittie\Component\Application\Repository\ApplicationConnectionRepositoryInterface;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listApplications(
        ApplicationRegistrar $registrar,
        ApplicationConnectionRepositoryInterface $repository
    ) {
        return view('components.application.index')
            ->with('applications', $registrar->all())
            ->with('applicationConnections', $repository->all(new UserID(auth()->id())));
    }

    public function deleteApplicationConnection(
        ApplicationConnectionRepositoryInterface $repository,
        ApplicationConnection $applicationConnection
    )
    {
        $repository->delete(new UserID(auth()->id()), $applicationConnection->getID());

        return redirect(route('applications'));
    }

    public function createApplicationConnection(ApplicationInterface $application)
    {
        return $application->getAuthFlow()
            ->authenticate($application);
    }
}
