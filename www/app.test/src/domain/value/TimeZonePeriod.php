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
     * Вычесление локального времени в городе по метке времени по UTC+0.
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
        //Введение летнего или змнего времени
        $this->applyDst($utcDatetime);

        $this->localDatetime = $this->city->getLocalFromUtc0($utcDatetime, $this->gmtOffset);
    }

    //Проверить период на смену зимнего/летнего времени и применить смещение, если необходимо
    private function applyDst(DateTime $dateTime)
    {
        $this->dst = $this->dstInit;
        //если переданная метка времени вышла за границу периода
        if ($this->zoneEnd && $this->zoneEnd < $dateTime) {
            $this->dst = !$this->dst;
            $this->gmtOffset->applyDst($this->dst);
        }
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

        //Введение летнего или змнего времени
        $this->applyDst($localDatetime);

        $this->utcDatetime = $this->city->getUtc0FromLocal($localDatetime, $this->gmtOffset);
    }

    /**
     * @return DateTime
     * @throws ValueException
     */
    public function getLocalDatetime(): DateTime
    {
        if (is_null($this->localDatetime)) {
            $this->calcLocalDatetime();
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
            $this->calcUtcDatetime();
        }
        return $this->utcDatetime;
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
            $this->calcLocalDatetime();
        }
        return $this->dst;
    }
}