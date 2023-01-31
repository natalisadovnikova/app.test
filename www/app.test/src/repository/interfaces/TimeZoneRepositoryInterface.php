<?php

namespace app\repository\interfaces;

use app\domain\exception\CityNotFoundException;
use Ramsey\Uuid\UuidInterface;

interface TimeZoneRepositoryInterface{

    public function setCityId(UuidInterface $cityUuid);

    public function getCityId(): UuidInterface;

    /**
     * Нужно ли добавлять данные
     * @return mixed
     */
    public function needFreshData(): bool;

    /**
     * Дабавить данные по временной зоне
     * @return mixed
     */
    public function refreshData($data);

    /**
     * Получить данные
     * @return mixed
     */
    public function getItems();
}