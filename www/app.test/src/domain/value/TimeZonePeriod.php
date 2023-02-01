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
    private ?DateTime $zoneEnd = null;
    private GmtOffset $gmtOffset;
    private bool $dstInit;
    private bool $dst;
    private ?DateTime $localDatetime = null;
    private ?DateTime $utcDatetime = null;

    /**
     * Какую дату проверяем
     * @var DateTime
     */
    private DateTime $targetDatetime;

    /**
     * @param City $city
     * @param string $zoneName
     * @param DateTime $zoneStart
     * @param GmtOffset $gmtOffset
     * @param bool $dstInit
     */
    public function __construct(City $city, string $zoneName, DateTime $zoneStart, GmtOffset $gmtOffset, bool $dstInit)
    {
        $this->city = $city;
        $this->zoneName = $zoneName;
        $this->zoneStart = $zoneStart;
        $this->gmtOffset = $gmtOffset;
        $this->dstInit = $dstInit;
    }

    /**
     * Получение локального времени в городе по метке времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * @return void
     * @throws \app\domain\exception\ValueException
     */
    public function calcLocalDatetime(): void
    {
        if (!$this->targetDatetime) {
            throw new ValueException('set target datetime');
        }

        //Переданное в параметре время переводим в UTC-0
        $utcDatetime = $this->targetDatetime;
        $utcDatetime->setTimezone(new DateTimeZone('UTC'));

        $this->dst = $this->dstInit;
        //если переданная метка времени вышла за границу периода
        if ($this->zoneEnd && $this->zoneEnd < $utcDatetime) {
            $this->dst = !$this->dst;
            $this->gmtOffset->applyDst($this->dst);
        }
        $this->localDatetime = $this->city->getLocalFromUtc0($utcDatetime, $this->gmtOffset);
    }

    /**
     * Обратное преобразование из локального времени в метку времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * в зависимости от параметра  dst
     * @return void
     * @throws ValueException
     */
    public function calcUtcDatetime(): void
    {
        if (!$this->targetDatetime) {
            throw new ValueException('set target datetime');
        }

        //Переданное в параметре время переводим в локальную зону
        $localDatetime = $this->targetDatetime;
        $localDatetime->setTimezone(new DateTimeZone($this->zoneName));

        $this->dst = $this->dstInit;
        //если переданная метка времени вышла за границу периода
        if ($this->zoneEnd && $this->zoneEnd < $localDatetime) {
            $this->dst = !$this->dst;
            $this->gmtOffset->applyDst($this->dst);
        }
        $this->utcDatetime = $this->city->getUtc0FromLocal($localDatetime, $this->gmtOffset);
    }

    /**
     * @return DateTime
     * @throws ValueException
     */
    public function getLocalDatetime(): DateTime
    {
        if (!$this->localDatetime) {
            throw new ValueException('make calcLocalDatetime method');
        }
        return $this->localDatetime;
    }

    /**
     * @return DateTime
     * @throws ValueException
     */
    public function getUtcDatetime(): DateTime
    {
        if (!$this->utcDatetime) {
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
    public function setZoneEnd(DateTime $zoneEnd): void
    {
        $this->zoneEnd = $zoneEnd;
    }

    public function getDst(): bool
    {
        return $this->dst;
    }
}