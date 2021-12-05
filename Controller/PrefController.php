<?php





class PrefController extends Controller
{



    public function __construct()
    {
        $this->view = new PrefView();
        $this->model = new PrefModel();
        $this->paramStat = new StationModel();

        parent::__construct();
    }

    public function listAction()
    {
        $station = $this->paramStat->getStationActive();
        $paramJson = $this->paramStat->getAPI();
        $config = $this->model->getConfigActive();
        $tab = $this->model->getTabActive();
        $this->view->displayList($station, $paramJson, $config, $tab);
    }

    public function configAction()
    {
        $lg = $this->l->getLg();
        $response = $this->model->updateConfig($this->paramPost);
        if ($response) {
            header('location:index.php?controller=pref&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=pref&action=error&lg=' . $lg);
        }
    }


    public function defaultAction()
    {
        $lg = $this->l->getLg();
        $response = $this->model->updateDefault($this->paramPost);
        if ($response) {
            header('location:index.php?controller=pref&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=pref&action=error&lg=' . $lg);
        }
    }


    public function linesAction()
    {
        $lg = $this->l->getLg();
        $response = $this->model->updateLines($this->paramPost);
        if ($response) {
            header('location:index.php?controller=pref&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=pref&action=error&lg=' . $lg);
        }
    }

    public function tabAction()
    {
        $lg = $this->l->getLg();
        $response = $this->model->updateTab($this->paramPost);
        if ($response) {
            header('location:index.php?controller=pref&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=pref&action=error&lg=' . $lg);
        }
    }

    public function errorAction()
    {
        $lg = $this->l->getLg();
        header('location:index.php?controller=home&lg=' . $lg);
    }


}
