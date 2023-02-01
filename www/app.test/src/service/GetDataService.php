<?php

namespace app\service;


use app\domain\aggregate\City;
use app\domain\exception\DataNotFoundException;
use app\domain\value\GmtOffset;
use app\domain\value\TimeZonePeriod;
use app\repository\SqlTimeZoneRepository;
use DateTime;
use PDO;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GetDataService
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
     * Обратное преобразование из локального времени и идентификатора города в метку времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * в зависимости от параметра  dst
     * @param string $uuid
     * @param int $timestamp
     * @return false|string
     * @throws \app\domain\exception\ValueException
     */
    public function getUtcTime(string $uuid, int $targetTimestamp): string
    {
        $result = [];
        try {
            $uuid = Uuid::fromString($uuid);
            $data = $this->getCorrectItem($uuid, $targetTimestamp);
            $city = new City($uuid, $data['city_name']);

            $gmtOffset = new GmtOffset($data['gmt_offset']);
            $timeZonePeriod = new TimeZonePeriod(
                $city,
                $data['zone_name'],
                new DateTime($data['zone_start']),
                $gmtOffset,
                $data['dst'],
            );

            $targetDatetime = (new DateTime())->setTimestamp($targetTimestamp);
            $timeZonePeriod->setTargetDatetime($targetDatetime);
            if ($data['zone_end']) {
                $timeZonePeriod->setZoneEnd(new DateTime($data['zone_end']));
            }
            $timeZonePeriod->calcUtcDatetime();
            $utc0Datetime = $timeZonePeriod->getUtcDatetime();
            $dst = $timeZonePeriod->getDst();

            $result['param_city_id'] = $uuid->toString();
            $result['param_time'] = ($targetDatetime)->format('Y-m-d H:i:s');
            $result['city_name'] = $data['city_name'];
            $result['zone_name'] = $data['zone_name'];
            $result['utc0_time'] = $utc0Datetime->format('Y-m-d H:i:s');
            $result['is_summer_time'] = $dst;
        } catch (DataNotFoundException $e) {
            $result = ['success' => false, 'error' => 'data not found'];
        }
        return json_encode($result);
    }

    /**
     * Получение локального времени в городе по переданному идентификатору города и метке времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * в зависимости от параметра  dst
     * @param string $uuid
     * @param int $timestamp
     * @return false|string
     * @throws \app\domain\exception\ValueException
     */
    public function getLocalTime(string $uuid, int $targetTimestamp): string
    {
        $result = [];
        try {
            $uuid = Uuid::fromString($uuid);
            $data = $this->getCorrectItem($uuid, $targetTimestamp);
            $city = new City($uuid, $data['city_name']);

            $gmtOffset = new GmtOffset($data['gmt_offset']);
            $timeZonePeriod = new TimeZonePeriod(
                $city,
                $data['zone_name'],
                new DateTime($data['zone_start']),
                $gmtOffset,
                $data['dst'],
            );

            //Какую дату проверяем
            $targetDatetime = (new DateTime())->setTimestamp($targetTimestamp);
            $timeZonePeriod->setTargetDatetime($targetDatetime);
            if ($data['zone_end']) {
                $timeZonePeriod->setZoneEnd(new DateTime($data['zone_end']));
            }
            $timeZonePeriod->calcLocalDatetime();
            $localDatetime = $timeZonePeriod->getLocalDatetime();
            $dst = $timeZonePeriod->getDst();

            $result['param_city_id'] = $uuid->toString();
            $result['param_time'] = $targetDatetime->format('Y-m-d H:i:s');
            $result['city_name'] = $data['city_name'];
            $result['zone_name'] = $data['zone_name'];
            $result['local_time'] = $localDatetime->format('Y-m-d H:i:s');
            $result['is_summer_time'] = $dst;
        } catch (DataNotFoundException $e) {
            $result = ['success' => false, 'error' => 'data not found'];
        }
        return json_encode($result);
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