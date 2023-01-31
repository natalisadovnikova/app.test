<?php
namespace app\service;


use app\domain\exception\ExternalDataProblemException;
use app\repository\interfaces\CityRepositoryInterface;
use app\repository\interfaces\TimeZoneRepositoryInterface;
use app\service\interfaces\ExternalDataServiceInterface;
use Ramsey\Uuid\UuidInterface;

class UpdateTimeZoneService implements \app\service\interfaces\UpdateTimeZoneServiceInterface
{
    private $cityRepository;
    private $timeZoneRepository;

    public function __construct(CityRepositoryInterface $cityRepository,TimeZoneRepositoryInterface $timeZoneRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->timeZoneRepository = $timeZoneRepository;
    }

    public function updateData(UuidInterface $uuid, ExternalDataServiceInterface $dataService)
    {
        $city = $this->cityRepository->findById($uuid);
        $this->timeZoneRepository->setCityId($city->getId());

        if($this->timeZoneRepository->needFreshData())
        {
            $data = false;
            $errorMsg = "";
            try {
                $data = $dataService->getData($city->getLongitude(), $city->getLatitude());
            } catch (\Throwable $error) {
                $errorMsg = $error->getMessage();
                //todo залогировать ошибку
            }

            if(!$data || !isset($data['status']) || $data['status'] != 'OK') {
                throw new ExternalDataProblemException($errorMsg);
            }
            $this->timeZoneRepository->refreshData($data);
        }

    }


}