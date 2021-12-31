<?php

require 'View/InstallView.php';
require 'Model/InstallModel.php';
require 'View/LoginView.php';
require 'Model/LoginModel.php';
require 'View/PrefView.php';
require 'Model/PrefModel.php';
require 'View/HomeView.php';
require 'Model/HomeModel.php';
require 'View/StationView.php';
require 'Model/StationModel.php';
require 'View/ChangeView.php';
require 'Model/ChangeModel.php';
require 'View/CronView.php';
require 'Model/CronModel.php';
require 'config/Color.php';
require 'config/Moon.php';


abstract class Controller
{
    protected $l;
    protected $model;
    protected $view;
    protected $paramGet;
    protected $paramPost;
    protected $paramStat;

    public function __construct()
    {

        $this->l = new Lang();



        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->paramGet[$key] = $this->protected_values($value);
            }
        }

        //converti le checkbox avec array (name[] + value) en checkbox normal
        if (!empty($_POST["var_sun"])) {
            if ((in_array('sun', $_POST["var_sun"])) && (in_array('uv', $_POST["var_sun"]))) {
                $_POST["var_sun"] = 'sun_uv';
            } else {
                $_POST["var_sun"] = $_POST["var_sun"][0];
            }
        }

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->paramPost[$key] = $this->protected_values($value);
            }
        }

        
        if (empty($this->paramPost["metabdd"])) {
            $this->paramPost["metabdd"] = "mb_";
        }





    }

    private function protected_values($values)
    {
        $values = trim($values);
        $values = stripslashes($values);
        $values = htmlspecialchars($values);
        return $values;
    }
}
