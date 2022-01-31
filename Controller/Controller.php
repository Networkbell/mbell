<?php

require MBELLPATH . 'View/InstallView.php';
require MBELLPATH . 'Model/InstallModel.php';
require MBELLPATH . 'View/LoginView.php';
require MBELLPATH . 'Model/LoginModel.php';
require MBELLPATH . 'View/PrefView.php';
require MBELLPATH . 'Model/PrefModel.php';
require MBELLPATH . 'View/HomeView.php';
require MBELLPATH . 'Model/HomeModel.php';
require MBELLPATH . 'View/StationView.php';
require MBELLPATH . 'Model/StationModel.php';
require MBELLPATH . 'View/ChangeView.php';
require MBELLPATH . 'Model/ChangeModel.php';
require MBELLPATH . 'View/CronView.php';
require MBELLPATH . 'Model/CronModel.php';
require MBELLPATH . 'config/Color.php';
require MBELLPATH . 'config/Moon.php';


abstract class Controller
{

    protected $model;
    protected $view;
    protected $paramGet;
    protected $paramPost;
    protected $paramStat;

    public function __construct()
    {

        $this->l = new Lang();
        $this->dispatcher = new Dispatcher();
        $this->file_admin = dirname(dirname(__FILE__)) . '/config/admin.php';

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
        } else {
            $_POST["var_sun"] = '';
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

    public function root()
    {
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $response = $root.$path;
        return $response;
    }
}
