<?php

require_once __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config/web.php';

$pdo = new PDO('mysql:host=mysql;dbname=' . $config['database'], $config['user'], $config['password']);


$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($pdo) {
    $r->addRoute('GET', '/localtime/{id}/{timestamp:\d+}', 'LocaltimeController/index');
    $r->addRoute('GET', '/utctime/{id}/{timestamp:\d+}', 'UtctimeController/index');
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
        $class = __NAMESPACE__ . '\app\controller\SiteController';
        call_user_func_array(array(new $class, 'index'), []);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode("/", $handler, 2);
        $class = __NAMESPACE__ . '\app\controller\\' . $class;
        $c = new $class($pdo);
        call_user_func_array(array($c, $method), [$vars]);
        break;
}
?>