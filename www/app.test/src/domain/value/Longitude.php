<?php

namespace app\domain\value;

use app\domain\exception\ValueException;

class Longitude
{
    const MIN_VALUE = -180;
    const MAX_VALUE = 180;

    private float $value;

    public function __construct(float $value)
    {
        $this->validateValue($value);
        $this->value = $value;
    }

    private function validateValue($value)
    {
        if($value > self::MAX_VALUE || $value < self::MIN_VALUE) {
            throw new ValueException('incorrect value');
        }
    }

    public function getValue()
    {
        return $this->value;
    }
}