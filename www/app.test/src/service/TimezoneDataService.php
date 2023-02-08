<?php

namespace app\service;

use app\domain\exception\DataNotFoundException;
use app\repository\SqlTimeZoneRepository;
use app\service\interfaces\TimeZoneIntevalDataServiceInterface;
use PDO;
use Ramsey\Uuid\UuidInterface;

class TimezoneDataService implements TimeZoneIntevalDataServiceInterface
{
    private $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Получение самой ближайшей записи периода временной зоны
     * @param UuidInterface $uuid
     * @param int $timestamp
     * @return mixed
     * @throws DataNotFoundException
     */
    public function getCorrectItem(UuidInterface $uuid, int $timestamp)
    {
        $timeZoneRepository = new SqlTimeZoneRepository($this->pdo);
        $timeZoneRepository->setCityId($uuid);

        foreach ($timeZoneRepository->getItems() as $item) {
            $zoneStart = new \DateTime($item['zone_start']);
            if ($zoneStart->getTimestamp() > $timestamp) {
                continue;
            }
            return $item;
        }

        throw new DataNotFoundException();
    }


}