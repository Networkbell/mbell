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
        $active = $this->paramStat->getStationActive();
        $liveStation = ($active['stat_type'] == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';
        $paramJson = $this->paramStat->getAPI();
        $config = $this->model->getConfigActive();
        $tab = $this->model->getTabActive();
        $this->view->displayList($active, $paramJson, $config, $tab, $liveStation);
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


    /**
     * $reponse return string si erreur, sinon retourne false
     *
     * @return void
     */
    public function patchAction()
    {
        $lg = $this->l->getLg();
        $version = $this->dispatcher->versionNumURL(true);
        $zipFile = 'mbellmaj.zip';
        $reponse1 = $this->model->downloadZip($version, $zipFile);
        if (!$reponse1) {
            $reponse2 =  $this->model->extractZip($zipFile);
            if (!$reponse2) {
                header('location:index.php?controller=install&action=maj&lg=' . $lg);
            } else {
                if (MB_DEBUG) {
                    var_dump($reponse2);
                } else {
                    header('location:index.php?controller=pref&action=error&lg=' . $lg);
                }
            }
            unlink($zipFile);
        } else {
            if (MB_DEBUG) {
                var_dump($reponse1);
            } else {
                header('location:index.php?controller=pref&action=error&lg=' . $lg);
            }
        }
    }

    public function sasAction()
    {    
        $this->view->sasPref();                 
    }

}
