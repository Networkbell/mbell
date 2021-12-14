<?php

/**
 * 
 * 
 * @method View
 * @abstract 
 * 
 * @return void
 */




abstract class View
{


    protected $page;  
    protected $col;
    protected $moon;

    public function __construct()
    {

        $this->l = new Lang();
        $this->col = new Color();


        if (isset($_SESSION['username'])) {
            $logout = "<a href='index.php?controller=login&action=logout' title='Déconnexion'><i class='fas fa-sign-out-alt'></i></a>";
        }

    }

    /**
     * Lien vers racine du site 
     * 
     * @return string
     */
    public function getRoot()
    {
    $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
    return $root;
    }


    /**
     * Construction du Header
     * 
     * @return void
     */
    public function getHead($param)
    {
        $this->page = $this->searchHTML('head', '');
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
        $this->page = str_replace('{META_KEY}',  $param['META_KEY'], $this->page);
        $this->page = str_replace('{META_DESCRIPTION}',  $param['META_DESCRIPTION'], $this->page);
        $this->page = str_replace('{MBELL_TITRE}',  $param['MBELL_TITRE'], $this->page);
        $this->page = str_replace('{_CSS}',  $param['_CSS'], $this->page);
        echo $this->page;
    }


    /**
     * Construction des drapeaux de langues
     * 
     * @return void
     */
    public function langDrapMenu($array, $lg)
    {
        $page = '<div class="drap2">';
        $numb = '2';
        foreach ($array as $key => $lang) {
            $class = ($lang == $lg) ? 'class="active float_left"' : 'class="inactive float_right"';
            $page .= '<a href="index.php?lg=' . $lang . '" ' . $class . '>';
            $page .= '<img src="images/' . $lang . $numb . '.png" alt="' . $lang . '" title="' . $lang . '" />';
            $page .= '</a>';
        }
        $page .= '</div>';
        return $page;
    }


    public function langDrapActive($array, $lg)
    {


        $numb = '2';
        foreach ($array as $key => $lang) {
            if ($lang == $lg) {
                $page = '<a href="index.php?lg=' . $lang . '" class="active">';
                $page .= '<img src="images/' . $lang . $numb . '.png" alt="' . $lang . '" title="' . $lang . '" />';
                $page .= '</a>';
            }
        }

        return $page;
    }

    public function stationActive($station)
    {

        switch ($station) {
            case 'v0':
                $page = $this->l->trad('NONE_CHOSEN');
                break;
            case 'v1':
                $page = $this->l->trad('STATION_SELECT_V1');;
                break;
            case 'v2':
                $page = $this->l->trad('STATION_SELECT_V2');;
                break;
            default:
                $page = $this->l->trad('NONE_CHOSEN');
        }

        return $page;
    }



    /**
     * Construction du bouton lien
     * 
     * @return void
     */
    public function getButton($lg, $controll, $action, $link)
    {
        $this->page .= $this->searchHTML('button', '');
        $this->page = str_replace('{_LG}',  $lg, $this->page);
        $this->page = str_replace('{_CONTROLL}',  $controll, $this->page);
        $this->page = str_replace('{_ACTION}',  $action, $this->page);
        $this->page = str_replace('{LINK}',  $link, $this->page);
    }

    /**
     * Construction du bouton submit
     * 
     * @return void
     */
    public function getSubmit($controll, $text)
    {
        $this->page .= $this->searchHTML('submit', '');
        $this->page = str_replace('{_CONTROLL}',  $controll, $this->page);
        $this->page = str_replace('{LINK}',  $text, $this->page);
    }



    /**
     * Construction du pied de page
     * 
     * @return void
     */
    public function display()
    {
        $this->page .= $this->searchHTML('footer', '');
        $this->page = str_replace('{BY}',  $this->l->trad('BY'), $this->page);
        $this->page = str_replace('{DATA_FROM}',  $this->l->trad('DATA_FROM'), $this->page);
        $this->page = str_replace('{_YEAR}',  date("Y"), $this->page);
        echo $this->page;
    }


