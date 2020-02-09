<?php


namespace Fittie\Component\Analytics;


class Metric
{
    protected string $name;
    protected string $unit;

    public function __construct(string $name, string $unit)
    {
        $this->name = $name;
        $this->unit = $unit;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function __toString()
    {
        return $this->name;
    }
}
