<?php

namespace unitTests\test\domain;

use app\domain\aggregate\City;
use app\domain\value\GmtOffset;
use app\domain\value\TimeZonePeriodUtc;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UtcTimeTest extends TestCase
{
    public function testCorrectUtcTime()
    {
        $data = [
            "city_id" => "3ef2f49f-7543-431e-890d-fceae99c97d8",
            "city_name" => "Craig Municipal Airport",
            "zone_name" => "America/New_York",
            "dst" => 0,
            "gmt_offset" => "-18000",
            "zone_start" => "2022-11-06 09:00:00",
            "zone_end" => "2023-03-12 10:00:00"
        ];


        $city = new City(Uuid::fromString($data["city_id"]), $data['city_name']);
        $gmtOffset = new GmtOffset($data['gmt_offset']);
        $timeZonePeriodUtc = new TimeZonePeriodUtc(
            $city,
            $data['zone_name'],
            $gmtOffset,
            $data['dst'],
        );

        //Какую дату проверяем
        $targetTimestamp = 1678621920;
        $timeZonePeriodUtc->setTargetDatetime((new DateTime())->setTimestamp($targetTimestamp));
        if ($data['zone_end']) {
            $timeZonePeriodUtc->setZoneEnd(new DateTime($data['zone_end']));
        }

        $expectedLocalTime = "2023-03-12 11:52:00";
        $expectedDst = true;
        $utcDatetime = $timeZonePeriodUtc->getResulDatetime();

        $this->assertEquals($expectedLocalTime, $utcDatetime->format("Y-m-d H:i:s"));
        $this->assertEquals($expectedDst, $timeZonePeriodUtc->getDst());
    }

    public function testCorrectUtcTimeWithoutEnd()
    {
        $data = [
            "city_id" => "746bdf1d-d154-46cd-b104-9415fcc39e35",
            "city_name" => "Midrand",
            "zone_name" => "Africa/Johannesburg",
            "dst" => 0,
            "gmt_offset" => "7200",
            "zone_start" => "1995-10-16 04:00:00",
            "zone_end" => null
        ];


        $city = new City(Uuid::fromString($data["city_id"]), $data['city_name']);
        $gmtOffset = new GmtOffset($data['gmt_offset']);
        $timeZonePeriodUtc = new TimeZonePeriodUtc(
            $city,
            $data['zone_name'],
            $gmtOffset,
            $data['dst'],
        );

        //Какую дату проверяем
        $targetTimestamp = 1678621920;
        $timeZonePeriodUtc->setTargetDatetime((new DateTime())->setTimestamp($targetTimestamp));
        if ($data['zone_end']) {
            $timeZonePeriodUtc->setZoneEnd(new DateTime($data['zone_end']));
        }

        $expectedLocalTime = "2023-03-12 11:52:00";
        $expectedDst = false;
        $utcDatetime = $timeZonePeriodUtc->getResulDatetime();

        $this->assertEquals($expectedLocalTime, $utcDatetime->format("Y-m-d H:i:s"));
        $this->assertEquals($expectedDst, $timeZonePeriodUtc->getDst());
    }
}
