<?php

namespace app\domain\value;

use app\domain\aggregate\City;
use app\domain\exception\ValueException;
use DateTime;
use DateTimeZone;

class TimeZonePeriod
{
    private City $city;
    private string $zoneName;
    private DateTime $zoneStart;
    private ?DateTime $zoneEnd;
    private GmtOffset $gmtOffset;
    private bool $dstInit;
    private bool $dst;
    private ?DateTime $localDatetime;
    private ?DateTime $utcDatetime = null;
    /**
     * Какую дату проверяем
     * @var DateTime
     */
    private DateTime $targetDatetime;

    public function __construct(City $city,string $zoneName, DateTime $zoneStart, GmtOffset $gmtOffset, bool $dstInit)
    {
        $this->city = $city;
        $this->zoneName = $zoneName;
        $this->zoneStart = $zoneStart;
        $this->gmtOffset = $gmtOffset;
        $this->dstInit = $dstInit;
        $this->zoneEnd = null;
        $this->localDatetime = null;
    }

    /**
     * Получение локального времени в городе по переданному идентификатору города и метке времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * @return void
     * @throws \app\domain\exception\ValueException
     */
    public function calcLocalDatetime()
    {
        if(!$this->targetDatetime) {
            throw new ValueException('set target datetime');
        }

        //Переданное в параметре время переводим в UTC-0
        $utcDatetime = $this->targetDatetime;
        $utcDatetime->setTimezone(new DateTimeZone('UTC'));

        $this->dst = $this->dstInit;
        //если переданная метка времени вышла за границу периода
        if($this->zoneEnd && $this->zoneEnd < $utcDatetime) {
            $this->dst = !$this->dst;
            $this->gmtOffset->applyDst($this->dst);
        }
        $this->localDatetime = $this->city->getLocalFromUtc0($utcDatetime, $this->gmtOffset);

    }

    public function calcUtcDatetime()
    {
        if(!$this->targetDatetime) {
            throw new ValueException('set target datetime');
        }

        //Переданное в параметре время переводим в локальную зону
        $localDatetime = $this->targetDatetime;
        $localDatetime->setTimezone(new DateTimeZone($this->zoneName));

        $this->dst = $this->dstInit;
        //если переданная метка времени вышла за границу периода
        if($this->zoneEnd && $this->zoneEnd < $localDatetime) {
            $this->dst = !$this->dst;
            $this->gmtOffset->applyDst($this->dst);
        }
        $this->utcDatetime = $this->city->getUtc0FromLocal($localDatetime, $this->gmtOffset);
    }

    /**
     * @return DateTime
     * @throws ValueException
     */
    public function getLocalDatetime()
    {
        if(!$this->localDatetime) {
            throw new ValueException('make calcLocalDatetime method');
        }
        return $this->localDatetime;
    }

    /**
     * @return DateTime
     * @throws ValueException
     */
    public function getUtcDatetime()
    {
        if(!$this->utcDatetime) {
            throw new ValueException('make calcUtcDatetime method');
        }
        return $this->utcDatetime;
    }



    /**
     * todo проверить что дата окончания больше даты начала
     * @param DateTime $zoneEnd
     * @return void
     */
    public function setTargetDatetime(DateTime $targetDatetime)
    {
        $this->targetDatetime = $targetDatetime;
    }


    /**
     * todo проверить что дата окончания больше даты начала
     * @param DateTime $zoneEnd
     * @return void
     */
    public function setZoneEnd(DateTime $zoneEnd)
    {
        $this->zoneEnd = $zoneEnd;
    }

    public function getDst()
    {
        return $this->dst;
    }
}