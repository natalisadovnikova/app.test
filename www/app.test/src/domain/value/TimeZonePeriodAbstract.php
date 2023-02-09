<?php

namespace app\domain\value;

use app\domain\aggregate\City;
use app\domain\exception\ValueException;
use DateTime;

abstract class TimeZonePeriodAbstract
{
    protected City $city;
    protected string $zoneName;
    protected ?DateTime $zoneEnd = null;
    protected GmtOffset $gmtOffset;
    protected bool $dstInit;
    protected bool $dst;
    protected ?DateTime $resultDatetime = null;

    /**
     * Какую дату проверяем
     * @var DateTime
     */
    protected DateTime $targetDatetime;

    /**
     * @param City $city
     * @param string $zoneName
     * @param DateTime $zoneStart
     * @param GmtOffset $gmtOffset
     * @param bool $dstInit
     */
    public function __construct(City $city, string $zoneName, GmtOffset $gmtOffset, bool $dstInit)
    {
        $this->city = $city;
        $this->zoneName = $zoneName;
        $this->gmtOffset = $gmtOffset;
        $this->dstInit = $dstInit;
    }

    abstract public function calcResultDatetime(): void;

    /**
     * Проверить период на смену зимнего/летнего времени и применить смещение, если необходимо
     * @param DateTime $dateTime
     * @return void
     */
    protected function applyDst(DateTime $dateTime)
    {
        $this->dst = $this->dstInit;
        //если переданная метка времени вышла за границу периода
        if ($this->zoneEnd && $this->zoneEnd < $dateTime) {
            $this->dst = !$this->dst;
            $this->gmtOffset->applyDst($this->dst);
        }
    }

    /**
     * @return DateTime
     * @throws ValueException
     */
    public function getResulDatetime(): DateTime
    {
        if (is_null($this->resultDatetime)) {
            $this->calcResultDatetime();
        }
        return $this->resultDatetime;
    }

    /**
     * Дата, по которой ищем
     * @param DateTime $targetDatetime
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
    public function setZoneEnd(DateTime $zoneEnd): void
    {
        $this->zoneEnd = $zoneEnd;
    }

    /**
     * Получить значение, летнее время или нет
     * @return bool
     * @throws ValueException
     */
    public function getDst(): bool
    {
        if(is_null($this->dst))
        {
            $this->calcResultDatetime();
        }
        return $this->dst;
    }
}