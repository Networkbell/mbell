<?php





class PrefController extends Controller
{



    public function __construct()
    {
        $this->view = new PrefView();
        $this->model = new PrefModel();
        $this->paramStat = new StationModel();
        $this->stationview = new StationView();

        parent::__construct();
    }

    public function listAction()
    {
        $active = $this->paramStat->getStationActive();
        $liveStation = ($active['stat_type'] == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';
        $livenbr = ($active['stat_type'] == 'live') ? $active['stat_livenbr'] - 1 : 0;
        $paramJson = $this->paramStat->getAPI();
        $config = $this->model->getConfigActive();
        $tab = $this->model->getTabActive();
        $this->view->displayList($active, $paramJson, $config, $tab, $liveStation, $livenbr);
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

    public function tabAjaxAction()
    {
        $response =  $this->model->updateTabAjax($this->paramPost);
        if ($response) {
            $id = $this->paramPost['tab'];
            $tab = $this->model->getTabAjaxActive($id);
            $config = $this->model->getConfigActive();
            $station = $this->paramStat->getStationActive();
            $type = $station['stat_type'];
            $datas = $this->paramStat->getAPI();
            $result = $this->stationview->liveiTab($datas);
            $tab_txt = $this->stationview->tabTxt($config, $tab);
                       
            $tab_n_abc = 'tab_' . $id;
            $ntab = $tab[$tab_n_abc];
            $nexplod = explode('-', $ntab);
            $n_tab = $nexplod[0];
            $itab = $nexplod[1];

            echo $this->view->addicountTab($tab_n_abc, $itab, $n_tab, $result, $tab_txt, $type);
        } else {
            echo "BUG : Echec Modif Ajax (UpdateTab), contacter l'administrateur";
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
