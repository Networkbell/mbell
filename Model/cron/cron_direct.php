#!/usr/local/bin/php


<?php
$path = dirname(__FILE__);
define('ROOT', dirname(dirname($path)) . '/');

//pour faire remplacer le MBELLPATH de index.php
if (!defined('MBELLPATH')) {
    define('MBELLPATH', dirname(dirname($path)) . '/');
}

require ROOT . 'Dispatcher.php';





/**
 * Clone de ModelCron
 *
 * 
 */


function activatedCron()
{
    $cron_model = new CronModel();
    $station_model = new StationModel();

    $station = $station_model->getStationActive();
    $type = (isset($station['stat_type'])) ? $station['stat_type'] : null;
    
    
    $datas = $station_model->getAPI();
    
    $liveStation = ($type == 'live') ? $station_model->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
    $response = $cron_model->addWeather($datas, $station, $liveStation);
    
    return $response;
}


$cron_model = new CronModel();
$config = $cron_model->getConfigActiveCron();

if ($config['config_crontime'] == 0 && ($config['config_cron'] != 1 || $config['config_cron'] != 2)) {
    $status_cron = 3;
    $response =  $cron_model->UpdateConfigCron($config, $status_cron);
    if ($response) {
        var_dump('config_cron updated to 3');
        $response2 = activatedCron();
        if ($response2) {
            var_dump('cronjob activated');
        }
        else {
            var_dump('cronjob failed');
        }
    }
} else {
    var_dump('Direct cronjob failed. Duration of cron server must be on 0 and other cronjob (1. easy / 2. intermediate) must be not activated');
}
