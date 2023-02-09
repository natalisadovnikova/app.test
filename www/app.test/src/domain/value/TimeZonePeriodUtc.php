<?php

namespace app\domain\value;

use app\domain\exception\ValueException;
use DateTimeZone;

class TimeZonePeriodUtc extends TimeZonePeriodAbstract
{
    /**
     * Обратное преобразование из локального времени в метку времени по UTC+0.
     * если временная метка выходит за границы загруженных данных
     * считать gmt_offset меньше или больше на 1 час для следующего периода
     * в зависимости от параметра  dst
     * @return void
     * @throws ValueException
     */
    public function calcResultDatetime(): void
    {
        if (!$this->targetDatetime) {
            throw new ValueException('set target datetime');
        }

        //Переданное в параметре время переводим в локальную зону
        $localDatetime = $this->targetDatetime;
        $localDatetime->setTimezone(new DateTimeZone($this->zoneName));

        //Введение летнего или змнего времени
        $this->applyDst($localDatetime);

        //время в UTC-0
        $this->resultDatetime = $this->city->getUtc0FromLocal($localDatetime, $this->gmtOffset);
    }
}