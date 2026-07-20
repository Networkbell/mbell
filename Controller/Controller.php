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
    protected $paramGet = array();
    protected $paramPost = array();
    protected $paramStat;
    protected $l;
    protected $dispatcher;
    protected $file_admin;

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

        /* Convert checkbox array input into a single checkbox value. */
        if (isset($_POST['var_sun']) && is_array($_POST['var_sun'])) {
            if (in_array('sun', $_POST['var_sun'], true) && in_array('uv', $_POST['var_sun'], true)) {
                $_POST['var_sun'] = 'sun_uv';
            } else {
                $_POST['var_sun'] = $_POST['var_sun'][0] ?? '';
            }
        } elseif (!isset($_POST['var_sun'])) {
            $_POST['var_sun'] = '';
        }

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->paramPost[$key] = $this->protected_values($value);
            }
        }

        if (empty($this->paramPost['metabdd'])) {
            $this->paramPost['metabdd'] = 'mb_';
        }
    }

    private function protected_values($values)
    {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $values[$key] = $this->protected_values($value);
            }

            return $values;
        }

        $values = trim((string) $values);
        $values = stripslashes($values);
        $values = htmlspecialchars($values, ENT_QUOTES, 'UTF-8');

        return $values;
    }

    public function root()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/'), '/');
        $response = $scheme . '://' . $host . $path;

        return $response;
    }
}
