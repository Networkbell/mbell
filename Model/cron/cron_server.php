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


function activateCron()
{
    $cron_model = new CronModel();
    $station_model = new StationModel();
    $station_view = new StationView();

    $crontime = $cron_model->getConfigActiveCron()['config_crontime'];
    $cron = $cron_model->getConfigActiveCron()['config_cron'];
    $station = $station_model->getStationActive();
    
    $type = (isset($station['stat_type'])) ? $station['stat_type'] : null;
    $livenbr = ($type == 'live') ? $station['stat_livenbr'] - 1 : 0;
    $livetab = 0; // pas besoin ici

    $nbr_cron_server = ($type == 'live') ? $crontime / 15 : $crontime / 10;
    $time_cron_limit = ($crontime * 60) - (5 * 60);

    ignore_user_abort(true);
    set_time_limit($time_cron_limit);

    $i = 1;
    while ($cron == 2 && $i <= $nbr_cron_server) {

        //on va chercher les valeurs dans la boucle plutôt que le controller en amont pour que les données de l'API se mettent bien à jour une fois le cron lancé
        $liveStation1 = ($type == 'live') ? $station_model->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
        $datas1 = $station_model->getAPI();
        
        $dateString = $station_view->getAPIDatasUp($datas1, $station, $liveStation1, $livenbr,$livetab)['time'];

        $time_sleep = ($type == 'live') ? 780 : 480; // 780 = 13 minutes / 480 = 8 minutes
        $time_precision = ($type == 'live') ? 15 : 10; // API v2 = 15mn / API v1 = 10mn
        $time_deviation = ($type == 'live' || $type == 'weewx') ? 0 : $cron_model->waitDeviationCron($dateString, $time_precision); // pas de déviation avec API v2 car on utilise time() dans l'API

        $time = $cron_model->waitactiveCron($dateString, $time_precision) + $time_deviation;

        time_sleep_until($time);

        $datas2 = $station_model->getAPI(); //on remet à jour les datas après le temps d'attente
        $liveStation2 = ($type == 'live') ? $station_model->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
        $response = $cron_model->addWeather($datas2, $station, $liveStation2, $livenbr);
        if ($i < $nbr_cron_server) {
            sleep($time_sleep);
        }
        $i++;
    }

    return $response;
}


$cron_model = new CronModel();
$config = $cron_model->getConfigActiveCron();

if ($config['config_crontime'] != 0 && ($config['config_cron'] != 1 || $config['config_cron'] != 3) ) {
    $status_cron = 2;
    $response =  $cron_model->UpdateConfigCron($config, $status_cron);
    if ($response) {
        var_dump('config_cron updated to 2');
        $response2 = activateCron(); 
        if ($response2) {
            var_dump('cronjob loop activated');
        }
        else {
            var_dump('cronjob loop failed');
        }
    }
} else {
    var_dump('Cronjob server failed. Absence of cron server duration (choose 20-30-60mn) or other cronjob (1. easy / 3. expert) activated');
}


