<?php

namespace unitTests\test\domain;

use app\domain\exception\ValueException;

use app\domain\value\CountryIso3;
use PHPUnit\Framework\TestCase;

class CountryIsoTest extends TestCase
{
    public function testValue()
    {
        $ob = new CountryIso3('usa');
        $this->assertEquals('USA', $ob->getValue());
    }

    public function testShortException()
    {
        $this->expectException(ValueException::class);
        $ob = new CountryIso3('Dt');
    }

    public function testLargeException()
    {
        $this->expectException(ValueException::class);
        $ob = new CountryIso3('USAA');
    }

    public function testNoAlphaException()
    {
        $this->expectException(ValueException::class);
        $ob = new CountryIso3('US1');
    }
}
