<?php


namespace Fittie\Component\Analytics\Metric;


class MetricType
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function restingHeartRate()
    {
        return new MetricType('restingHeartRate');
    }

    public function getName(): string
    {
        return $this->name;
    }

    static function weight() {
        return new MetricType('weight');
    }

    static function steps() {
        return new MetricType('steps');
    }

    static function fatFreeMass() {
        return new MetricType('fatFreeMass');
    }
}
