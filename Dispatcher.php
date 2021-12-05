<?php
require 'lang/Lang.php';
require 'View/View.php';
require 'Model/Model.php';
require 'Controller/Controller.php';

require 'Controller/InstallController.php';
require 'Controller/LoginController.php';
require 'Controller/PrefController.php';
require 'Controller/HomeController.php';
require 'Controller/ChangeController.php';

class Dispatcher
{
    public function __construct()
    {
    }

    public function dispatch()
    {

        /*VERIFICATION DU FICHIER DE CONNEXION BDD*/
        if (file_exists('config/admin.php')) {

            /*VERIFICATION ALREADY INSTALLED*/
            require 'config/admin.php';

            $controller = (isset($_GET['controller'])) ? $_GET['controller'] : "home";

            if ($controller == "install" && $installed == 'installed_true') {
                $controller = 'HomeController';
                $action = 'indexAction';
            } else {

                /*VERIFICATION DE LOGIN*/
                if (!isset($_SESSION['user_login']) && ($controller == "pref")) {
                    $controller = 'LoginController';
                    $action = 'formAction';
                } else if (isset($_SESSION['user_login']) && ($controller == "pref")) {
                    
                    $controller = ucfirst($controller) . 'Controller';
                    $action = (isset($_GET['action'])) ? $_GET['action'] : "list";
                    $action = $action . "Action";
                }

                //HomeController
                else {
                    
                    $controller = ucfirst($controller) . "Controller";
                    $action = (isset($_GET['action'])) ? $_GET['action'] : "index";
                    $action = $action . "Action";
                }

                //condition pour forcer la classe et la méthode si mauvaise saisie de URL
                if (!class_exists($controller) || !method_exists($controller, $action)) {
                    $controller = 'HomeController';
                    $action = 'indexAction';
                }
            }
        }

        //Si admin.php n'existe pas, alors on charge le formulaire d'installation
        else {
            $controller = 'InstallController';
            $action = (isset($_GET['action'])) ? $_GET['action'] : 'step1';
            if ($action != 'step1') {               
                $action = $action . "Action";
            } else {
                $action = 'step1Action';
            }
        }

        $my_controller = new $controller();
        $my_controller->$action();
    }
}
