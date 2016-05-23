<?php

function csv($filename)
{
    $model = new Csv\Model($filename);
    $controller = new Csv\Controller($model, $filename);
    $action = isset($_GET['csv_action']) ? $_GET['csv_action'] : 'index';
    return $controller->{"process$action"}();
}
