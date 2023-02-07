<?php

namespace app\service;

use app\domain\exception\CityNotFoundException;
use app\domain\exception\DbSaveProblemException;
use app\domain\exception\ExternalDataProblemException;
use app\repository\SqlCityRepository;
use app\repository\SqlTimeZoneRepository;
use PDO;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class RMQReceiver
{
    private $queueName;
    private $channel;
    private $connection;
    private $pdo;


    public function __construct(PDO $pdo, string $queueName)
    {
        $this->queueName = $queueName;
        $this->pdo = $pdo;
        $this->connection = new AMQPConnection('dev-rabbitmq', 5672, 'rmq_test', 'rmq_test');
        $this->channel = $this->connection->channel();
    }

    public function listen()
    {
        $this->channel->queue_declare($this->queueName, false, false, false, false);

        $this->channel->basic_consume(
            $this->queueName,                    #очередь
            '',                             #тег получателя - Идентификатор получателя, валидный в пределах текущего канала. Просто строка
            false,                          #не локальный - TRUE: сервер не будет отправлять сообщения соединениям, которые сам опубликовал
            true,                           #без подтверждения - отправлять соответствующее подтверждение обработчику, как только задача будет выполнена
            false,                          #эксклюзивная - к очереди можно получить доступ только в рамках текущего соединения
            false,                          #не ждать - TRUE: сервер не будет отвечать методу. Клиент не должен ждать ответа
            array($this, 'processUpdate')    #функция обратного вызова - метод, который будет принимать сообщение
            );

        while(count($this->channel->callbacks)) {

            $this->channel->wait();

        }
    }

    public function processUpdate(AMQPMessage $msg)
    {
        $cityRepository = new SqlCityRepository($this->pdo);
        $externalDataService = new ExternalDataService();
        $timeZoneRepository = new SqlTimeZoneRepository($this->pdo);
        $timeZoneService = new UpdateTimeZoneService($cityRepository, $timeZoneRepository);

        try{
            $uuid = Uuid::fromString($msg->body);
            $timeZoneService->updateData($uuid, $externalDataService);
        } catch (ExternalDataProblemException $exception) {
            var_dump('Ошибка сервиса данных: '. $exception->getMessage());
        } catch (DbSaveProblemException $exception) {
            var_dump('Ошибка сохранения данных в базу: '. $exception->getMessage());
        } catch (CityNotFoundException $exception) {
            var_dump('Ошибка данных в базе: '. $exception->getMessage());
        } catch (\Throwable $exception) {
            var_dump('Неизвестная ошибка: '. $exception->getMessage());
        }

        echo date('Y-m-d H:i:s').' message from queue '. $msg->body. PHP_EOL;
        sleep(2);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
        echo 'closed '.PHP_EOL;
        // TODO: Implement __destruct() method.
    }


}