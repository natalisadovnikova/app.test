<?php

namespace app\controller;

class SiteController
{
    public function index()
    {
        echo 'Для получения локального времени в городе по переданной метке UTC+0<br>';
        echo 'http://app.test/localtime/3ef2f49f-7543-431e-890d-fceae99c97d8/1675170876<br><br>';

        echo 'Для получения временной метки UTC+0 по локальному времени города<br>';
        echo 'http://app.test/utctime/3ef2f49f-7543-431e-890d-fceae99c97d8/1675170876<br><br>';
    }
}