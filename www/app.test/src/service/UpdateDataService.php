<?php

namespace app\service;

use app\repository\SqlCityRepository;
use app\repository\SqlTimeZoneRepository;
use PDO;
use Ramsey\Uuid\Uuid;

class UpdateDataService
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
     * Запуск процесса обновления данных
     * @return void
     * @throws \app\domain\exception\CityNotFoundException
     * @throws \app\domain\exception\ExternalDataProblemException
     */
    public function run()
    {
        $dataService = new ExternalDataService();
        $cityRepository = new SqlCityRepository($this->pdo);
        $timeZoneRepository = new SqlTimeZoneRepository($this->pdo);
        $timeZoneService = new UpdateTimeZoneService($cityRepository, $timeZoneRepository);

        $allCitiesIds = $cityRepository->findAll();
        foreach ($allCitiesIds as $citiesId) {
            $uuid = Uuid::fromString($citiesId['id']);
            $timeZoneService->updateData($uuid, $dataService);
            //todo задания на обновления данных надо ставить в очередь
            sleep(2);
        }
    }


}