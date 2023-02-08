<?php

namespace app\repository;

use app\domain\exception\DbSaveProblemException;
use app\repository\interfaces\TimeZoneRepositoryInterface;
use Ramsey\Uuid\UuidInterface;
use PDO;


class SqlTimeZoneRepository implements TimeZoneRepositoryInterface
{
    private $pdo;
    private $items;
    private UuidInterface $cityUuid;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param UuidInterface $cityUuid
     * @return void
     */
    public function setCityId(UuidInterface $cityUuid)
    {
        $this->cityUuid = $cityUuid;
    }


    /**
     * Записи
     * @return array
     */
    public function getItems()
    {
        if(is_null($this->items)) {
            $this->loadItems();
        }
        return $this->items;
    }

    /**
     * @return void
     */
    private function loadItems()
    {
        $stmt = $this->pdo->prepare("SELECT  * FROM timezone_items WHERE `city_id` = :id ORDER BY zone_start DESC");
        $stmt->execute(['id' => $this->cityUuid->toString()]);
        $this->items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return UuidInterface
     */
    public function getCityId(): UuidInterface
    {
        return $this->cityUuid;
    }

    /**
     * todo сделать правильную проверку
     * нужно ли обновлять данные в таблицце
     * @return bool
     */
    public function needFreshData(): bool
    {
        return true;
    }

    /**
     * Сейчас по простому - удаляем и записываем свежие данные
     * todo переделать на проверку + добавление
     * @param $data
     * @return bool
     */
    public function refreshData($data): bool
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("DELETE FROM timezone_items WHERE city_id = :city_id");
            $stmt->execute(['city_id' => $this->cityUuid->toString()]);

            $stmt = $this->pdo->prepare(
                "INSERT INTO timezone_items (id, city_id, city_name, zone_name, dst, gmt_offset, zone_start, zone_end) 
    VALUES (:id, :city_id, :city_name, :zone_name, :dst, :gmt_offset, :zone_start, :zone_end)"
            );

            $res = $stmt->execute([
                'id' => \Ramsey\Uuid\Uuid::fromDateTime(new \DateTime())->toString(),
                'city_id' => $this->cityUuid->toString(),
                'city_name' => $data['cityName'],
                'zone_name' => $data['zoneName'],
                'dst' => (int)$data['dst'],
                'gmt_offset' => (int)$data['gmtOffset'],
                'zone_start' => date('Y-m-d H:i:s', abs($data['zoneStart'])),
                'zone_end' => $data['zoneEnd'] ? date('Y-m-d H:i:s', abs($data['zoneEnd'])) : null,
            ]);
            if (!$res) {
                throw new DbSaveProblemException(
                    'city_id = ' . $this->cityUuid->toString() . ' ' . var_export($stmt->errorInfo(), 1)
                );
            }
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            //todo залогировать ошибку
            throw new DbSaveProblemException($exception->getMessage());
        }
        $this->pdo->commit();
        return true;
    }
}