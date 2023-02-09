<?php

namespace app\domain\value;

use app\domain\exception\ValueException;
use DateTimeZone;

class TimeZonePeriodLocal extends TimeZonePeriodAbstract
{
    /**
     * Вычесление локального времени в городе по метке времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * @return void
     * @throws \app\domain\exception\ValueException
     */
    public function calcResultDatetime(): void
    {
        if (!$this->targetDatetime) {
            throw new ValueException('set target datetime');
        }

        //Переданное в параметре время переводим в UTC-0
        $utcDatetime = $this->targetDatetime;
        $utcDatetime->setTimezone(new DateTimeZone('UTC'));
        //Введение летнего или змнего времени
        $this->applyDst($utcDatetime);

        $this->resultDatetime = $this->city->getLocalFromUtc0($utcDatetime, $this->gmtOffset);
    }
}