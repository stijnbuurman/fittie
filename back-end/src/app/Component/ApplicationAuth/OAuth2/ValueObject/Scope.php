<?php

namespace Fittie\Component\ApplicationAuth\OAuth2\ValueObject;

class Scope
{
    private string $name;
    private string $key;

    public function __construct(string $name, string $key)
    {
        $this->name = $name;
        $this->key = $key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
