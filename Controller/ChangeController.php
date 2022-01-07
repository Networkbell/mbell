<?php



class ChangeController  extends Controller
{



    public function __construct()
    {
        $this->view = new ChangeView();
        $this->model = new ChangeModel();
        $this->paramStat = new StationModel();
        $this->paramCron = new CronModel();
        parent::__construct();
    }


    public function listAction()
    {
        $paramJson = $this->paramStat->getAPI();
        $v1 = $this->model->getAllStationUserLogin('v1');
        $v2 = $this->model->getAllStationUserLogin('v2');
        $live = $this->model->getAllStationUserLogin('live');
        $weewx = $this->model->getAllStationUserLogin('weewx');
        $active = $this->paramStat->getStationActive();
        $liveStation = ($active['stat_type'] == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';

        $this->view->changeList($v1, $v2, $live, $weewx, $active, $paramJson, $liveStation);
    }

    public function chooseAction()
    {
        $user = $this->model->getUserSession();
        $station = $this->model->getStation();
        $this->view->addStationView($station, $user);
    }

    public function addAction()
    {
        $lg = $this->l->getLg();
        $response = $this->model->addStationChange($this->paramPost);
        if ($response) {
            header('location:index.php?controller=change&action=addconfig&stat_id=' . $response . '&lg=' . $lg);
        } else {
            header('location:index.php?controller=change&action=choose&lg=' . $lg);
        }
    }

    public function addconfigAction()
    {
        $lg = $this->l->getLg();
        $station = $this->model->getItem($this->paramGet);
        $response = $this->model->addConfig($station);
        if ($response) {
            header('location:index.php?controller=change&action=addtab&stat_id=' . $response . '&lg=' . $lg);
        } else {
            header('location:index.php?controller=change&action=choose&lg=' . $lg);
        }
    }


    public function addtabAction()
    {
        $lg = $this->l->getLg();
        $station = $this->model->getItem($this->paramGet);
        $response = $this->model->addTable($station);
        if ($response) {
            header('location:index.php?controller=change&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=change&action=choose&lg=' . $lg);
        }
    }

    public function deleteAction()
    {
        $lg = $this->l->getLg();

        $response = $this->model->deleteBDD($this->paramGet);
        if ($response) {
            header('location:index.php?controller=change&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=change&action=error&lg=' . $lg);
        }
    }

    public function updateAction()
    {
        $user = $this->model->getUserSession();
        $item = $this->model->getItem($this->paramGet);
        $this->view->updateStation($item, $user);
    }


    public function updateFormAction()
    {
        $lg = $this->l->getLg();
        $response = $this->model->updateBDD($this->paramPost);
        if ($response) {
            header('location:index.php?controller=change&action=list&lg=' . $lg);
        } else {
            if (MB_DEBUG) {
                var_dump($response);
            } else {
                header('location:index.php?controller=change&action=error&lg=' . $lg);
            }
        }
    }



    public function activeAction()
    {
        $lg = $this->l->getLg();
        $active = $this->paramStat->getStationActive();
        $cron = $this->paramCron->getConfigActiveCron();
        $response = $this->model->activateStation($this->paramGet, $active, $cron);
        if ($response) {
            header('location:index.php?controller=change&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=change&action=error&lg=' . $lg);
        }
    }



    public function errorAction()
    {
        $lg = $this->l->getLg();
        header('location:index.php?controller=home&lg=' . $lg);
    }
}
