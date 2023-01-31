<?php

namespace app\domain\entity;



use Ramsey\Uuid\UuidInterface;

class Country
{
    private UuidInterface $id;
    private string $name;

    public function __construct(UuidInterface $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}