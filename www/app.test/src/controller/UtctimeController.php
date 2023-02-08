<?php

namespace app\controller;

use app\service\TimezoneDataService;
use app\service\UtctimeDataService;
use PDO;


class UtctimeController
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
        $utcDataService = new UtctimeDataService($dataService);
        $data = $utcDataService->getData($vars['id'], $vars['timestamp']);
        echo json_encode($data);
    }
}