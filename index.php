<?php
session_start();

/**
 * Compare two sets of versions, where major/minor/etc. releases are separated by dots.
 * Returns 0 if both are equal, 1 if A > B, and -1 if A < B. 
 * Remplace version_compare()
 *
 * @param [string] $a 
 * @param [string] $b 
 * @return int
 */
function versionCompare($a, $b)
{
    $a = explode(".", rtrim($a, ".0"));
    $b = explode(".", rtrim($b, ".0"));
    foreach ($a as $depth => $aVal) {
        if (isset($b[$depth])) {
            if ($aVal > $b[$depth]) return 1;
            else if ($aVal < $b[$depth]) return -1;
        } else {
            return 1;
        }
    }
    return (count($a) < count($b)) ? -1 : 0;
}



/**
 * PATH ABSOLUTE FOR CRON
 */
if (!defined('MBELLPATH')) {
    define('MBELLPATH', dirname(__FILE__) . '/');
}

/* DEBUG FUNCTION */
if (file_exists(MBELLPATH . 'config/admin.php')) {
    require MBELLPATH . 'config/admin.php';
    if (MB_DEBUG) {
        ini_set("display_errors", "1");
        error_reporting(E_ALL);
    }
}

/**
 * VERSION PHP 7 à 7.4
 */

if (versionCompare(phpversion(), '8') == 1 || versionCompare(phpversion(), '8') == 0) {
    echo "FR : Votre hébergement est en version PHP " . phpversion() . ". Cette version est trop précoce. MBell est compatible de version 7 à 7.4";
    echo "<br><br>";
    echo "EN : Your hosting is in PHP " . phpversion() . " version. This version is too early. MBell is compatible from version 7 to 7.4";
} else if (versionCompare(phpversion(), '7') == -1) {
    echo "FR : Votre hébergement est en version PHP " . phpversion() . ". Cette version est obsolète. MBell est compatible de version 7 à 7.4";
    echo "<br><br>";
    echo "EN : Your hosting is in PHP " . phpversion() . " version. This version is out of date. MBell is compatible from version 7 to 7.4 ";
} else {

    /**
     * MBELL LAUNCH
     */
    require MBELLPATH . 'Dispatcher.php';
    $controller = new Dispatcher();
    $controller->dispatch();
}
