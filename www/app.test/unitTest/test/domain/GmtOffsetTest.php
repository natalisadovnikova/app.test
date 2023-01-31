<?php

namespace unitTests\test\domain;

use app\domain\exception\ValueException;

use app\domain\value\GmtOffset;
use PHPUnit\Framework\TestCase;

class GmtOffsetTest extends TestCase
{
    public function testValue()
    {
        $ob = new GmtOffset(1500);
        $this->assertEquals(1500, $ob->getOffset());
    }

    public function testExceededMaxNegativeOffsetException()
    {
        $this->expectException(ValueException::class);
        $ob = new GmtOffset(-45000);
    }

    public function testExceededMaxPositiveOffseException()
    {
        $this->expectException(ValueException::class);
        $ob = new GmtOffset(60000);
    }


}
