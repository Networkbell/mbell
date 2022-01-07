<?php
session_start();

/* DEBUG FUNCTION */
if (file_exists('config/admin.php')) {
    require 'config/admin.php';
    if (MB_DEBUG) {
        ini_set("display_errors", "1");
        error_reporting(E_ALL);
    }
}

require 'Dispatcher.php';
$controller = new Dispatcher();
$controller->dispatch();
