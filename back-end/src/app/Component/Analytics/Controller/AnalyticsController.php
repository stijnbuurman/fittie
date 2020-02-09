<?php

namespace Fittie\Component\Analytics\Controller;

use Fittie\Component\Analytics\MeasurementsRequest;
use Fittie\Component\Analytics\Metric\MetricType;
use Fittie\Component\Analytics\Service\AnalyticsService;
use Fittie\Component\Application\Entity\ApplicationConnection;
use Fittie\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getAnalytics(
        Request $request,
        MetricType $metricType,
        ApplicationConnection $applicationConnection,
        AnalyticsService $analyticsService
    ) {
        if ($request->has(['start', 'end'])) {
            $measurementsRequest = new MeasurementsRequest(
                Carbon::createFromTimestamp($request->get('start')),
                Carbon::createFromTimestamp($request->get('end')),
                $metricType
            );
        } else {
            $measurementsRequest = MeasurementsRequest::thisWeek($metricType);
        }

        $dataset = $analyticsService->getMeasurements($applicationConnection, $measurementsRequest);

        return view('components.analytics.index')
            ->with('dataset', $dataset)
            ->with('metricRequest', $measurementsRequest);
    }
}
