<?php


namespace Fittie\Common;


abstract class Event
{
    abstract public function getName(): string;
    abstract public function getData(): array;

    public function toArray(): array
    {
        return [
            'event' => $this->getName(),
            'data' => $this->getData(),
        ];
    }
}
