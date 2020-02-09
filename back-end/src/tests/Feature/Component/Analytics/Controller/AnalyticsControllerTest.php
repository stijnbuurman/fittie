<?php

namespace Tests\Feature\Component\Analytics\Controller;

use Fittie\Component\Analytics\DataSet;
use Fittie\Component\Analytics\Exception\InvalidMetricType;
use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Analytics\Metric;
use Fittie\Component\Analytics\Service\AnalyticsService;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\User\Entity\UserID;
use Tests\TestCase;

class AnalyticsControllerTest extends TestCase
{
    public function testGetAnalytics()
    {
        $user = $this->mockUser();
        $applicationConnection = $this->mockApplicationConnection(null, new UserID($user->id));
        $this->mockApplicationConnectionRepository([$applicationConnection]);

        // Login as user
        $this->be($user);

        // Make the service return a dataset
        $dataset = new DataSet(new Metric('steps', 'steps'), []);
        $analyticsService = $this->mock(AnalyticsService::class);
        $analyticsService->shouldReceive('getMeasurements')
            ->andReturn($dataset);

        // Request the route
        $response = $this->get(route('analytics', ['steps', (string)$applicationConnection->getID()]));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('dataset', $dataset);
        $response->assertViewHas('metricRequest', function($metricRequest) {
            $this->assertInstanceOf(MeasurementsRequest::class, $metricRequest);
            $this->assertEquals('steps', $metricRequest->getMetricType()->getName());

            return true;
        });
    }

    public function testGetAnalyticsInvalidMeasurement()
    {
        $user = $this->mockUser();
        $this->mockApplicationConnectionRepository([$this->mockApplicationConnection(null, new UserID($user->id))]);

        // Login as user
        $this->be($user);

        // Make the service return a dataset
        $dataset = new DataSet(new Metric('steps', 'steps'), []);
        $analyticsService = $this->mock(AnalyticsService::class);
        $analyticsService->shouldReceive('getMeasurements')
            ->andThrow(new InvalidMetricType());

        // Request the route
        $response = $this->get(route('analytics', ['steps', (string)new ApplicationConnectionID()]));

        // Assert
        $response->assertStatus(500);
        $response->assertSee('Fittie\Component\Analytics\Exception\InvalidMetricType');
    }

}
