<?php

use app\domain\exception\CityNotFoundException;
use app\domain\exception\DbSaveProblemException;
use app\domain\exception\ExternalDataProblemException;
use app\service\UpdateDataService;

require_once __DIR__.'/vendor/autoload.php';
$config = require __DIR__ . '/config/console.php';

$pdo = new PDO('mysql:host=mysql;dbname='.$config['database'], $config['user'], $config['password']);

$updateDataService = new UpdateDataService($pdo);
try{
    $updateDataService->run();
} catch (ExternalDataProblemException $exception) {
    var_dump('Ошибка сервиса данных: '. $exception->getMessage());
} catch (DbSaveProblemException $exception) {
    var_dump('Ошибка сохранения данных в базу: '. $exception->getMessage());
} catch (CityNotFoundException $exception) {
    var_dump('Ошибка данных в базе: '. $exception->getMessage());
}




//$sqlRep = new \app\repository\SqlCityRepository($pdo);
//$uuid = \Ramsey\Uuid\Uuid::fromString('0aa5711e-f664-4066-800a-286dfa3f3255');
//$city = $sqlRep->findById($uuid);
//
//var_dump($city);