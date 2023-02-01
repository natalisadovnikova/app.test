<?php

namespace app\domain\aggregate;

use app\domain\value\CountryIso3;
use app\domain\value\GmtOffset;
use app\domain\value\Latitude;
use app\domain\value\Longitude;
use DateTime;
use Ramsey\Uuid\UuidInterface;

class City
{
    private UuidInterface $id;
    private string $name;
    private Latitude $latitude;
    private Longitude $longitude;
    private CountryIso3 $countryIso3;

    /**
     * @param UuidInterface $id
     * @param string $name
     */
    public function __construct(UuidInterface $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Получение локального времени по переданному времени UTC-0
     * Со сдвигом по времени на $offset секунд
     * @param DateTime $utc0Datetime
     * @param GmtOffset $offset
     * @return DateTime
     */
    public function getLocalFromUtc0(DateTime $utc0Datetime, GmtOffset $offset): DateTime
    {
        $result = clone($utc0Datetime);
        $result->modify(sprintf("%s seconds", $offset->getOffset()));
        return $result;
    }

    /**
     * Получение времени UTC-0 по переданному локальному времени
     * Со сдвигом по времени на $offset секунд
     * @param DateTime $localDatetime
     * @param GmtOffset $offset
     * @return DateTime
     */
    public function getUtc0FromLocal(DateTime $localDatetime, GmtOffset $offset): DateTime
    {
        $result = clone($localDatetime);
        $result->modify(sprintf("-%s seconds", $offset->getOffset()));
        return $result;
    }

    /**
     * @param Latitude $latitude
     * @return void
     */
    public function setLatitude(Latitude $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return Latitude
     */
    public function getLatitude(): Latitude
    {
        return $this->latitude;
    }


    /**
     * @param Longitude $longitude
     * @return void
     */
    public function setLongitude(Longitude $longitude): void
    {
        $this->longitude = $longitude;
    }


    /**
     * @return Longitude
     */
    public function getLongitude(): Longitude
    {
        return $this->longitude;
    }

    /**
     * @param CountryIso3 $countryIso3
     * @return void
     */
    public function setCountryIso3(CountryIso3 $countryIso3): void
    {
        $this->countryIso3 = $countryIso3;
    }

    /**
     * @return CountryIso3
     */
    public function getCountryIso3(): CountryIso3
    {
        return $this->countryIso3;
    }
}