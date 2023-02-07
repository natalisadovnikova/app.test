<?php

namespace app\service;

use app\repository\SqlCityRepository;
use PDO;

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
     * Ставит задачи в очередь
     * @return void
     * @throws \app\domain\exception\CityNotFoundException
     * @throws \app\domain\exception\ExternalDataProblemException
     */
    public function run()
    {
        $cityRepository = new SqlCityRepository($this->pdo);
        $allCitiesIds = $cityRepository->findAll();
        $rmqService = new RMQService('dev-ex');
        foreach ($allCitiesIds as $citiesId) {
            //задания на обновления данных ставим в очередь
            $rmqService->sendMessageToQueue($citiesId['id']);
        }
    }


}