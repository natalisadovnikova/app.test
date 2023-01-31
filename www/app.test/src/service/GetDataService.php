<?php
namespace app\service;


use app\domain\aggregate\City;
use app\domain\exception\DataNotFoundException;
use app\domain\exception\ExternalDataProblemException;
use app\domain\value\GmtOffset;
use app\repository\interfaces\CityRepositoryInterface;
use app\repository\interfaces\TimeZoneRepositoryInterface;
use app\repository\SqlTimeZoneRepository;
use app\service\interfaces\ExternalDataServiceInterface;
use Cassandra\Date;
use DateTime;
use DateTimeZone;
use PDO;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GetDataService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Получения локального времени в городе по переданному идентификатору города и метке времени по UTC+0.
     * @param string $uuid
     * @param int $timestamp
     * @return false|string
     * @throws \app\domain\exception\ValueException
     */
    public function getLocalTime(string $uuid, int $timestamp) {
        //todo если временная метка выходит за границы загруженных данных
        //todo доработать - считать gmt_offset меньше или больше на 1 для следующего периода
        //todo в зависимости от параметра  dst

        $result = [];
        try{
            $uuid = Uuid::fromString($uuid);
            $data = $this->getCorrectItem($uuid, $timestamp);
            $city = new City($uuid, $data['city_name']);

            //Переданное в параметре время
            $utcDatetime = new DateTime();
            $utcDatetime->setTimestamp($timestamp);
            $utcDatetime->setTimezone(new DateTimeZone('UTC'));

            // смещение в секундах
            $seconds = $data['gmt_offset'];
            $offset = new GmtOffset($seconds);
            $localDatetime = $city->getLocalFromUtc0($utcDatetime, $offset);

            $result['param_city_id'] = $uuid->toString();
            $result['param_time'] = $utcDatetime->format('Y-m-d H:i:s');
            $result['city_name'] = $data['city_name'];
            $result['zone_name'] = $data['zone_name'];
            $result['local_time'] = $localDatetime->format('Y-m-d H:i:s');
            $result['is_summer_time'] = $data['dst'];

        } catch (DataNotFoundException $e) {
            $result = ['success' => false, 'error' => 'data not found'];
        }

        return json_encode($result);
    }

    /**
     * Обратное преобразование из локального времени и идентификатора города в метку времени по UTC+0.
     * @param string $uuid
     * @param int $timestamp
     * @return false|string
     * @throws \app\domain\exception\ValueException
     */
    public function getUtcTime(string $uuid, int $timestamp) {
        //todo если временная метка выходит за границы загруженных данных
        //todo доработать - считать gmt_offset меньше или больше на 1 для следующего периода
        //todo в зависимости от параметра  dst

        $result = [];
        try{
            $uuid = Uuid::fromString($uuid);
            $data = $this->getCorrectItem($uuid, $timestamp);
            $city = new City($uuid, $data['city_name']);

            //Переданное в параметре время - локальное в стране
            $localDatetime = new DateTime();
            $localDatetime->setTimezone(new DateTimeZone($data['zone_name']));
            $localDatetime->setTimestamp($timestamp);

            // смещение в секундах
            $seconds = $data['gmt_offset'];
            $offset = new GmtOffset($seconds);
            $utc0Datetime = $city->getUtc0FromLocal($localDatetime, $offset);

            $result['param_city_id'] = $uuid->toString();
            $result['param_time'] = $localDatetime->format('Y-m-d H:i:s');
            $result['city_name'] = $data['city_name'];
            $result['zone_name'] = $data['zone_name'];
            $result['utc0_time'] = $utc0Datetime->format('Y-m-d H:i:s');
            $result['is_summer_time'] = $data['dst'];

        } catch (DataNotFoundException $e) {
            $result = ['success' => false, 'error' => 'data not found'];
        }

        return json_encode($result);
    }

    public function getCorrectItem(UuidInterface $uuid, int $timestamp)
    {
        $timeZoneRepository = new SqlTimeZoneRepository($this->pdo);
        $timeZoneRepository->setCityId($uuid);

        foreach ($timeZoneRepository->getItems() as $item) {

            $zoneStart = new \DateTime($item['zone_start']);
            $zoneEnd = new \DateTime($item['zone_end']);
            if($zoneStart->getTimestamp() > $timestamp ) {
                continue;
            }

            if($item['zone_end'] && $zoneEnd->getTimestamp() < $timestamp) {
                continue;
            }
            return $item;
        }

        throw new DataNotFoundException();

    }


}