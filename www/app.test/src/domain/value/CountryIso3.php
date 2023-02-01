<?php

namespace app\domain\value;

use app\domain\exception\ValueException;

class CountryIso3
{
    /**
     * длина строки
     */
    const LENGTH = 3;

    private string $value;

    /**
     * @param string $value
     * @throws ValueException
     */
    public function __construct(string $value)
    {
        $this->validateValue($value);
        $this->value = mb_strtoupper($value);
    }

    /**
     * @param $value
     * @return void
     * @throws ValueException
     */
    private function validateValue($value): void
    {
        if (strlen($value) != self::LENGTH) {
            throw new ValueException('incorrect length');
        }
        if (!ctype_alpha($value)) {
            throw new ValueException('incorrect value');
        }
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}