    /**
     * Mets les fichiers du dossier template en string
     * 
     * @return string
     */
    public function searchHTML($files, $folder)
    {
        if ($folder != '') {
            $search = file_get_contents('template/' . $folder . '/' . $files . '.html');
        } else {
            $search = file_get_contents('template/' . $files . '.html');
        }
        return $search;
    }


    public function getInfo($info)
    {
        $this->page .= $this->searchHTML('infoText', '');
        $this->page = str_replace('{INFO}',  $info, $this->page);
    }


    public function getListInfov0($param)
    {
        $this->page .= $this->searchHTML('listInfo', '');
        $this->page = str_replace('{_INFO_LOGIN}',  $param['1'], $this->page);
        $this->page = str_replace('{_INFO_EMAIL}',  $param['2'], $this->page);
        $this->page = str_replace('{LOGIN_USER_LABEL}',  $this->l->trad('LOGIN_USER_LABEL'), $this->page);
        $this->page = str_replace('{LOGIN_PASSWORD_LABEL}',  $this->l->trad('LOGIN_PASSWORD_LABEL'), $this->page);
        $this->page = str_replace('{LOGIN_EMAIL_LABEL}',  $this->l->trad('LOGIN_EMAIL_LABEL'), $this->page);
    }

    public function getListInfov1($param)
    {
        $this->page .= $this->searchHTML('listInfov1', '');
        $this->page = str_replace('{_STATION_TYPE}',  $param['3'], $this->page);
        $this->page = str_replace('{_STATION_LOCATION}',  $param['4'], $this->page);
        $this->page = str_replace('{_STATION_USER}',  $param['5'], $this->page);
        $this->page = str_replace('{_STATION_DID}',  $param['6'], $this->page);
        $this->page = str_replace('{_STATION_KEY}',  $param['7'], $this->page);
        $this->page = str_replace('{STATION_TYPE}',  $this->l->trad('STATION_TYPE'), $this->page);
        $this->page = str_replace('{STATION_LOCATION}',  $this->l->trad('STATION_LOCATION'), $this->page);
        $this->page = str_replace('{STATION_USER}',  $this->l->trad('STATION_USER'), $this->page);
        $this->page = str_replace('{STATION_DID}',  'DID', $this->page);
        $this->page = str_replace('{STATION_KEY}',  'KEY', $this->page);
    }

    public function getListInfov2($param)
    {
        $this->page .= $this->searchHTML('listInfov2', '');
        $this->page = str_replace('{_STATION_TYPE}',  $param['3'], $this->page);
        $this->page = str_replace('{_STATION_LOCATION}',  $param['4'], $this->page);
        $this->page = str_replace('{_STATION_USER}',  $param['5'], $this->page);
        $this->page = str_replace('{_STATION_DID}',  $param['6'], $this->page);
        $this->page = str_replace('{_STATION_PASSWORD}',  $param['8'], $this->page);
        $this->page = str_replace('{_STATION_TOKEN}',  $param['9'], $this->page);
        $this->page = str_replace('{STATION_TYPE}',  $this->l->trad('STATION_TYPE'), $this->page);
        $this->page = str_replace('{STATION_LOCATION}',  $this->l->trad('STATION_LOCATION'), $this->page);
        $this->page = str_replace('{STATION_USER}',  $this->l->trad('STATION_USER'), $this->page);
        $this->page = str_replace('{STATION_DID}',  'DID', $this->page);
        $this->page = str_replace('{STATION_PASSWORD}',  $this->l->trad('DAVIS_PASS'), $this->page);
        $this->page = str_replace('{STATION_TOKEN}',  $this->l->trad('DAVIS_TOKEN'), $this->page);
    }
    public function getListInfoLive($param)
    {
        $this->page .= $this->searchHTML('listInfolive', '');
        $this->page = str_replace('{_STATION_TYPE}',  $param['3'], $this->page);
        $this->page = str_replace('{_STATION_LOCATION}',  $param['4'], $this->page);
        $this->page = str_replace('{_STATION_USER}',  $param['5'], $this->page);        
        $this->page = str_replace('{_STATION_LIVEKEY}',  $param['10'], $this->page);
        $this->page = str_replace('{_STATION_LIVESECRET}',  $param['11'], $this->page);
        $this->page = str_replace('{STATION_TYPE}',  $this->l->trad('STATION_TYPE'), $this->page);
        $this->page = str_replace('{STATION_LOCATION}',  $this->l->trad('STATION_LOCATION'), $this->page);
        $this->page = str_replace('{STATION_USER}',  $this->l->trad('STATION_USER'), $this->page);
        $this->page = str_replace('{STATION_LIVEKEY}',  'API V2', $this->page);
        $this->page = str_replace('{STATION_LIVESECRET}',  'API SECRET', $this->page);
    }

