<?php

namespace app\service\interfaces;

use Ramsey\Uuid\UuidInterface;

interface TimeZoneIntevalDataServiceInterface
{
    public function getCorrectItem(UuidInterface $uuid, int $timestamp);
}