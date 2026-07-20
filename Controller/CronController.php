<?php

class CronController extends Controller
{
    protected $stationview;

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
        $config = $this->model->getConfigActiveCron();
        $active = $this->paramStat->getStationActive();
        $liveStation = (($active['stat_type'] ?? '') == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';
        $timeCron = $this->model->getLastTimeCron();
        $livenbr = (($active['stat_type'] ?? '') == 'live') ? (($active['stat_livenbr'] ?? 1) - 1) : 0;

        if (
            $this->model->IsServerCronDisabled() == true
            && (($config['config_cron'] ?? 0) == 1 || ($config['config_cron'] ?? 0) == 2 || ($config['config_cron'] ?? 0) == 3)
            && (!isset($_COOKIE['cron']) || empty($_COOKIE['cron']))
        ) {
            $status_cron = 0;
            setcookie('cron', 'activated', time() + 600);

            /* The cookie allows UpdateConfigCron to work during the 10 minutes when IsServerCronDisabled is still true but cron has already been reactivated */
            $response = $this->model->UpdateConfigCron($config, $status_cron);

            if ($response) {
                header('location:index.php?controller=cron&action=list&lg=' . $lg);
                exit;
            }

            header('location:index.php?controller=cron&action=error&lg=' . $lg);
            exit;
        }

        $this->view->cronList($config, $active, $paramJson, $liveStation, $timeCron, $livenbr);
    }

    public function activeAction()
    {
        $lg = $this->l->getLg();
        $dial = $this->l->trad('CRON_ALERT_2');
        $url = 'controller=cron&action=server';

        $config = $this->model->getConfigActiveCron();
        if (($config['config_crontime'] ?? null) == 0) {
            $status_cron = 1;
            $response = $this->model->UpdateConfigCron($config, $status_cron);
            setcookie('cron', 'activated', time() - 600);

            if ($response) {
                $response2 = $this->model->activateCron();

                if ($response2) {
                    header('location:index.php?controller=cron&action=list&lg=' . $lg);
                    exit;
                }

                header('location:index.php?controller=cron&action=error&lg=' . $lg);
                exit;
            }

            header('location:index.php?controller=cron&action=error&lg=' . $lg);
            exit;
        }

        $this->view->alertCron($dial, $lg, $url);
    }

    public function disactiveAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActiveCron();
        $status_cron = 0;
        $response = $this->model->UpdateConfigCron($config, $status_cron);

        if ($response) {
            header('location:index.php?controller=cron&action=list&lg=' . $lg);
            exit;
        }

        header('location:index.php?controller=cron&action=error&lg=' . $lg);
        exit;
    }

    public function serverAction()
    {
        $lg = $this->l->getLg();
        $paramJson = $this->paramStat->getAPI();
        $config = $this->model->getConfigActiveCron();
        $active = $this->paramStat->getStationActive();
        $liveStation = (($active['stat_type'] ?? '') == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';
        $timeCron = $this->model->getLastTimeCron();
        $this->view->serverList($config, $active, $paramJson, $liveStation, $timeCron);
    }

    public function directAction()
    {
        $lg = $this->l->getLg();
        $paramJson = $this->paramStat->getAPI();
        $config = $this->model->getConfigActiveCron();
        $active = $this->paramStat->getStationActive();
        $liveStation = (($active['stat_type'] ?? '') == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';
        $timeCron = $this->model->getLastTimeCron();
        $this->view->directList($config, $active, $paramJson, $liveStation, $timeCron);
    }

    public function timerAction()
    {
        $lg = $this->l->getLg();
        $dial = $this->l->trad('CRON_ALERT_1');
        $url = 'controller=cron&action=list';

        $config = $this->model->getConfigActiveCron();
        if (($config['config_cron'] ?? 0) == 0 || ($config['config_cron'] ?? 0) == 3) {
            $response = $this->model->updateCron($this->paramPost);

            if ($response) {
                header('location:index.php?controller=cron&action=server&lg=' . $lg);
                exit;
            }

            header('location:index.php?controller=pref&action=error&lg=' . $lg);
            exit;
        }

        $this->view->alertCron($dial, $lg, $url);
    }
}
