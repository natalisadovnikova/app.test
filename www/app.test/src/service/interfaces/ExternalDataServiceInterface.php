<?php
namespace app\service\interfaces;

use app\domain\value\Latitude;
use app\domain\value\Longitude;

interface ExternalDataServiceInterface
{
    public function getData(Longitude $lng, Latitude $ltd);
}