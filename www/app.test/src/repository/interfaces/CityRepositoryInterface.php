<?php

namespace app\repository\interfaces;

use app\domain\aggregate\City;
use app\domain\exception\CityNotFoundException;
use Ramsey\Uuid\UuidInterface;

interface CityRepositoryInterface{

    /**
     * @throws CityNotFoundException
     * @param UuidInterface $uuid
     * @return mixed
     */
    public function findById(UuidInterface $uuid): City;
}