<?php

namespace app\controller;


use app\service\TimezoneDataService;
use app\service\LocaltimeDataService;
use PDO;


class LocaltimeController
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index($vars)
    {
        header('Content-Type: application/json; charset=utf-8');
        $dataService = new TimezoneDataService($this->pdo);
        $localtimeDataService = new LocaltimeDataService($dataService);
        $data = $localtimeDataService->getData($vars['id'], $vars['timestamp']);
        echo json_encode($data);
    }
}