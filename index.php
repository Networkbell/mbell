<?php
session_start();

/*
 * Absolute path for cron.
 */
if (!defined('MBELLPATH')) {
    define('MBELLPATH', __DIR__ . '/');
}

/* Debug function */
if (file_exists(MBELLPATH . 'config/admin.php')) {
    require MBELLPATH . 'config/admin.php';

    if (defined('MB_DEBUG') && MB_DEBUG) {
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
    }
}

/*
 * PHP version check.
 * MBell now requires PHP 7.4 or higher.
 */
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    echo 'FR : Votre hébergement est en version PHP ' . PHP_VERSION . '. Cette version est trop ancienne. MBell nécessite PHP 7.4 ou plus.';
    echo '<br><br>';
    echo 'EN : Your hosting is running PHP ' . PHP_VERSION . '. This version is too old. MBell requires PHP 7.4 or higher.';
    exit;
}

/*
 * MBell launch.
 */
require MBELLPATH . 'Dispatcher.php';
$controller = new Dispatcher();
$controller->dispatch();
