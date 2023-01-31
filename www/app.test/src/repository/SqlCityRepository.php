<?php
namespace app\repository;

use app\domain\aggregate\City;
use app\domain\exception\CityNotFoundException;
use app\domain\value\Latitude;
use app\domain\value\Longitude;
use app\repository\interfaces\CityRepositoryInterface;
use Ramsey\Uuid\UuidInterface;
use PDO;


class SqlCityRepository implements CityRepositoryInterface
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(UuidInterface $uuid): City
    {
        $stmt = $this->pdo->prepare("SELECT  * FROM city WHERE `id` = :id");
        $stmt->execute(['id' => $uuid->toString()]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if($res === false) {
            throw new CityNotFoundException();
        }
        $city = new City($uuid, $res['name']);
        $city->setLatitude(new Latitude($res['latitude']));
        $city->setLongitude(new Longitude($res['longitude']));

        return $city;
    }

    public function findAll()
    {
        $stmt = $this->pdo->prepare("SELECT id FROM city");
        $stmt->execute();
        $resAll = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($resAll === false) {
            throw new CityNotFoundException();
        }

        return $resAll;
    }

}