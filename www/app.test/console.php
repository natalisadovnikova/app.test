<?php

use app\service\UpdateDataService;

require_once __DIR__.'/vendor/autoload.php';
$config = require __DIR__ . '/config/console.php';
$pdo = new PDO('mysql:host=mysql;dbname='.$config['database'], $config['user'], $config['password']);

$updateDataService = new UpdateDataService($pdo);
$updateDataService->run();