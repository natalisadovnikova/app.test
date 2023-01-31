<?php

namespace unitTests\test\domain;

use app\domain\exception\ValueException;
use app\domain\value\Latitude;
use PHPUnit\Framework\TestCase;

class LatitudeTest extends TestCase
{
    public function testValue()
    {
        $ob = new Latitude(20);
        $this->assertEquals(20, $ob->getValue());
    }

    public function testBigValueException()
    {
        $this->expectException(ValueException::class);
        $ob = new Latitude(100);
    }

    public function testSmallValueException()
    {
        $this->expectException(ValueException::class);
        $ob = new Latitude(-100);
    }
}
