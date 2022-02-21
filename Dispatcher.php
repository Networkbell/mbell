<?php
require MBELLPATH . 'lang/Lang.php';
require MBELLPATH . 'View/View.php';
require MBELLPATH . 'Model/Model.php';
require MBELLPATH . 'Controller/Controller.php';

require MBELLPATH . 'Controller/InstallController.php';
require MBELLPATH . 'Controller/LoginController.php';
require MBELLPATH . 'Controller/PrefController.php';
require MBELLPATH . 'Controller/HomeController.php';
require MBELLPATH . 'Controller/ChangeController.php';
require MBELLPATH . 'Controller/CronController.php';

class Dispatcher
{

    public function __construct()
    {
    }

    public function dispatch()
    {
        //déjà installé 1 fois
        if (file_exists(MBELLPATH . 'config/admin.php')) {
            if (MB_DEBUG) {
                var_dump('Dispatch : 0');
            }
            require  MBELLPATH . 'config/admin.php';
            $version = $this->versionNumURL(false);

            $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "home";
            $action = (isset($_GET['action'])) ? $_GET['action'] : "index";

            $version_installed = (isset($version_installed)) ? floatval($version_installed) : ($version + 1);



            // on empéche d'accéder à install si install est terminé
            if ($controller == "install" && $installed == 'yes' && $version == $version_installed) {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 1');
                }
                $controller = 'home';
                $action = 'index';
                // phase d'install principal
            } elseif ((($installed == 'no' || $installed == '{no}') && $version == $version_installed && $controller != 'install') 
            || (($installed == 'no' || $installed == '{no}') &&  $controller == 'install')
            ) {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 2');
                }
                $controller = 'install';
                $action = (isset($_GET['action'])) ? $_GET['action'] : "step1";
            }
            // ecrasement de fichier avec version plus ancienne ou version inférieur à 2.3
            elseif (($installed == 'yes' || $installed == 'installed_true' || $installed == '{no}') && $version < $version_installed && ($controller == 'install' || !class_exists($controller))) {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 3');
                }
                $controller = 'install';
                $action = 'error';
            }
            // Phase de Mise à Jour (version supérieur à 2.3)
            elseif ($installed == 'yes' && $version > $version_installed && ($controller == 'install' || !class_exists($controller))) {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 4');
                }
                $controller = 'install';
                $action = (isset($_GET['action'])) ? $_GET['action'] : "maj";
                // fin de l'étape 9 d'install si maj --> vers pref 
            } elseif (($installed == 'no' || $installed == '{no}') && $version > $version_installed && $controller == 'pref' && isset($_SESSION['user_login'])) {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 5');
                }              
                $controller =  "pref";
                $action = 'sas';                           
                   //étape de login dans l'installation 
            } elseif (($installed == 'no' || $installed == '{no}') && $controller == 'login') {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 6');
                }
                if (!isset($_SESSION['user_login']) && ($controller == "install")) {
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 7');
                    }
                    $controller = 'login';
                    $action = 'install';
                } elseif (isset($_SESSION['user_login']) && ($controller == "install")) {
                    $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "install";
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 8');
                    }
                    $action = (isset($_GET['action'])) ? $_GET['action'] : "step6";
                } else {
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 9');
                    }
                    $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "login";
                    $action = (isset($_GET['action'])) ? $_GET['action'] : "form";
                }
                // PHASE PRINCIPAL PREF-CHANGE-CRON avec LOGIN
            } elseif ($installed == 'yes' && $version == $version_installed) {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 10');
                }
                /*VERIFICATION DE LOGIN*/
                if ((!isset($_SESSION['user_login']) && ($controller == "pref")) || (!isset($_SESSION['user_login']) && ($controller == "change")) || (!isset($_SESSION['user_login']) && ($controller == "cron"))) {
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 11');
                    }
                    $controller = 'login';
                    $action = 'form';
                    //si login correct, on ouvre l'accès
                } else if ((isset($_SESSION['user_login']) && ($controller == "pref")) || (isset($_SESSION['user_login']) && ($controller == "change")) || (isset($_SESSION['user_login']) && ($controller == "cron"))) {
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 12');
                    }
                    $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "pref";
                    $action = (isset($_GET['action'])) ? $_GET['action'] : "list";
                }
                //pour accéder à connexion login et déconnexion login
                else if (($controller == "login")) {
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 13');
                    }
                    $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "login";
                    $action = (isset($_GET['action'])) ? $_GET['action'] : "form";
                    //condition pour forcer la classe et la méthode si mauvaise saisie de URL (pour index.php aussi)
                } elseif (!class_exists($controller) || !method_exists($controller, $action)) {
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 14');
                    }
                    $controller = 'home';
                    $action = 'index';
                }
                //HomeController
                else {
                    if (MB_DEBUG) {
                        var_dump('Dispatch : 15');
                    }
                    $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "home";
                    $action = (isset($_GET['action'])) ? $_GET['action'] : "index";
                }
            }


            //condition pour forcer la classe et la méthode si mauvaise saisie de URL
            elseif (!class_exists($controller) || !method_exists($controller, $action)) {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 16');
                }
                $controller = 'home';
                $action = 'index';
            }
            //HomeController
            else {
                if (MB_DEBUG) {
                    var_dump('Dispatch : 17');
                }
                $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "home";
                $action = (isset($_GET['action'])) ? $_GET['action'] : "index";
            }
        }

        //Si admin.php n'existe pas, alors on charge le formulaire d'installation
        else {
            /*
            require 'config/admin_backup.php';           
            if (MB_DEBUG) {
                var_dump('Dispatch : 18');
            }*/
            $controller = 'install';
            $action = (isset($_GET['action'])) ? $_GET['action'] : 'step1';
            $action = ($action != 'step1') ? $action : 'step1';
        }
        if (file_exists(MBELLPATH . 'config/admin.php')) {
            if (MB_DEBUG) {
                var_dump('Controller = ' . $controller);
                var_dump('Action = ' . $action);
                var_dump('Installation = ' . $installed);
                var_dump('Version Installé = ' . $version_installed);
            }
        }
        $controller = ucfirst($controller) . "Controller";
        $action = $action . "Action";
        $my_controller = new $controller();
        $my_controller->$action();
    }




    /**
     * Retourne le numéro de version de Mbell disponible en téléchargement (true) ou installable (false)
     *
     * @param [boolean] $true = true (meteobell.com) / false (mbell)
     * @return float
     */
    public function versionNumURL($true)
    {
        $pathVersion =  'config/version.txt';
        if ($true == true) {
            $version_file = file_get_contents('http://www.meteobell.com/mbell/' . $pathVersion);
        } elseif ($true == false) {
            $version_file = (file_exists($pathVersion)) ? file_get_contents($pathVersion) : '';
        }
        $filed = explode(PHP_EOL, $version_file);
        $version = floatval($filed[0]);
        return $version;
    }

    /**
     * Retourne le numéro de version de Mbell actuel depuis config/version.txt
     *
     * @return string
     */
    public function version()
    {
        $version = $this->versionNumURL(false);
        return strval($version);
    }
}
