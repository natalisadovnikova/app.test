<?php

namespace app\domain\value;

use app\domain\exception\ValueException;

/**
 * Смещение во времени в часах может быть таким:
 * https://ru.wikipedia.org/wiki/Список_часовых_поясов_по_странам
 * −12−11−10−9:30−9−8:30−8−7−6−5−4:30−4−3:30−3−2:30−2−1−0:25:21
 * UTC (0)
 * +0:20+0:30+1+2+2:30+3+3:30+4+4:30+5+5:30+5:40+5:45+6+6:30+7+7:20+7:30+8+8:30
 * +8:45+9+9:30+10+10:30+11+11:30+12+12:45+13+13:45+14
 * Есть не полные часы => будем работать в секундах
 */
class GmtOffset
{
    /**
     * Максимальное отрицательное смещение:  -12 часов
     */
    const MAX_NEGATIVE_OFFSET = -43200;

    /**
     * Максимальное положительное смещение:  14 часов
     */
    const MAX_POSITIVE_OFFSET = 50400;

    /**
     * Числовая величина смещения
     * @var int
     */
    private int $offset;

    /**
     * @param int $offset
     * @throws ValueException
     */
    public function __construct(int $offset)
    {
        $this->validateOffset($offset);
        $this->offset = $offset;
    }

    /**
     * Применить смещение на 1 час в случае введения летнего времени, или при возвращении на зимнее время
     * @return void
     */
    public function applyDst(bool $isDst = false): void
    {
        if ($isDst) {
            //при вводе летнего времени часы переводят на час вперед, gmt_offset = gmt_offset + 1*60*60
            $this->offset += 3600;
        } else {
            //при возвращении на зимнее время - на час назад
            $this->offset -= 3600;
        }
    }

    /**
     * @param $offset
     * @return void
     * @throws ValueException
     */
    private function validateOffset($offset): void
    {
        if (!is_int($offset)) {
            throw new ValueException('incorrect offset');
        }

        if ($offset > self::MAX_POSITIVE_OFFSET || $offset < self::MAX_NEGATIVE_OFFSET) {
            throw new ValueException('incorrect offset');
        }
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}