<?php

class InstallView extends View
{

    public function __construct()
    {

        $this->statview = new StationView();
        parent::__construct();
    }

    public function constructHead()
    {
        $param = array(
            "META_KEY" => $this->l->trad('META_KEY'),
            "META_DESCRIPTION" => $this->l->trad('META_DESCRIPTION'),
            "MBELL_TITRE" => $this->l->trad('MBELL_TITRE_INSTALL'),
            "_CSS" => "maincolor",
            "_LOGO" => "1",
            "_ROOT" => $this->getRoot(),
            "_LG" => $this->l->getLg()
        );
        $this->page =  $this->getHead($param);
        $this->page .=  '<body>';
        $this->page .=  '<div class="container px-0">';
        $this->page .= $this->getHeader($param);
    }

    public function InstallMain8($info, $datas, $livestation)
    {
      
        $location = $this->statview->getAPIDatas($datas, $info, $livestation)['location'];
        $station_id = $this->statview->getAPIDatas($datas, $info, $livestation)['station_id'];

        $param = array(
            "1" => $info['user_login'],
            "2" => $info['user_email'],
            "3" => $info['stat_type'],
            "4" => $location,
            "5" => $station_id,
            "6" => $info['stat_did'],
            "7" => $info['stat_key'],
            "8" => $info['stat_password'],
            "9" => $info['stat_token'],
            "10" => $info['stat_livekey'],
            "11" => $info['stat_livesecret'],
            "MBELL_INSTALLED" => $this->l->trad('MBELL_INSTALLED'),
            "STATION_STEP7_P1" => $this->l->trad('STATION_STEP7_P1'),
            "USER_INFO" => $this->l->trad('USER_INFO'),
            "STATION_INFO" => $this->l->trad('STATION_INFO'),
            "VALIDATE" => $this->l->trad('VALIDATE'),
        );
        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['MBELL_INSTALLED'] . '</h1>';
        $this->page .= $this->getInfo($param['STATION_STEP7_P1']);
        $this->page .= '<h1>' . $param['USER_INFO'] . '</h1>';
        $this->page .= $this->getListInfov0($param);
        $this->page .= '<h1>' . $param['STATION_INFO'] . '</h1>';
        if ($info['stat_type'] == 'v1') {
            $this->page .= $this->getListInfov1($param);
        }
        if ($info['stat_type'] == 'v2') {
            $this->page .= $this->getListInfov2($param);
        }
        if ($info['stat_type'] == 'live') {
            $this->page .= $this->getListInfoLive($param);
        }
        $this->page .= $this->getButton($this->l->getLg(), 'install', 'step9', $param['VALIDATE']);
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function InstallStation($station, $paramGet)
    {
        $item = $paramGet['user_id'];

        $this->constructHead();
        $param = array(
            "STATION_CHOOSE" => $this->l->trad('STATION_CHOOSE'),
            "1" => $this->stationActive($station),
            "2" => $this->l->trad('CHOOSE_SELECT'),
            "3" => $this->l->trad('STATION_SELECT_V1'),
            "4" => $this->l->trad('STATION_SELECT_V2'),
            "5" => $this->l->trad('STATION_SELECT_LIVE'),
            "STATION_STEP6_P1" => $this->l->trad('STATION_STEP6_P1'),
            "STATION_STEP6_P2" => $this->l->trad('STATION_STEP6_P2'),
            "INFO_STATION" => $this->l->trad('INFO_STATION'),
            "_USER_ID" => $item,
            "_LG" => $this->l->getLg(),
            "_CONTROLLER" => 'install',
            "_ACTION" => 'step6',
            "_VAL_STAT_DID" => '',
            "_VAL_STAT_KEY" => '',
            "_VAL_STAT_USERS" => '',
            "_VAL_STAT_PASSWORD" => '',
            "_VAL_STAT_TOKEN" => '',
            "_VAL_STAT_LIVEKEY" => '',
            "_VAL_STAT_LIVESECRET" => '',
            "_VAL_STAT_LIVEID" => '',
            "_VAL_STAT_ID" => '',
            "STATION_BUTTON" => $this->l->trad('INSTALL_STEP6')

        );
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['STATION_CHOOSE'] . '</h1>';
        $this->page .= $this->getInfo($param['STATION_STEP6_P1']);
        $this->page .= $this->getInfo($param['STATION_STEP6_P2']);
        $this->page .= $this->getStationV0($param);
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['INFO_STATION'] . '</h1>';
        $this->page .= '<form action="index.php?controller=install&action=step7&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormStation($station, $param,  $param['_CONTROLLER']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function InstallMain4()
    {
        $this->constructHead();
        $param = array(
            "INFO_REQUIRED" => $this->l->trad('INFO_REQUIRED'),
            "LOGIN_STEP4_P1" => $this->l->trad('LOGIN_STEP4_P1'),
            "LOGIN_STEP4_P2" => $this->l->trad('LOGIN_STEP4_P2'),
            "1" => $this->l->trad('LOGIN_USER_LABEL'),
            "2" => $this->l->trad('USERNAME'),
            "3" => $this->l->trad('LOGIN_USER_TEXT'),
            "4" => $this->l->trad('LOGIN_PASSWORD_LABEL'),
            "5" => $this->l->trad('PASSWORD'),
            "6" => $this->l->trad('LOGIN_PASSWORD_TEXT'),
            "7" => $this->l->trad('LOGIN_EMAIL_LABEL'),
            "8" => $this->l->trad('EMAIL'),
            "9" => $this->l->trad('LOGIN_EMAIL_TEXT'),
            "LOGIN_STEP4_P3" => $this->l->trad('LOGIN_STEP4_P3'),
            "INSTALL_STEP" => $this->l->trad('CONTINUE'),
            "_LG" => $this->l->getLg(),
        );
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['INFO_REQUIRED'] . '</h1>';
        $this->page .= $this->getInfo($param['LOGIN_STEP4_P1']);
        $this->page .= $this->getInfo($param['LOGIN_STEP4_P2']);
        $this->page .= '<form action="index.php?controller=install&action=step5&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormUser($param);
        $this->page .= $this->getInfo($param['LOGIN_STEP4_P3']);
        $this->page .= $this->getSubmit('install', $param['INSTALL_STEP']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function InstallMain3No1()
    {
        $param = array(
            "1" => $this->l->trad('BDD_NO_INSTALLED'),
            "2" => $this->l->trad('BDD_STEP3_P2'),
            "3" => $this->l->trad('BDD_STEP3_P3'),
            "4" => $this->l->trad('BDD_STEP3_P4'),
            "5" => $this->l->trad('BDD_STEP3_P5'),
            "6" => $this->l->getLg(),
            "7" => 'install',
            "8" => 'step2',
            "9" => $this->l->trad('RESTART'),
        );
        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['1'] . '</h1>';
        $this->page .= $this->getInfo($param['2']);
        $this->page .= $this->getInfo($param['3']);
        $this->page .= $this->getInfo($param['4']);
        $this->page .= $this->getInfo($param['5']);
        $this->page .= $this->getButton($param['6'], $param['7'], $param['8'], $param['9']);
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function InstallMain3No2()
    {
        $param = array(
            "1" => $this->l->trad('BDD_NO_INSTALLED'),
            "2" => $this->l->trad('BDD_STEP3_P6'),
            "3" => $this->l->trad('BDD_STEP3_P7'),
            "4" => $this->l->trad('BDD_STEP3_P8'),
            "6" => $this->l->getLg(),
            "7" => 'install',
            "8" => 'step2',
            "9" => $this->l->trad('RESTART'),
        );
        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['1'] . '</h1>';
        $this->page .= $this->getInfo($param['2']);
        $this->page .= $this->getInfo($param['3']);
        $this->page .= $this->getInfo($param['4']);
        $this->page .= $this->getButton($param['6'], $param['7'], $param['8'], $param['9']);
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function InstallMain3No3()
    {
        $param = array(
            "1" => $this->l->trad('BDD_NO_INSTALLED'),
            "2" => $this->l->trad('BDD_STEP3_P9'),
            "3" => $this->l->trad('BDD_STEP3_P10'),
            "4" => $this->l->trad('ERROR_MYSQL'),
            "6" => $this->l->getLg(),
            "7" => 'install',
            "8" => 'step2',
            "9" => $this->l->trad('RESTART'),
        );
        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['1'] . '</h1>';
        $this->page .= $this->getInfo($param['2']);
        $this->page .= $this->getInfo($param['3']);
        $this->page .= $this->getInfo($param['4']);
        $this->page .= $this->getButton($param['6'], $param['7'], $param['8'], $param['9']);
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function InstallMain3Yes()
    {
        $param = array(
            "1" => $this->l->trad('BDD_INSTALLED'),
            "2" => $this->l->trad('BDD_STEP3_P1'),
            "6" => $this->l->getLg(),
            "7" => 'install',
            "8" => 'step4',
            "9" => $this->l->trad('INSTALL_STEP3'),
        );
        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['1'] . '</h1>';
        $this->page .= $this->getInfo($param['2']);
        $this->page .= $this->getButton($param['6'], $param['7'], $param['8'], $param['9']);
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }







    public function InstallMain2()
    {
        $this->constructHead();
        $param = array(
            "INFO_BDD" => $this->l->trad('INFO_BDD'),
            "BDD_STEP2_P1" => $this->l->trad('BDD_STEP2_P1'),
            "1" => $this->l->trad('BDD_HOST_LABEL'),
            "2" => $this->l->trad('LOCALHOST'),
            "3" => $this->l->trad('BDD_HOST_TEXT'),
            "4" => $this->l->trad('BDD_USER_LABEL'),
            "5" => $this->l->trad('USERNAME'),
            "6" => $this->l->trad('BDD_USER_TEXT'),
            "7" => $this->l->trad('BDD_PASSWORD_LABEL'),
            "8" => $this->l->trad('PASSWORD'),
            "9" => $this->l->trad('BDD_PASSWORD_TEXT'),
            "10" => $this->l->trad('BDD_NAME_LABEL'),
            "11" => $this->l->trad('MBELL_MIN'),
            "12" => $this->l->trad('BDD_NAME_TEXT'),
            "13" => $this->l->trad('BDD_META_LABEL'),
            "14" => $this->l->trad('TAG_MBELL'),
            "15" => $this->l->trad('BDD_META_TEXT_2'),
            "BDD_STEP2_P2" => $this->l->trad('BDD_STEP2_P2'),
            "INSTALL_STEP" => $this->l->trad('INSTALL_STEP2'),
            "_LG" => $this->l->getLg(),
        );
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['INFO_BDD'] . '</h1>';
        $this->page .= $this->getInfo($param['BDD_STEP2_P1']);
        $this->page .= '<form action="index.php?controller=install&action=step2a&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getBdd($param);
        $this->page .= $this->getInfo($param['BDD_STEP2_P2']);
        $this->page .= $this->getSubmit('install', $param['INSTALL_STEP']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function InstallMain1()
    {
        $this->constructHead();
        $param = array(
            "_IMG_DRAP_ACTIVE" => $this->langDrapActive($this->l->lgList(), $this->l->getLg()),
            "LANG_CHOOSE" => $this->l->trad('LANG_CHOOSE'),
            "1" => $this->l->trad('_LANG_TEXT'),
            "2" => $this->l->trad('CHOOSE_SELECT'),
            "3" => $this->l->trad('LANG_TEXT_EN'),
            "4" => $this->l->trad('LANG_TEXT_FR'),
            "MBELL_WELCOME" => $this->l->trad('MBELL_WELCOME'),
            "5" => $this->l->trad('BDD_HOST_LABEL'),
            "6" => $this->l->trad('BDD_USER_LABEL'),
            "7" => $this->l->trad('BDD_PASSWORD_LABEL'),
            "8" => $this->l->trad('BDD_NAME_LABEL'),
            "9" => $this->l->trad('BDD_META_TEXT_1'),
            "BDD_STEP1_P1" => $this->l->trad('BDD_STEP1_P1'),
            "BDD_STEP1_P2" => $this->l->trad('BDD_STEP1_P2'),
            "BDD_STEP1_P3" => $this->l->trad('BDD_STEP1_P3'),
            "BDD_STEP1_P4" => $this->l->trad('BDD_STEP1_P4'),
            "LINK" => $this->l->trad('INSTALL_STEP1')
        );
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['LANG_CHOOSE'] . '</h1>';
        $this->page .= $this->getLang($param, $param['_IMG_DRAP_ACTIVE']);
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['MBELL_WELCOME'] . '</h1>';
        $this->page .= $this->getInfo($param['BDD_STEP1_P1']);
        $this->page .= $this->getList($param);
        $this->page .= $this->getInfo($param['BDD_STEP1_P2']);
        $this->page .= $this->getInfo($param['BDD_STEP1_P3']);
        $this->page .= $this->getInfo($param['BDD_STEP1_P4']);
        $this->page .= $this->getButton($this->l->getLg(), 'install', 'step2', $param['LINK']);
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function getHeader($param)
    {
        $this->page .= $this->searchHTML('headerInstall', 'install');
        $this->page = str_replace('{_LOGO}',  $param['_LOGO'], $this->page);
    }


    public function getLang($param, $drap)
    {
        $this->page .= $this->searchHTML('langInstall', 'install');
        $this->page = str_replace('{_IMG_DRAP_ACTIVE}', $drap, $this->page);
        $this->page = str_replace('{_LANG_TEXT}',  $param['1'], $this->page);
        $this->page = str_replace('{CHOOSE_SELECT}',  $param['2'], $this->page);
        $this->page = str_replace('{LANG_TEXT_EN}',  $param['3'], $this->page);
        $this->page = str_replace('{LANG_TEXT_FR}',  $param['4'], $this->page);
    }



    public function getBdd($param)
    {
        $this->page .= $this->searchHTML('bddInstall', 'install');
        $this->page = str_replace('{BDD_HOST_LABEL}',  $param['1'], $this->page);
        $this->page = str_replace('{LOCALHOST}',  $param['2'], $this->page);
        $this->page = str_replace('{BDD_HOST_TEXT}',  $param['3'], $this->page);
        $this->page = str_replace('{BDD_USER_LABEL}',  $param['4'], $this->page);
        $this->page = str_replace('{USERNAME}',  $param['5'], $this->page);
        $this->page = str_replace('{BDD_USER_TEXT}',  $param['6'], $this->page);
        $this->page = str_replace('{BDD_PASSWORD_LABEL}',  $param['7'], $this->page);
        $this->page = str_replace('{PASSWORD}',  $param['8'], $this->page);
        $this->page = str_replace('{BDD_PASSWORD_TEXT}',  $param['9'], $this->page);
        $this->page = str_replace('{BDD_NAME_LABEL}',  $param['10'], $this->page);
        $this->page = str_replace('{MBELL_MIN}',  $param['11'], $this->page);
        $this->page = str_replace('{BDD_NAME_TEXT}',  $param['12'], $this->page);
        $this->page = str_replace('{BDD_META_LABEL}',  $param['13'], $this->page);
        $this->page = str_replace('{TAG_MBELL}',  $param['14'], $this->page);
        $this->page = str_replace('{BDD_META_TEXT_2}',  $param['15'], $this->page);
    }

    public function getFormUser($param)
    {
        $this->page .= $this->searchHTML('userInstall', 'install');
        $this->page = str_replace('{LOGIN_USER_LABEL}',  $param['1'], $this->page);
        $this->page = str_replace('{USERNAME}',  $param['2'], $this->page);
        $this->page = str_replace('{LOGIN_USER_TEXT}',  $param['3'], $this->page);
        $this->page = str_replace('{LOGIN_PASSWORD_LABEL}',  $param['4'], $this->page);
        $this->page = str_replace('{PASSWORD}',  $param['5'], $this->page);
        $this->page = str_replace('{LOGIN_PASSWORD_TEXT}',  $param['6'], $this->page);
        $this->page = str_replace('{LOGIN_EMAIL_LABEL}',  $param['7'], $this->page);
        $this->page = str_replace('{EMAIL}',  $param['8'], $this->page);
        $this->page = str_replace('{LOGIN_EMAIL_TEXT}',  $param['9'], $this->page);
    }




    public function getList($param)
    {
        $this->page .= $this->searchHTML('listInstall', 'install');
        $this->page = str_replace('{BDD_HOST_LABEL}',  $param['5'], $this->page);
        $this->page = str_replace('{BDD_USER_LABEL}',  $param['6'], $this->page);
        $this->page = str_replace('{BDD_PASSWORD_LABEL}',  $param['7'], $this->page);
        $this->page = str_replace('{BDD_NAME_LABEL}',  $param['8'], $this->page);
        $this->page = str_replace('{BDD_META_LABEL}',  $param['9'], $this->page);
    }
}
