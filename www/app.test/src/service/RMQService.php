<?php

namespace app\service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RMQService
{
    private $queueName;
    private $channel;
    private $connection;

    public function __construct($queueName)
    {
        $this->queueName = $queueName;
        //todo настройки вынести в параметры
        $this->connection = new AMQPStreamConnection('dev-rabbitmq', 5672, 'rmq_test', 'rmq_test');
        $this->channel = $this->connection->channel();
    }

    public function sendMessageToQueue($message)
    {
        $this->channel->queue_declare($this->queueName, false, false, false, false);

        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, '', $this->queueName);
        echo 'sended '.$message.PHP_EOL;

    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
        echo 'closed '.PHP_EOL;
        // TODO: Implement __destruct() method.
    }


}