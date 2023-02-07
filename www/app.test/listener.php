<?php

use app\service\RMQReceiver;

require_once __DIR__.'/vendor/autoload.php';
$config = require __DIR__ . '/config/console.php';
$pdo = new PDO('mysql:host=mysql;dbname='.$config['database'], $config['user'], $config['password']);

$receiver = new RMQReceiver($pdo, 'dev-ex' );
$receiver->listen();