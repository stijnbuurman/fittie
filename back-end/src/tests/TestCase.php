<?php

namespace Tests;

use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Analytics\Metric\MetricType;
use Fittie\Component\Application\Client\ApplicationClientInterface;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\Application\Entity\ApplicationInterface;
use Fittie\Component\Application\Registrar\ApplicationRegistrar;
use Fittie\Component\Application\Repository\ApplicationConnectionRepositoryInterface;
use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\ApplicationAuth\Credentials;
use Fittie\Component\User\Entity\UserID;
use Fittie\Component\User\Model\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function mockUser()
    {
        $user = factory(User::class)->make();

        $this->be($user);

        return $user;
    }

    public function mockApplication($authFlow = null, $applicationClient = null) {

        $application = $this->mock(ApplicationInterface::class);
        $application->shouldReceive('getName')->andReturn('Fake Application');
        $application->shouldReceive('getSlug')->andReturn('fake-application');
        $application->shouldReceive('availableMetricTypes')->andReturn([MetricType::steps()]);

        $authFlow = $authFlow ?: $this->mock(AuthFlow::class);

        $applicationClient = $applicationClient ?: $this->mock(ApplicationClientInterface::class);

        $authFlow->shouldReceive('getAuthenticatedClient')->andReturn($applicationClient);

        $application->shouldReceive('getAuthFlow')->andReturn($authFlow);
        return $application;
    }

    public function mockApplicationRegistrar($applications = []) {
        $applicationRegistrar = $this->mock(ApplicationRegistrar::class);
        $applicationRegistrar->shouldReceive('all')->andReturn($applications);
        $applicationRegistrar->shouldReceive('get')->andReturn($applications[0]);
    }

    public function mockApplicationConnection(ApplicationInterface $application = null, UserID $userID = null)
    {
        if ($application === null) {
            $application = $this->mockApplication();
        }

        $credentials = $this->mock(Credentials::class);

        $applicationConnection = $this->mock(ApplicationConnection::class);
        $applicationConnection->shouldReceive('getApplication')->andReturn($application);
        $applicationConnection->shouldReceive('getID')->andReturn(new ApplicationConnectionID());
        $applicationConnection->shouldReceive('getUserID')->andReturn($userID ?: new UserID());

        $applicationConnection->shouldReceive('getAccountName')->andReturn('id');
        $applicationConnection->shouldReceive('getAccountID')->andReturn('id');
        $applicationConnection->shouldReceive('getApplication')->andReturn($application);
        $applicationConnection->shouldReceive('getCredentials')->andReturn($credentials);

        return $applicationConnection;
    }

    public function mockApplicationConnectionRepository($applicationConnections = [])
    {
        $repo = $this->mock(ApplicationConnectionRepositoryInterface::class);
        $repo->shouldReceive('all')->andReturn($applicationConnections);

        foreach ($applicationConnections as $applicationConnection) {
            $repo->shouldReceive('get')->withArgs(function ($userID, $applicationConnectionID) use ($applicationConnection) {
                return $userID->equals($applicationConnection->getUserID())
                    && $applicationConnectionID->equals($applicationConnectionID);
            })->andReturn($applicationConnection);
        }

        return $repo;
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
