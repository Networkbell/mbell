<?php
class PrefView extends View
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
            "MBELL_TITRE" => $this->l->trad('MBELL_TITRE_PREF'),
            "_CSS" => "maincolor",
            "_LOGO" => "1",
            "_ROOT" => $this->getRoot(),
            "_URL" => 'index.php?',
            "_LG" => $this->l->getLg()
        );
        $this->page =  $this->getHead($param);
        $this->page .=  '<body>';
        $this->page .=  '<div class="container px-0">';
        $this->page .= $this->getHeader($param);
    }



    public function displayList($info, $datas, $config, $tab, $livestation)
    {
        $location = $this->statview->getAPIDatas($datas, $info, $livestation)['location'];
        $station_id = $this->statview->getAPIDatas($datas, $info, $livestation)['station_id'];

        $param = array(
            "config_id" => $config['config_id'],
            "tab_id" => $tab['tab_id'],
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
            "12" => $this->versionInstalled() . $this->versionCompare(),
            "_LG" => $this->l->getLg(),
            "SAVE" => $this->l->trad('SAVE'),
            "CHANGE_STATION" => $this->l->trad('CHANGE_STATION'),
            "CRON_TITLE" => $this->l->trad('CRON_TITLE'),
        );

        $this->constructHead();
        $this->page .= '<main id="main_pref">';
        $this->page .= '<section>';
        $this->page .= $this->titleMenu($this->l->trad('USER_TITLE'), '0');
        $this->page .= '<div class="section_show" id="show_hide0">';
        $this->page .= $this->getListInfov0($param);
        if (!($this->isVersionCompare())) {
            $this->page .= $this->getButton($this->l->getLg(), 'pref', 'patch', $this->l->trad('MAJ_MBELL'));
        }
        $this->page .= '</div>';

        $this->page .= $this->titleMenu($this->l->trad('STATION_TITLE'), '1');
        $this->page .= '<div class="section_show" id="show_hide1">';
        if ($info['stat_type'] == 'v1') {
            $this->page .= $this->getListInfov1($param);
        } elseif ($info['stat_type'] == 'v2') {
            $this->page .= $this->getListInfov2($param);
        } elseif ($info['stat_type'] == 'live') {
            $this->page .= $this->getListInfoLive($param);
        } elseif ($info['stat_type'] == 'weewx') {
            $this->page .= $this->getListInfoWx($param);
        }
        $this->page .= $this->getButton($this->l->getLg(), 'change', 'list', $param['CHANGE_STATION']);
        $this->page .= '</div>';
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= $this->titleMenu($this->l->trad('OPTION_TITLE'), '2');
        $this->page .= '<div class="section_show" id="show_hide2">';
        $this->page .= '<form action="index.php?controller=pref&action=config&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getOptions($param, $config);
        $this->page .= $this->getSubmit('pref', $param['SAVE']);
        $this->page .= '</form>';
        $this->page .= '</div>';
        $this->page .= $this->titleMenu($this->l->trad('DEFAULT_CHOICE'), '3');
        $this->page .= '<div class="section_show" id="show_hide3">';
        $this->page .= '<form action="index.php?controller=pref&action=default&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getDefault($param, $config);
        $this->page .= $this->getSubmit('pref', $param['SAVE']);
        $this->page .= '</form>';
        $this->page .= '</div>';
        $this->page .= $this->titleMenu($this->l->trad('CONFIGURATION_TITLE'), '4');
        $this->page .= '<div class="section_show" id="show_hide4">';
        $this->page .= '<form action="index.php?controller=pref&action=lines&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getLines($param, $tab);
        $this->page .= '</form>';
        $this->page .= '<form action="index.php?controller=pref&action=tab&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= '<div class="container-fluid conteneur_row_pref px-0">';
        $this->page .= $this->getTab($param, $tab, $this->statview->tabTxt($config), $this->statview->optionValue($config), $config);
        $this->page .= '</div>';
        $this->page .= $this->getSubmit('pref', $param['SAVE']);
        $this->page .= '</form>';
        $this->page .= '</div>';
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= $this->titleMenu($this->l->trad('MANAGMENT'), '5');
        $this->page .= '<div class="section_show" id="show_hide5">';
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_1'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_2'));

        $this->page .= $this->getButton($this->l->getLg(), 'cron', 'list', $param['CRON_TITLE']);
        $this->page .= '</div>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }



    public function getTab($param, $tab, $tab_txt, $options, $config)
    {
        $page = '';
        for ($i = 1; $i <= $tab['tab_lines']; $i++) {
            $page .=  $this->addRow($param, $tab, $i, $tab_txt, $options, $config);
        }
        return $page;
    }

    public function addRow($param, $tab, $row_id, $tab_txt, $options, $config)
    {
        $page = '<div class="row row_pref" id="tab_row_' . $row_id . '">';
        $page .= $this->getSelect($param, $tab, $row_id, 'a', $tab_txt, $options, $config);
        $page .= $this->getSelect($param, $tab, $row_id, 'b', $tab_txt, $options, $config);
        $page .= $this->getSelect($param, $tab, $row_id, 'c', $tab_txt, $options, $config);
        $page .= '</div>';
        return $page;
    }

    public function getSelect($param, $tab, $row_id, $select_id, $tab_txt, $options, $config)
    {
        $result = 'tab_' . $row_id . $select_id;

        $page = $this->searchHTML('tabSelect', 'pref');
        $page = str_replace('{CHOOSE_SELECT}',  $this->l->trad('CHOOSE_SELECT'), $page);
        $page = str_replace('{ANY}',  $this->l->trad('ANY'), $page);
        $page = str_replace('{_ID_ROW}',  $row_id, $page);
        $page = str_replace('{_ID_SELECT}',  $select_id, $page);
        $page = str_replace('{tab_id}',  $param['tab_id'], $page);
        $page = str_replace('{_OPTIONS}',  $this->addSelect($options, $config), $page);
        $page = str_replace('{_TAB_CHOICE_SELECT_TXT}',  $tab_txt[$tab[$result]]['txt'], $page);
        $page = str_replace('{_TAB_CHOICE_SELECT_TEXT}',  $tab_txt[$tab[$result]]['text'], $page);
        $page = str_replace('{_VAL_ACTU}',  $tab[$result], $page);
        return $page;
    }



    public function addSelect($options, $config)
    {
        $addOptions = '';
        $i = 0;
        $arr = array();

        //array_push additionne dans un tableau vide $arr les valeurs sélectionnés

        //si les options dans config[] sont absentes on filtre
        if ($config['config_sun'] == 'sun') {
            array_push($arr, '23');
        }
        if ($config['config_sun'] == 'uv') {
            array_push($arr, '22');
        }
        if ($config['config_sun'] == '') {
            array_push($arr, '22', '23');
        }
        if ($config['config_aux1'] == '0' && $config['config_aux2'] != '1') {
            array_push($arr, '15', '16', '17', '18', '19', '20', '21');
        }
        if ($config['config_aux2'] == '0') {
            array_push($arr, '30', '31', '32', '33', '34', '35', '36');
        }
        if ($config['config_aux3'] == '0') {
            array_push($arr, '24', '25', '26', '27', '28', '29', '37', '38', '39', '40', '41', '42');
        }

        //permet de filtrer le tableau $options en enlevant $arr (définis comme clefs)
        $result = array_diff_key($options, array_flip($arr));

        foreach ($result as $key => $values) {
            $addOptions .= '<option class="small1000" value="' . $key . '">' . ++$i . '- ' . $values['txt'] . '</option>';
            $addOptions .= '<option class="large1000" value="' . $key . '">' . $i . '- ' . $values['text'] . '</option>';
        }
        return $addOptions;
    }





    public function getHeader($param)
    {
        $this->page .= $this->searchHTML('headerPref', 'pref');
        $this->page = str_replace('{_LOGO}',  $param['_LOGO'], $this->page);
        $this->page = str_replace('{_ROOT}',  $param['_ROOT'], $this->page);
        $this->page = str_replace('{_URL}',  $param['_URL'], $this->page);
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
        $this->page = str_replace('{HOMEPAGE}',  $this->l->trad('HOMEPAGE'), $this->page);
        $this->page = str_replace('{HOMESCREEN}',  $this->l->trad('HOMESCREEN'), $this->page);
        $this->page = str_replace('{LOGOUT1}',  $this->l->trad('LOGOUT1'), $this->page);
        $this->page = str_replace('{LOGOUT2}',  $this->l->trad('LOGOUT2'), $this->page);
    }


    public function titleMenu($title, $id)
    {
        $this->page .= $this->searchHTML('titlePref', 'pref');
        $this->page = str_replace('{TITLE}',  $title, $this->page);
        $this->page = str_replace('{_ID}',  $id, $this->page);
    }

    public function getOptions($param, $config)
    {
        $this->page .= $this->searchHTML('optionsForm', 'pref');
        $this->page = str_replace('{config_id}',  $param['config_id'], $this->page);
        $this->page = str_replace('{para1}',  $this->checkOptions('1', $config), $this->page);
        $this->page = str_replace('{para2}',  $this->checkOptions('2', $config), $this->page);
        $this->page = str_replace('{para3}',  $this->checkOptions('3', $config), $this->page);
        $this->page = str_replace('{para4}',  $this->checkOptions('4', $config), $this->page);
        $this->page = str_replace('{para5}',  $this->checkOptions('5', $config), $this->page);
        $this->page = str_replace('{SOLAR_AUX_1}',  $this->l->trad('SOLAR_AUX_1'), $this->page);
        $this->page = str_replace('{SOLAR_AUX_2}',  $this->l->trad('SOLAR_AUX_2'), $this->page);
        $this->page = str_replace('{UV_AUX_1}',  $this->l->trad('UV_AUX_1'), $this->page);
        $this->page = str_replace('{UV_AUX_2}',  $this->l->trad('UV_AUX_2'), $this->page);
        $this->page = str_replace('{AIR_WATER_GROUND_1}',  $this->l->trad('AIR_WATER_GROUND_1'), $this->page);
        $this->page = str_replace('{AIR_WATER_GROUND_2}',  $this->l->trad('AIR_WATER_GROUND_2'), $this->page);
        $this->page = str_replace('{AIR_AUX_1}',  $this->l->trad('AIR_AUX_1'), $this->page);
        $this->page = str_replace('{AIR_AUX_2}',  $this->l->trad('AIR_AUX_2'), $this->page);
        $this->page = str_replace('{LEAF_SOIL_1}',  $this->l->trad('LEAF_SOIL_1'), $this->page);
        $this->page = str_replace('{LEAF_SOIL_2}',  $this->l->trad('LEAF_SOIL_2'), $this->page);
    }

    public function getDefault($param, $config)
    {
        $this->page .= $this->searchHTML('defaultForm', 'pref');
        $this->page = str_replace('{config_id}',  $param['config_id'], $this->page);
        $this->page = str_replace('{para1}',  $this->checkDefault('1', $config), $this->page);
        $this->page = str_replace('{para2}',  $this->checkDefault('2', $config), $this->page);
        $this->page = str_replace('{para3}',  $this->checkDefault('3', $config), $this->page);
        $this->page = str_replace('{para4}',  $this->checkDefault('4', $config), $this->page);
        $this->page = str_replace('{para5}',  $this->checkDefault('5', $config), $this->page);
        $this->page = str_replace('{para6}',  $this->checkDefault('6', $config), $this->page);
        $this->page = str_replace('{para7}',  $this->checkDefault('7', $config), $this->page);
        $this->page = str_replace('{para8}',  $this->checkDefault('8', $config), $this->page);
        $this->page = str_replace('{para9}',  $this->checkDefault('9', $config), $this->page);
        $this->page = str_replace('{para10}',  $this->checkDefault('10', $config), $this->page);
        $this->page = str_replace('{para11}',  $this->checkDefault('11', $config), $this->page);
        $this->page = str_replace('{para12}',  $this->checkDefault('12', $config), $this->page);
        $this->page = str_replace('{para13}',  $this->checkDefault('13', $config), $this->page);
        $this->page = str_replace('{para14}',  $this->checkDefault('14', $config), $this->page);
        $this->page = str_replace('{para15}',  $this->checkDefault('15', $config), $this->page);
        $this->page = str_replace('{para16}',  $this->checkDefault('16', $config), $this->page);
        $this->page = str_replace('{para17}',  $this->checkDefault('17', $config), $this->page);
        $this->page = str_replace('{para18}',  $this->checkDefault('18', $config), $this->page);
        $this->page = str_replace('{para19}',  $this->checkDefault('19', $config), $this->page);
        $this->page = str_replace('{para20}',  $this->checkDefault('20', $config), $this->page);
        $this->page = str_replace('{para21}',  $this->checkDefault('21', $config), $this->page);
        $this->page = str_replace('{LANGUAGE}',  $this->l->trad('LANGUAGE'), $this->page);
        $this->page = str_replace('{TEMPERATURE}',  $this->l->trad('TEMPERATURE'), $this->page);
        $this->page = str_replace('{WIND}',  $this->l->trad('WIND'), $this->page);
        $this->page = str_replace('{RAIN}',  $this->l->trad('RAIN'), $this->page);
        $this->page = str_replace('{PRESSURE}',  $this->l->trad('PRESSURE'), $this->page);
        $this->page = str_replace('{DESIGN}',  $this->l->trad('STYLES'), $this->page);
        $this->page = str_replace('{DAY_NIGHT}',  $this->l->trad('DAY_NIGHT'), $this->page);
        $this->page = str_replace('{COLOR}',  $this->l->trad('COLOR'), $this->page);
        $this->page = str_replace('{ICONS}',  $this->l->trad('ICONS'), $this->page);
        $this->page = str_replace('{ENGLISH}',  $this->l->trad('LANG_TEXT_EN'), $this->page);
        $this->page = str_replace('{FRENCH}',  $this->l->trad('LANG_TEXT_FR'), $this->page);
        $this->page = str_replace('{CELSIUS}',  $this->l->trad('CELSIUS'), $this->page);
        $this->page = str_replace('{FAHRENHEIT}',  $this->l->trad('FAHRENHEIT'), $this->page);
        $this->page = str_replace('{KPH}',  $this->l->trad('KPH'), $this->page);
        $this->page = str_replace('{MPH}',  $this->l->trad('MPH'), $this->page);
        $this->page = str_replace('{MM}',  $this->l->trad('MM'), $this->page);
        $this->page = str_replace('{IN}',  $this->l->trad('IN'), $this->page);
        $this->page = str_replace('{HPA}',  $this->l->trad('HPA'), $this->page);
        $this->page = str_replace('{INHG}',  $this->l->trad('INHG'), $this->page);
        $this->page = str_replace('{DARK_BLUE}',  $this->l->trad('DARK_BLUE'), $this->page);
        $this->page = str_replace('{LIGHT_BLUE}',  $this->l->trad('LIGHT_BLUE'), $this->page);
        $this->page = str_replace('{BLACK}',  $this->l->trad('BLACK'), $this->page);
        $this->page = str_replace('{WHITE}',  $this->l->trad('WHITE'), $this->page);
        $this->page = str_replace('{ON}',  $this->l->trad('ON'), $this->page);
        $this->page = str_replace('{OFF}',  $this->l->trad('OFF'), $this->page);
        $this->page = str_replace('{NEUTRAL}',  $this->l->trad('NEUTRAL'), $this->page);
        $this->page = str_replace('{COLORED}',  $this->l->trad('COLORED'), $this->page);
        $this->page = str_replace('{DYNAMIC}',  $this->l->trad('DYNAMIC'), $this->page);
        $this->page = str_replace('{YES}',  $this->l->trad('YES'), $this->page);
        $this->page = str_replace('{NO}',  $this->l->trad('NO'), $this->page);
    }

    public function getLines($param, $tab)
    {
        $this->page .= $this->searchHTML('linesForm', 'pref');
        $this->page = str_replace('{tab_id}',  $param['tab_id'], $this->page);
        $this->page = str_replace('{para1}',  $this->checkLines('1', $tab), $this->page);
        $this->page = str_replace('{para2}',  $this->checkLines('2', $tab), $this->page);
        $this->page = str_replace('{para3}',  $this->checkLines('3', $tab), $this->page);
        $this->page = str_replace('{para4}',  $this->checkLines('4', $tab), $this->page);
        $this->page = str_replace('{para5}',  $this->checkLines('5', $tab), $this->page);
        $this->page = str_replace('{para6}',  $this->checkLines('6', $tab), $this->page);
        $this->page = str_replace('{para7}',  $this->checkLines('7', $tab), $this->page);
        $this->page = str_replace('{para8}',  $this->checkLines('8', $tab), $this->page);
        $this->page = str_replace('{para9}',  $this->checkLines('9', $tab), $this->page);
        $this->page = str_replace('{para10}',  $this->checkLines('10', $tab), $this->page);
        $this->page = str_replace('{LINE}',  $this->l->trad('LINE'), $this->page);
    }



    public function checkLines($id, $tab)
    {
        if ($tab['tab_lines'] == $id) {
            $checked = 'checked';
            return $checked;
        }
    }


    public function checkOptions($id, $config)
    {
        switch ($id) {
            case '1':
                $checked = ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? 'checked' : '';
                break;
            case '2':
                $checked = ($config['config_sun'] == 'uv' || $config['config_sun'] == 'sun_uv') ? 'checked' : '';
                break;
            case '3':
                $checked = ($config['config_aux1'] == 1) ? 'checked' : '';
                break;
            case '4':
                $checked = ($config['config_aux2'] == 1) ? 'checked' : '';
                break;
            case '5':
                $checked = ($config['config_aux3'] == 1) ? 'checked' : '';
                break;
            default:
                $checked = '';
        }
        return $checked;
    }

    public function checkDefault($id, $config)
    {
        switch ($id) {
            case '1':
                $checked = ($config['config_lang'] == 'en') ? 'checked' : '';
                break;
            case '2':
                $checked = ($config['config_lang'] == 'fr') ? 'checked' : '';
                break;
            case '3':
                $checked = ($config['config_temp'] == 'C') ? 'checked' : '';
                break;
            case '4':
                $checked = ($config['config_temp'] == 'F') ? 'checked' : '';
                break;
            case '5':
                $checked = ($config['config_wind'] == 'kph') ? 'checked' : '';
                break;
            case '6':
                $checked = ($config['config_wind'] == 'mph') ? 'checked' : '';
                break;
            case '7':
                $checked = ($config['config_rain'] == 'mm') ? 'checked' : '';
                break;
            case '8':
                $checked = ($config['config_rain'] == 'in') ? 'checked' : '';
                break;
            case '9':
                $checked = ($config['config_press'] == 'hpa') ? 'checked' : '';
                break;
            case '10':
                $checked = ($config['config_press'] == 'inhg') ? 'checked' : '';
                break;
            case '11':
                $checked = ($config['config_css'] == 'bluedark') ? 'checked' : '';
                break;
            case '12':
                $checked = ($config['config_css'] == 'bluelight') ? 'checked' : '';
                break;
            case '13':
                $checked = ($config['config_css'] == 'white') ? 'checked' : '';
                break;
            case '14':
                $checked = ($config['config_css'] == 'black') ? 'checked' : '';
                break;
            case '15':
                $checked = ($config['config_daynight'] == 1) ? 'checked' : '';
                break;
            case '16':
                $checked = ($config['config_daynight'] == 0) ? 'checked' : '';
                break;
            case '17':
                $checked = ($config['config_color'] == 'neutral') ? 'checked' : '';
                break;
            case '18':
                $checked = ($config['config_color'] == 'colored') ? 'checked' : '';
                break;
            case '19':
                $checked = ($config['config_color'] == 'dynamic') ? 'checked' : '';
                break;
            case '20':
                $checked = ($config['config_icon'] == 1) ? 'checked' : '';
                break;
            case '21':
                $checked = ($config['config_icon'] == 0) ? 'checked' : '';
                break;

            default:
                $checked = '';
        }
        return $checked;
    }

    public function versionCompare()
    {
        $version = $this->dispatcher->versionNumURL(true);
        if ($this->isVersionCompare()) {
            $rep = " - vous avez la dernière version installée";
        } elseif (!($this->isVersionCompare())) {
            $rep = " - une version plus récente est disponible (" . $version . ")";
        } else {
            $rep = '';
        }
        return $rep;
    }


    public function versionInstalled()
    {
        require $this->file_admin;

        $version = $this->dispatcher->version();
        if ($version_installed == '{version}') {
            $rep = $version;
        } else {
            $rep = $version_installed;
        }
        return $rep;
    }

    /**
     * if true = version égal
     * if false = version pas à jour
     * return boolean
     */
    public function isVersionCompare()
    {
        require $this->file_admin;
        $version = $this->dispatcher->versionNumURL(true);
        if ($version == $version_installed) {
            $rep = true;
        } else {
            $rep = false;
        }

        return $rep;
    }
}
