<?php



class HomeController  extends Controller
{



    public function __construct()
    {
        $this->view = new HomeView();
        $this->model = new HomeModel();
        $this->paramStat = new StationModel();
        parent::__construct();
    }


    public function indexAction()
    {
        $active = $this->paramStat->getStationActive();       
        $paramJson = $this->paramStat->getAPI();
        $liveStation = ($active['stat_type']=='live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']): '';              
        $config = $this->model->getConfigActive();   
        $tab = $this->model->getTabActive();  
        $switch = $this->model->allChoice($config);    
        $this->view->displayHome($active, $paramJson, $config, $tab, $switch,$liveStation);
    }


    public function s_tempAction()
    {
        
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_temp', $this->model->tempList(), $this->model->tempDefaut($config));

        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }

    public function s_windAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_wind', $this->model->windList(), $this->model->windDefaut($config));
        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }

    public function s_rainAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_rain', $this->model->rainList(), $this->model->rainDefaut($config));
        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }
    public function s_pressAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_press', $this->model->pressList(), $this->model->pressDefaut($config));
        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }


    public function s_cssAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_css', $this->model->cssList(), $this->model->cssDefaut($config));
        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }


    public function s_daynightAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_daynight', $this->model->daynightList(), $this->model->daynightDefaut($config));       
        if (isset($response)) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }


    public function s_colorAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_color', $this->model->colorList(), $this->model->colorDefaut($config));
        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }

    public function s_iconAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_icon', $this->model->iconList(), $this->model->iconDefaut($config));
        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }

    public function s_dmyAction()
    {
        $lg = $this->l->getLg();
        $config = $this->model->getConfigActive();
        $response = $this->model->getChoice('s_dmy', $this->model->dmyList(), $this->model->dmyDefaut($config));
        if ($response) {           
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=error&lg=' . $lg);
        }
    }

}