    public function getFormStation($station, $param, $controll)
    {
        switch ($station) {
            case 'v0':
                $page = $this->getInfo($this->l->trad('STATION_STEP6_P3'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P4'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P5'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P6'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P7'));
                break;
            case 'v1':
                $page = $this->getStationV1($param);
                $page .= $this->getSubmit($controll, $param['STATION_BUTTON']);
                break;
            case 'v2':
                $page = $this->getStationV2($param);
                $page .= $this->getSubmit($controll, $param['STATION_BUTTON']);
                break;
            case 'live':
                    $page = $this->getStationLive($param);
                    $page .= $this->getSubmit($controll, $param['STATION_BUTTON']);
                    break;
            default:
                $page = $this->getInfo($this->l->trad('STATION_STEP6_P3'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P4'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P5'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P6'));
                $page .= $this->getInfo($this->l->trad('STATION_STEP6_P7'));
        }
        return $page;
    }



    public function getStationV0($param)
    {
        $this->page .= $this->searchHTML('stationInstall', 'install');
        $this->page = str_replace('{_STATION_CHOICE_SELECT}',  $param['1'], $this->page);
        $this->page = str_replace('{CHOOSE_SELECT}',  $param['2'], $this->page);
        $this->page = str_replace('{STATION_SELECT_V1}',  $param['3'], $this->page);
        $this->page = str_replace('{STATION_SELECT_V2}',  $param['4'], $this->page);
        $this->page = str_replace('{STATION_SELECT_LIVE}',  $param['5'], $this->page);        
        $this->page = str_replace('{ANY}',  $this->l->trad('ANY'), $this->page);
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
        $this->page = str_replace('{_CONTROLLER}',  $param['_CONTROLLER'], $this->page);
        $this->page = str_replace('{_ACTION}',  $param['_ACTION'], $this->page);          
    }


    public function getStationV1($param)
    {
        $this->page .= $this->searchHTML('stationv1', 'install');
        $this->page = str_replace('{DAVIS_DID}',  $this->l->trad('DAVIS_DID'), $this->page);
        $this->page = str_replace('{UPPERCASE}',  $this->l->trad('UPPERCASE'), $this->page);
        $this->page = str_replace('{DAVIS_DID_TEXT}',  $this->l->trad('DAVIS_DID_TEXT'), $this->page);
        $this->page = str_replace('{DAVIS_KEY}',  $this->l->trad('DAVIS_KEY'), $this->page);
        $this->page = str_replace('{CASE_SENSITIVE}',  $this->l->trad('CASE_SENSITIVE'), $this->page);
        $this->page = str_replace('{DAVIS_KEY_TEXT}',  $this->l->trad('DAVIS_KEY_TEXT'), $this->page);
        $this->page = str_replace('{_USER_ID}',  $param['_USER_ID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_DID}',  $param['_VAL_STAT_DID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_KEY}',  $param['_VAL_STAT_KEY'], $this->page);
        $this->page = str_replace('{_VAL_STAT_USERS}',  $param['_VAL_STAT_USERS'], $this->page);
        $this->page = str_replace('{_VAL_STAT_PASSWORD}',  $param['_VAL_STAT_PASSWORD'], $this->page);
        $this->page = str_replace('{_VAL_STAT_TOKEN}',  $param['_VAL_STAT_TOKEN'], $this->page);
        $this->page = str_replace('{_VAL_STAT_ID}',  $param['_VAL_STAT_ID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVEKEY}',  $param['_VAL_STAT_LIVEKEY'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVESECRET}',  $param['_VAL_STAT_LIVESECRET'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVEID}',  $param['_VAL_STAT_LIVEID'], $this->page);
    }
    public function getStationV2($param)
    {
        $this->page .= $this->searchHTML('stationv2', 'install');
        $this->page = str_replace('{CASE_SENSITIVE}',  $this->l->trad('CASE_SENSITIVE'), $this->page);
        $this->page = str_replace('{UPPERCASE}',  $this->l->trad('UPPERCASE'), $this->page);
        $this->page = str_replace('{DAVIS_DID}',  $this->l->trad('DAVIS_DID'), $this->page);
        $this->page = str_replace('{DAVIS_DID_TEXT}',  $this->l->trad('DAVIS_DID_TEXT'), $this->page);
        $this->page = str_replace('{DAVIS_PASS}',  $this->l->trad('DAVIS_PASS'), $this->page);
        $this->page = str_replace('{DAVIS_PASS_TEXT}',  $this->l->trad('DAVIS_PASS_TEXT'), $this->page);
        $this->page = str_replace('{DAVIS_TOKEN}',  $this->l->trad('DAVIS_TOKEN'), $this->page);
        $this->page = str_replace('{DAVIS_TOKEN_TEXT}',  $this->l->trad('DAVIS_TOKEN_TEXT'), $this->page);
        $this->page = str_replace('{_USER_ID}',  $param['_USER_ID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_DID}',  $param['_VAL_STAT_DID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_KEY}',  $param['_VAL_STAT_KEY'], $this->page);
        $this->page = str_replace('{_VAL_STAT_USERS}',  $param['_VAL_STAT_USERS'], $this->page);
        $this->page = str_replace('{_VAL_STAT_PASSWORD}',  $param['_VAL_STAT_PASSWORD'], $this->page);
        $this->page = str_replace('{_VAL_STAT_TOKEN}',  $param['_VAL_STAT_TOKEN'], $this->page);
        $this->page = str_replace('{_VAL_STAT_ID}',  $param['_VAL_STAT_ID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVEKEY}',  $param['_VAL_STAT_LIVEKEY'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVESECRET}',  $param['_VAL_STAT_LIVESECRET'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVEID}',  $param['_VAL_STAT_LIVEID'], $this->page);
    }
    public function getStationLive($param)
    {
        $this->page .= $this->searchHTML('stationlive', 'install');
        $this->page = str_replace('{CASE_SENSITIVE}',  $this->l->trad('CASE_SENSITIVE'), $this->page);
       /* $this->page = str_replace('{UPPERCASE}',  $this->l->trad('UPPERCASE'), $this->page);*/
        $this->page = str_replace('{DAVIS_LIVEKEY}',  $this->l->trad('DAVIS_LIVEKEY'), $this->page);
        $this->page = str_replace('{DAVIS_LIVEKEY_TEXT}',  $this->l->trad('DAVIS_LIVEKEY_TEXT'), $this->page);
        $this->page = str_replace('{DAVIS_LIVESECRET}',  $this->l->trad('DAVIS_LIVESECRET'), $this->page);
        $this->page = str_replace('{DAVIS_LIVESECRET_TEXT}',  $this->l->trad('DAVIS_LIVESECRET_TEXT'), $this->page);
        $this->page = str_replace('{_USER_ID}',  $param['_USER_ID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_DID}',  $param['_VAL_STAT_DID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_KEY}',  $param['_VAL_STAT_KEY'], $this->page);
        $this->page = str_replace('{_VAL_STAT_USERS}',  $param['_VAL_STAT_USERS'], $this->page);
        $this->page = str_replace('{_VAL_STAT_PASSWORD}',  $param['_VAL_STAT_PASSWORD'], $this->page);
        $this->page = str_replace('{_VAL_STAT_TOKEN}',  $param['_VAL_STAT_TOKEN'], $this->page);
        $this->page = str_replace('{_VAL_STAT_ID}',  $param['_VAL_STAT_ID'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVEKEY}',  $param['_VAL_STAT_LIVEKEY'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVESECRET}',  $param['_VAL_STAT_LIVESECRET'], $this->page);
        $this->page = str_replace('{_VAL_STAT_LIVEID}',  $param['_VAL_STAT_LIVEID'], $this->page);
    }
}
