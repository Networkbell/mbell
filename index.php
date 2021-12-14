<?php
session_start();

/* DEBUG FUNCTION */
ini_set("display_errors", "0");
error_reporting(E_ALL);

require 'Dispatcher.php';
$controller = new Dispatcher();
$controller->dispatch();
