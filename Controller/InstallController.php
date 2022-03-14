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
        $this->model->truncateTable('tab');
        $this->model->truncateTable('config');
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
        $liveStation = ($active['stat_type'] == 'live') ? $this->model->getLiveAPIStation($active['stat_livekey'], $active['stat_livesecret']) : '';
        $livenbr = ($active['stat_type'] == 'live') ? $active['stat_livenbr'] - 1 : 0;
        $this->view->InstallMain8($active, $paramJson, $liveStation, $livenbr);
    }

    public function step9Action()
    {      
        $response =  $this->model->InstallTrue();
        $lg = $this->l->getLg();
        if ($response) {
            header('Location:index.php?controller=pref&action=sas&lg=' . $lg);
            exit;
        }
    }





    /**
     * Système de réinstallation avec mise en place de maj futur
     * Effectif à partir de version 2.3 pour version installé inférieur à 2.3
     *
     * @return void
     */
    public function errorAction()
    {
        //on supprime toutes les tables (sauf mb_data) et le fichier admin.php
        $this->model->dropBDD();
        $this->view->InstallError();
    }


    public function majAction()
    {
        $this->view->InstallMaj();
    }
    /**
     * Système de maj
     * Effectif à partir de version 2.4 pour version installé à partir de 2.3
     *
     * @return header
     */
    public function majstepAction()
    {
        require $this->file_admin;
        $lg = $this->l->getLg();

        $version = $this->dispatcher->versionNumURL(false);
        $version_installed = (isset($version_installed)) ? floatval($version_installed) : ($version + 1);
        $active = $this->paramStat->getStationActive();


        //EXEMPLE SI SYSTEME DE PATCH AVAIT ETE MIS DES LA V2.0
        /*
        $valid1 = true;
        $valid2 = ($version_installed >= 2.1) ? true : false;
        $valid3 = ($version_installed >= 2.2) ? true : false;
        $valid4 = ($version_installed >= 2.3) ? true : false;

        //tous
        if ($version >= 2.0 && $version_installed <= $version) {
            $response1 = $this->model->InstallNo();
            $valid1 = ($response1) ? true : false;
        }
        //si installed < 2.1
        if ($version >= 2.1 && $valid1 && $version_installed <= $version && $version_installed < 2.1) {
            $response2 = $this->model->Maj20To21();
            $valid2 = ($response2) ? true : false;
        }
        //si installed < 2.2
        if ($version >= 2.2 && $valid1 && $valid2 && $version_installed <= $version && $version_installed < 2.2) {
            $response3 = $this->model->Maj21To22();
            $valid3 = ($response3) ? true : false;
        }
        //si installed < 2.3
        if ($version >= 2.3 && $valid1 && $valid2 && $valid3 && $version_installed <= $version && $version_installed < 2.3) {
            $response4 = $this->model->Maj22To23();
            $valid4 = ($response4) ? true : false;
        }
        else {
            $valid1 = $valid2 = $valid3 = $valid4 = false;
        }

        if ($valid1 && $valid2 && $valid3 && $valid4) {
            header('location:index.php?controller=install&action=step8&lg=' . $lg);
        } else {
            header('location:index.php?controller=install&action=error&lg=' . $lg);
        }*/

        //version_installed n'existe pas avant 2.3
        if (!($version_installed)) {
            header('location:index.php?controller=install&action=step8&lg=' . $lg);
        }
        //MAJ 2.3 à dernière
        elseif ($version_installed <= 2.3 && $version_installed <= $version) {
            $response1 = $this->model->Maj23To24();
            $ver = ($response1) ? 2.4 : false;
            $response2 = 1;
            $ver = ($response2) ? 2.41 : false;
            $response3 = $this->model->Maj241To242();
            $ver = ($response3) ? 2.42 : false;
            $response4a = $this->model->Maj242To25a();
            if ($response4a) {
                $this->model->truncateTable('tab');
                $response4b = $this->model->Maj242To25b();
                if ($response4b) {
                    $response4c = $this->model->addTable($active);
                    $this->model->Maj242To25c();
                }
            }
            $ver = ($response4c) ? 2.5 : false;
            $response5 = 1;
            $ver = ($response5) ? 2.51 : false;
        } elseif ($version_installed <= 2.4 && $version_installed <= $version) {
            $response2 = 1;
            $ver = ($response2) ? 2.41 : false;
            $response3 = $this->model->Maj241To242();
            $ver = ($response3) ? 2.42 : false;
            $response4a = $this->model->Maj242To25a();
            if ($response4a) {
                $this->model->truncateTable('tab');
                $response4b = $this->model->Maj242To25b();
                if ($response4b) {
                    $response4c = $this->model->addTable($active);
                    $this->model->Maj242To25c();
                }
            }
            $ver = ($response4c) ? 2.5 : false;
            $response5 = 1;
            $ver = ($response5) ? 2.51 : false;
        } elseif ($version_installed <= 2.41 && $version_installed <= $version) {
            $response3 = $this->model->Maj241To242();
            $ver = ($response3) ? 2.42 : false;
            $response4a = $this->model->Maj242To25a();
            if ($response4a) {
                $this->model->truncateTable('tab');
                $response4b = $this->model->Maj242To25b();
                if ($response4b) {
                    $response4c = $this->model->addTable($active);
                    $this->model->Maj242To25c();
                }
            }
            $ver = ($response4c) ? 2.5 : false;
            $response5 = 1;
            $ver = ($response5) ? 2.51 : false;
        } elseif ($version_installed <= 2.42 && $version_installed <= $version) {
            $response4a = $this->model->Maj242To25a();
            if ($response4a) {
                $this->model->truncateTable('tab');
                $response4b = $this->model->Maj242To25b();
                if ($response4b) {
                    $response4c = $this->model->addTable($active);
                    $this->model->Maj242To25c();
                }
            }
            $ver = ($response4c) ? 2.5 : false;
            $response5 = 1;
            $ver = ($response5) ? 2.51 : false;
        }
        elseif ($version_installed <= 2.5 && $version_installed <= $version) {
            $response5 = 1;
            $ver = ($response5) ? 2.51 : false;
        }
        if ($ver) {
            $rep = $this->model->InstallNo($ver);
            if ($rep) {
                header('location:index.php?controller=install&action=step8&lg=' . $lg);
            } else {
                header('location:index.php?controller=install&action=error&lg=' . $lg);
            }
        } else {
            header('location:index.php?controller=install&action=error&lg=' . $lg);
        }
    }
}
