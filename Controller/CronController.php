<?php



class CronController  extends Controller
{



    public function __construct()
    {
        $this->view = new CronView();
        $this->model = new CronModel();
        $this->paramStat = new StationModel();
        $this->stationview = new StationView();
        parent::__construct();
    }


    public function listAction()
    {
        $lg = $this->l->getLg();
        $paramJson = $this->paramStat->getAPI();
        $config = $this->model->getConfigActive();
        $active = $this->paramStat->getStationActive();
        $liveStation = ($active['stat_type'] == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';
        $timeCron = $this->model->getLastTimeCron();

        if (($this->model->IsServerCronDisabled() == true) && ($config['config_cron'] == 1) && (!(isset($_COOKIE['cron'])) || empty($_COOKIE['cron']))) {
            $status_cron = 0;
            setcookie('cron', 'activated', time() + (600));
            //le cookie permet que UpdateConfigCron fonctionne pendant les 10mn où IsServerCronDisabled est toujours vrai, mais que le cron a été réactivé
            $response = $this->model->UpdateConfigCron($config, $status_cron);
            if ($response) {
                header('location:index.php?controller=cron&action=list&lg=' . $lg);
            } else {
                header('location:index.php?controller=cron&action=error&lg=' . $lg);
            }
        }
        $this->view->cronList($config, $active, $paramJson, $liveStation, $timeCron);
    }

    public function activeAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $status_cron = 1;
        $response = $this->model->UpdateConfigCron($config, $status_cron);

        if ($response) {

            $response2 = $this->model->activateCron();
            if ($response2) {
                header('location:index.php?controller=cron&action=list&lg=' . $lg);
            } else {
                header('location:index.php?controller=cron&action=error&lg=' . $lg);
            }
        } else {
            header('location:index.php?controller=cron&action=error&lg=' . $lg);
        }
    }

    public function disactiveAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $status_cron = 0;
        $response = $this->model->UpdateConfigCron($config, $status_cron);
        if ($response) {
            header('location:index.php?controller=cron&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=cron&action=error&lg=' . $lg);
        }
    }
}
