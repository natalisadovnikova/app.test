<?php

namespace unitTests\test\domain;

use app\domain\exception\ValueException;
use app\domain\value\Longitude;
use PHPUnit\Framework\TestCase;

class LongitudeTest extends TestCase
{
    public function testValue()
    {
        $ob = new Longitude(20);
        $this->assertEquals(20, $ob->getValue());
    }
    public function testBigValueException()
    {
        $this->expectException(ValueException::class);
        $ob = new Longitude(200);
    }

    public function testSmallValueException()
    {
        $this->expectException(ValueException::class);
        $ob = new Longitude(-200);
    }
}
