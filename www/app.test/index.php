<?php

use app\service\GetDataService;

require_once __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config/web.php';

$pdo = new PDO('mysql:host=mysql;dbname=' . $config['database'], $config['user'], $config['password']);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($pdo) {
    $r->addRoute('GET', '/localtime/{id}/{timestamp:\d+}', function ($vars) use ($pdo) {
        header('Content-Type: application/json; charset=utf-8');
        $getDataService = new GetDataService($pdo);
        echo $getDataService->getLocalTime($vars['id'], $vars['timestamp']);
    });
    $r->addRoute('GET', '/utctime/{id}/{timestamp:\d+}', function ($vars) use ($pdo) {
        header('Content-Type: application/json; charset=utf-8');
        $getDataService = new GetDataService($pdo);
        echo $getDataService->getUtcTime($vars['id'], $vars['timestamp']);
    });
});


$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        show_instruction();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $handler($vars);
        break;
}

function show_instruction()
{
    echo 'Для получения локального времени в городе по переданной метке UTC+0<br>';
    echo 'http://app.test/localtime/3ef2f49f-7543-431e-890d-fceae99c97d8/1675170876<br><br>';

    echo 'Для получения временной метки UTC+0 по локальному времени города<br>';
    echo 'http://app.test/utctime/3ef2f49f-7543-431e-890d-fceae99c97d8/1675170876<br><br>';
}

?>