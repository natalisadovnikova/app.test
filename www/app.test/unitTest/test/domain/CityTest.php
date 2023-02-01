<?php

namespace unitTests\test\domain;

use app\domain\aggregate\City;
use app\domain\value\GmtOffset;
use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CityTest extends TestCase
{
    public function testName()
    {
        $uuid = Uuid::fromInteger(time());
        $ob = new City($uuid, 'Moscow');
        $this->assertEquals('Moscow', $ob->getName());
    }

    public function testId()
    {
        $uuid = Uuid::fromInteger(time());
        $ob = new City($uuid, 'Moscow');
        $this->assertEquals($uuid, $ob->getId());
    }

    public function testLocalFromUtc0()
    {
        $uuid = Uuid::fromInteger(time());
        $ob = new City($uuid, 'Moscow');

        $utcDatetime = new DateTime();
        $utcDatetime->setTimezone(new DateTimeZone('UTC'));

        //положительное смещение
        $seconds = 3 * 60 * 60;
        $offset = new GmtOffset($seconds);
        $localDatetime = $ob->getLocalFromUtc0($utcDatetime, $offset);
        //нужный тип
        $this->assertInstanceOf("DateTime", $localDatetime);
        //Разница в секундах
        $diff = $utcDatetime->diff($localDatetime);
        $secondsDiff = $diff->h * 60 * 60 + $diff->i * 60 + $diff->s;

//        var_dump($utcDatetime->format('Y-m-d H:i:s'));
//        var_dump($localDatetime->format('Y-m-d H:i:s'));

        $this->assertEquals($seconds, $secondsDiff);
        $this->assertEquals($diff->invert, 0);

        //отрицательное смещение
        $seconds = -3 * 60 * 60;
        $offset = new GmtOffset($seconds);
        $localDatetime = $ob->getLocalFromUtc0($utcDatetime, $offset);
        //нужный тип
        $this->assertInstanceOf("DateTime", $localDatetime);
        //Разница в секундах
        $diff = $utcDatetime->diff($localDatetime);

        $secondsDiff = $diff->h * 60 * 60 + $diff->i * 60 + $diff->s;

//        var_dump($diff->invert);
//        var_dump($utcDatetime->format('Y-m-d H:i:s'));
//        var_dump($localDatetime->format('Y-m-d H:i:s'));
//        die;
        $this->assertEquals(abs($seconds), $secondsDiff);
        $this->assertEquals($diff->invert, 1);
    }

    public function testUtc0FromLocal()
    {
        $uuid = Uuid::fromInteger(time());
        $ob = new City($uuid, 'Moscow');

        $localDatetime = new DateTime();
        $localDatetime->setTimezone(new DateTimeZone('Europe/Moscow'));

        //положительное смещение
        $seconds = 3 * 60 * 60;
        $offset = new GmtOffset($seconds);
        $utc0Datetime = $ob->getUtc0FromLocal($localDatetime, $offset);
        //нужный тип
        $this->assertInstanceOf("DateTime", $utc0Datetime);

        //Разница в секундах
        $diff = $localDatetime->diff($utc0Datetime);
        $secondsDiff = $diff->h * 60 * 60 + $diff->i * 60 + $diff->s;

//        var_dump($localDatetime->format('Y-m-d H:i:s'));
//        var_dump($utc0Datetime->format('Y-m-d H:i:s'));

        $this->assertEquals($seconds, $secondsDiff);
        $this->assertEquals($diff->invert, 1);

        //отрицательное смещение
        $seconds = -3 * 60 * 60;
        $offset = new GmtOffset($seconds);
        $localDatetime->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $utc0Datetime = $ob->getUtc0FromLocal($localDatetime, $offset);

        //нужный тип
        $this->assertInstanceOf("DateTime", $utc0Datetime);
        //Разница в секундах
        $diff = $localDatetime->diff($utc0Datetime);
        $secondsDiff = $diff->h * 60 * 60 + $diff->i * 60 + $diff->s;

//        var_dump($diff->invert);
//        var_dump($localDatetime->format('Y-m-d H:i:s'));
//        var_dump($utc0Datetime->format('Y-m-d H:i:s'));
//        die;
        $this->assertEquals(abs($seconds), $secondsDiff);
        $this->assertEquals($diff->invert, 0);
    }


}
