<?php




class InstallController extends Controller
{



    public function __construct()
    {
        $this->view = new InstallView();
        $this->model = new InstallModel();
        $this->paramStat = new StationModel();

        parent::__construct();
    }

    public function step1Action()
    {
        $this->view->InstallMain1();
    }

    public function step2Action()
    {
        $this->model->dropBDD();
        $this->view->InstallMain2();
    }

    public function step2aAction()
    {
        $lg = $this->l->getLg();
        $response = $this->model->addBDD($this->paramPost);
        switch ($response) {
            case 1:
                header('location:index.php?controller=install&action=step3No1&lg=' . $lg);
                break;
            case 2:
                header('location:index.php?controller=install&action=step3No2&lg=' . $lg);
                break;
            case 3:
                header('location:index.php?controller=install&action=step3No3&lg=' . $lg);
                break;
            case 4:
                header('location:index.php?controller=install&action=step3Yes&lg=' . $lg);
                break;
            default:
                header('location:index.php?controller=install&action=step2&lg=' . $lg);
        }
    }
    public function step3No1Action()
    {
        $this->view->InstallMain3No1();
    }
    public function step3No2Action()
    {
        $this->view->InstallMain3No2();
    }
    public function step3No3Action()
    {
        $this->view->InstallMain3No3();
    }
    public function step3YesAction()
    {
        $this->view->InstallMain3Yes();
    }


    public function step4Action()
    {
        $this->model->truncateTable('user');
        $this->view->InstallMain4();
    }

    public function step5Action()
    {
        $lg = $this->l->getLg();
        $response = $this->model->addUser($this->paramPost);
        if ($response) {
            header('location:index.php?controller=login&action=install&lg=' . $lg);
        } else {
            header('location:index.php?controller=install&action=step4&lg=' . $lg);
        }
    }
    public function step6Action()
    {
        $this->model->truncateTable('station');
        $user = $this->model->getUserSession();
        $station = $this->model->getStation();
        $this->view->InstallStation($station, $user);
    }

    public function step7Action()
    {
        $lg = $this->l->getLg();       
        $response = $this->model->addStation($this->paramPost);
        //var_dump($response);
        if ($response) {
            header('location:index.php?controller=install&action=step7b&lg=' . $lg);
        } else {
            header('location:index.php?controller=install&action=step6&lg=' . $lg);
        }
    }

    public function step7bAction()
    {
        $lg = $this->l->getLg();
        $active = $this->paramStat->getStationActive();
        $response = $this->model->addConfig($active);
        if ($response) {
            header('location:index.php?controller=install&action=step7c&lg=' . $lg);
        } else {
            header('location:index.php?controller=install&action=step6&lg=' . $lg);
        }
    }


    public function step7cAction()
    {
        $lg = $this->l->getLg();
        $active = $this->paramStat->getStationActive();
        $response = $this->model->addTable($active);
        if ($response) {
            header('location:index.php?controller=install&action=step8&lg=' . $lg);
        } else {
            header('location:index.php?controller=install&action=step6&lg=' . $lg);
        }
    }

    public function step8Action()
    {
        $active = $this->paramStat->getStationActive();
        $paramJson = $this->paramStat->getAPI();       
        $liveStation = ($active['stat_type']=='live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']): '';    
        $this->view->InstallMain8($active, $paramJson, $liveStation);
    }

    public function step9Action()
    {
        $this->model->InstallTrue();
        $lg = $this->l->getLg();
        header('location:index.php?controller=pref&action=list&lg=' . $lg);
    }
}
