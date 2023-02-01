<?php

namespace app\domain\value;

use app\domain\exception\ValueException;

class Longitude
{
    const MIN_VALUE = -180;
    const MAX_VALUE = 180;

    private float $value;

    /**
     * @param float $value
     * @throws ValueException
     */
    public function __construct(float $value)
    {
        $this->validateValue($value);
        $this->value = $value;
    }

    /**
     * @param $value
     * @return void
     * @throws ValueException
     */
    private function validateValue($value): void
    {
        if ($value > self::MAX_VALUE || $value < self::MIN_VALUE) {
            throw new ValueException('incorrect value');
        }
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }
}