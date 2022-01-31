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

    $nbr_cron_server = ($type == 'live') ? $crontime / 15 : $crontime / 10;
    $time_cron_limit = ($crontime * 60) - (5 * 60);

    ignore_user_abort(true);
    set_time_limit($time_cron_limit);

    $i = 1;
    while ($cron == 1 && $i <= $nbr_cron_server) {

        //on va chercher les valeurs dans la boucle plutôt que le controller en amont pour que les données de l'API se mettent bien à jour une fois le cron lancé
        $liveStation1 = ($type == 'live') ? $station_model->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
        $datas1 = $station_model->getAPI();
        $dateString = $station_view->getAPIDatasUp($datas1, $station, $liveStation1)['time'];

        $time_sleep = ($type == 'live') ? 780 : 480; // 780 = 13 minutes / 480 = 8 minutes
        $time_precision = ($type == 'live') ? 15 : 10; // API v2 = 15mn / API v1 = 10mn
        $time_deviation = ($type == 'live') ? 0 : $cron_model->waitDeviationCron($dateString, $time_precision); // pas de déviation avec API v2 car on utilise time() dans l'API

        $time = $cron_model->waitactiveCron($dateString, $time_precision) + $time_deviation;

        time_sleep_until($time);

        $datas2 = $station_model->getAPI(); //on remet à jour les datas après le temps d'attente
        $liveStation2 = ($type == 'live') ? $station_model->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
        $response = $cron_model->addWeather($datas2, $station, $liveStation2);
        if ($i < $nbr_cron_server) {
            sleep($time_sleep);
        }
        $i++;
    }

    return $response;
}


$cron_model = new CronModel();
$config = $cron_model->getConfigActiveCron();

if ($config['config_crontime'] != 0) {
    $status_cron = 1;
    $response =  $cron_model->UpdateConfigCron($config, $status_cron);
    if ($response) {
        var_dump($response);
        $response2 = activateCron();
        //  
        if ($response2) {
            var_dump($response2);
        }
    }
} else {
    var_dump('Veuillez choisir une durée de crontab dans Mbell et désactiver le cronjob PHP de Mbell');
}


