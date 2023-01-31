<?php
namespace app\service\interfaces;

use Ramsey\Uuid\UuidInterface;

interface UpdateTimeZoneServiceInterface
{
    public function updateData(UuidInterface $uuid, ExternalDataServiceInterface $dataService);
}