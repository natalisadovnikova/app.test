<?php

namespace app\service;


use app\domain\aggregate\City;
use app\domain\exception\DataNotFoundException;
use app\domain\value\GmtOffset;
use app\domain\value\TimeZonePeriodUtc;
use app\service\interfaces\TimeZoneIntevalDataServiceInterface;
use DateTime;
use Ramsey\Uuid\Uuid;

class UtctimeDataService
{
    private $dataService;

    /**
     * @param TimeZoneIntevalDataServiceInterface $dataService
     */
    public function __construct(TimeZoneIntevalDataServiceInterface $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Обратное преобразование из локального времени и идентификатора города в метку времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * в зависимости от параметра  dst
     * @param string $uuid
     * @param int $timestamp
     * @return array
     * @throws \app\domain\exception\ValueException
     */
    public function getData(string $uuid, int $targetTimestamp): array
    {
        $result = [];
        try {
            $uuid = Uuid::fromString($uuid);
            $data = $this->dataService->getCorrectItem($uuid, $targetTimestamp);
            $city = new City($uuid, $data['city_name']);

            $gmtOffset = new GmtOffset($data['gmt_offset']);
            $timeZonePeriodUtc = new TimeZonePeriodUtc(
                $city,
                $data['zone_name'],
                $gmtOffset,
                $data['dst'],
            );

            $targetDatetime = (new DateTime())->setTimestamp($targetTimestamp);
            $timeZonePeriodUtc->setTargetDatetime($targetDatetime);
            if ($data['zone_end']) {
                $timeZonePeriodUtc->setZoneEnd(new DateTime($data['zone_end']));
            }

            $utc0Datetime = $timeZonePeriodUtc->getResulDatetime();

            $result['param_city_id'] = $uuid->toString();
            $result['param_time'] = ($targetDatetime)->format('Y-m-d H:i:s');
            $result['city_name'] = $data['city_name'];
            $result['zone_name'] = $data['zone_name'];
            $result['utc0_time'] = $utc0Datetime->format('Y-m-d H:i:s');
            $result['is_summer_time'] = $timeZonePeriodUtc->getDst();
            $result = ['success' => true, "data" => $result];
        } catch (DataNotFoundException $e) {
            $result = ['success' => false, 'error' => 'data not found'];
        }
        return $result;
    }

}