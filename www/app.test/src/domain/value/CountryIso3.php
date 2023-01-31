<?php

namespace app\domain\value;

use app\domain\exception\ValueException;

class CountryIso3
{
    const LENGTH = 3;

    private string $value;

    public function __construct(string $value)
    {
        $this->validateValue($value);
        $this->value = mb_strtoupper($value);
    }

    private function validateValue($value)
    {
        if(strlen($value) != self::LENGTH) {
            throw new ValueException('incorrect length');
        }
        if(!ctype_alpha($value)) {
            throw new ValueException('incorrect value');
        }
    }

    public function getValue()
    {
        return $this->value;
    }
}