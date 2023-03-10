<?php

namespace app\service;

use app\domain\aggregate\City;
use app\domain\exception\DataNotFoundException;
use app\domain\value\GmtOffset;
use app\domain\value\TimeZonePeriodLocal;
use app\service\interfaces\TimeZoneIntevalDataServiceInterface;
use DateTime;
use Ramsey\Uuid\Uuid;

class LocaltimeDataService
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
     * Получение локального времени в городе по переданному идентификатору города и метке времени по UTC+0.
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
            $timeZonePeriodLocal = new TimeZonePeriodLocal(
                $city,
                $data['zone_name'],
                $gmtOffset,
                $data['dst'],
            );

            //Какую дату проверяем
            $targetDatetime = (new DateTime())->setTimestamp($targetTimestamp);
            $timeZonePeriodLocal->setTargetDatetime($targetDatetime);
            if ($data['zone_end']) {
                $timeZonePeriodLocal->setZoneEnd(new DateTime($data['zone_end']));
            }
            $localDatetime = $timeZonePeriodLocal->getResulDatetime();

            $result['param_city_id'] = $uuid->toString();
            $result['param_time'] = $targetDatetime->format('Y-m-d H:i:s');
            $result['city_name'] = $data['city_name'];
            $result['zone_name'] = $data['zone_name'];
            $result['local_time'] = $localDatetime->format('Y-m-d H:i:s');
            $result['is_summer_time'] = $timeZonePeriodLocal->getDst();
            $result = ['success' => true, 'data' => $result];
        } catch (DataNotFoundException $e) {
            $result = ['success' => false, 'error' => 'data not found'];
        }
        return $result;
    }


}