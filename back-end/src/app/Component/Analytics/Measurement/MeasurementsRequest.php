<?php

namespace Fittie\Component\Analytics;

use Carbon\Carbon;
use DateTime;
use Fittie\Component\Analytics\Metric\MetricType;

class MeasurementsRequest
{
    private DateTime $start;
    private DateTime $end;
    private MetricType $metricType;

    public function __construct(DateTime $start, DateTime $end, MetricType $metricType)
    {
        $this->start = $start;
        $this->end = $end;
        $this->metricType = $metricType;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function getMetricType(): MetricType
    {
        return $this->metricType;
    }

    static public function today(MetricType $metricType) {
        $end = Carbon::now(config('fittie.timezone'))->endOfDay();
        $start = Carbon::now(config('fittie.timezone'))->startOfDay();

        return new MeasurementsRequest($start, $end, $metricType);
    }

    static public function thisWeek(MetricType $metricType) {
        $end = Carbon::now(config('fittie.timezone'))->endOfWeek();
        $start = Carbon::now(config('fittie.timezone'))->startOfWeek();

        return new MeasurementsRequest($start, $end, $metricType);
    }
}
