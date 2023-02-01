<?php

namespace app\service;

use app\domain\value\Latitude;
use app\domain\value\Longitude;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ExternalDataService implements \app\service\interfaces\ExternalDataServiceInterface
{
    private $serviceUrl;
    private $method;
    private $clientData;

    public function __construct()
    {
        //todo вынести в конфиги все настройки
        $this->serviceUrl = 'http://api.timezonedb.com';
        $this->method = '/v2.1/get-time-zone';
        $this->clientData = [
            'connect_timeout' => 30,
            'key' => "GSU7TU8XNDW5",
            'format' => 'json',
            'by' => 'position',
            'lat' => 33.9383,
            'lng' => -81.1200,
        ];
    }

    /**
     * Запрос данных во внешнем сервисе
     * @param Longitude $lng
     * @param Latitude $ltd
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getData(Longitude $lng, Latitude $ltd): array
    {
        $this->clientData['lat'] = $ltd->getValue();
        $this->clientData['lng'] = $lng->getValue();
        $url = $this->createUrl($this->serviceUrl, $this->method, $this->clientData);

        try {
            $client = new Client();
            $response = $client->get($url);

            $body = $response->getBody();
        } catch (ClientException $exception) {
            $body = $exception->getResponse()->getBody();
        }

        $content = $body->getContents();
        return json_decode($content, true);
    }

    /**
     * Сборка урла
     * @param $host
     * @param $method
     * @param $append_params
     * @return string
     */
    function createUrl($host, $method, $append_params = [])
    {
        $res = $host . $method;
        if (count($append_params)) {
            $res = $res . '?' . $this->bindParams($append_params);
        }
        return $res;
    }

    /**
     * Подготовка параметров
     * @param $params
     * @return string
     */
    function bindParams($params)
    {
        $u = [];
        foreach ($params as $k => $param) {
            if (is_array($param)) {
                foreach ($param as $p) {
                    $u[] = $k . "=" . urlencode($p);
                }
            } else {
                $u[] = $k . "=" . urlencode($param);
            }
        }
        asort($u);
        return implode('&', $u);
    }

}