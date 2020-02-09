<?php


namespace Tests\Feature\Component\Application;


use Fittie\Component\ApplicationAuth\AuthFlow;
use Fittie\Component\User\Entity\UserID;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
{
    public function testListApplications()
    {
        $application = $this->mockApplication();

        $this->mockApplicationRegistrar([$application]);
        $applicationConnection = $this->mockApplicationConnection($application);
        $this->mockApplicationConnectionRepository([$applicationConnection]);

        $this->be($this->mockUser());

        // run
        $response = $this->get(route('applications'));

        // assert
        $response->assertStatus(200);
        $response->assertViewHas('applications', [$application]);
        $response->assertViewHas('applicationConnections', [$applicationConnection]);
    }

    public function testCreate()
    {
        $authFlow = $this->mock(AuthFlow::class);
        $application = $this->mockApplication($authFlow);

        $this->mockApplicationRegistrar([$application]);

        $authFlow->shouldReceive('authenticate')->andReturn(redirect('/fake'));

        $this->be($this->mockUser());

        // run
        $response = $this->get(route('create-application', [$application->getSlug()]));

        // assert
        $response->assertRedirect('/fake');
    }

    public function testDelete()
    {
        $user = $this->mockUser();
        $applicationConnection = $this->mockApplicationConnection(null, new UserID($user->id));
        $repository = $this->mockApplicationConnectionRepository([$applicationConnection]);

        $this->be($user);

        $repository->shouldReceive('delete')
            ->withArgs(function($userIDArg, $applicationIDArg) use ($applicationConnection, $user) {
                    return $userIDArg->equals(new UserID($user->id))
                        && $applicationIDArg->equals($applicationConnection->getID());
            })
            ->andReturn(1);


        // run
        $response = $this->get(route('delete-application', [$applicationConnection->getID()]));

        // assert
        $response->assertRedirect(route('applications'));
    }
}
