<?php


namespace Fittie\Common;


use Ramsey\Uuid\Uuid;

class ID
{
    private string $id;

    public function __construct($id = null)
    {
        if ($id === null) {
            $this->id = (string) Uuid::uuid4();
            return;
        }

        $this->id = (string) Uuid::fromString($id);
    }

    public function equals(ID $ID)
    {
        return (string) $this->id === (string) $ID;
    }

    public function __toString()
    {
        return $this->id;
    }
}
