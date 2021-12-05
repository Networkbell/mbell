<?php
class HomeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Selection d'un élément dans la BDD config et de la station associé
     * 
     * @return type(string, int)
     */
    public function getConfigActive()
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $config_tab = $table_prefix . 'config';
        $stat_statid = $station_tab . '.stat_id';
        $config_statid = $config_tab . '.stat_id';
        $stat_active = 1;

        $req = "SELECT config_id, config_lang, config_sun, config_aux1, config_aux2, config_aux3, config_temp, config_wind, config_rain, config_press, config_css, config_daynight, config_color, config_icon, $config_statid FROM $config_tab INNER JOIN $station_tab ON $config_statid = $stat_statid WHERE stat_active = :stat_active";

        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':stat_active', $stat_active);

            $result = $this->requete->execute();
            $list = array();
            if ($result) {
                $list = $this->requete->fetch(PDO::FETCH_ASSOC);
            }
            return $list;
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }


    /**
     * Selection d'un élément dans la BDD tab et de la station associé
     * 
     * @return type(string, int)
     */
    public function getTabActive()
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $tab_tab = $table_prefix . 'tab';
        $stat_statid = $station_tab . '.stat_id';
        $tab_statid = $tab_tab . '.stat_id';
        $stat_active = 1;

        $req = "SELECT tab_id, tab_lines, tab_1a, tab_1b, tab_1c, tab_2a, tab_2b, tab_2c, tab_3a, tab_3b, tab_3c, tab_4a, tab_4b, tab_4c, tab_5a, tab_5b, tab_5c, tab_6a, tab_6b, tab_6c, tab_7a, tab_7b, tab_7c, tab_8a, tab_8b, tab_8c, tab_9a, tab_9b, tab_9c, tab_10a, tab_10b, tab_10c, $tab_statid FROM $tab_tab INNER JOIN $station_tab ON $tab_statid = $stat_statid WHERE stat_active = :stat_active";

        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':stat_active', $stat_active);

            $result = $this->requete->execute();
            $list = array();
            if ($result) {
                $list = $this->requete->fetch(PDO::FETCH_ASSOC);
            }
            return $list;
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }





    public function tempDefaut($config)
    {
        $default = (isset($config['config_temp']) ? $config['config_temp'] : 'C');
        return $default;
    }
    public function tempList()
    {
        $init = array('C', 'F');
        return $init;
    }

    public function windDefaut($config)
    {
        $default = (isset($config['config_wind']) ? $config['config_wind'] : 'kph');
        return $default;
    }
    public function windList()
    {
        $init = array('kph', 'mph');
        return $init;
    }

    public function rainDefaut($config)
    {
        $default = (isset($config['config_rain']) ? $config['config_rain'] : 'mm');
        return $default;
    }
    public function rainList()
    {
        $init = array('mm', 'in');
        return $init;
    }

    public function pressDefaut($config)
    {
        $default = (isset($config['config_press']) ? $config['config_press'] : 'hpa');
        return $default;
    }
    public function pressList()
    {
        $init = array('hpa', 'inhg');
        return $init;
    }

    public function cssDefaut($config)
    {
        $default = (isset($config['config_css']) ? $config['config_css'] : 'bluedark');
        return $default;
    }
    public function cssList()
    {
        $init = array('bluedark', 'bluelight', 'black', 'white');
        return $init;
    }

    public function daynightDefaut($config)
    {
        $default = (isset($config['config_daynight']) ? (($config['config_daynight']== 0) ? 'off' : 'on') : 'on');
        return $default;
    }
    public function daynightList()
    {
        $init = array('on', 'off');
        return $init;
    }

    public function colorDefaut($config)
    {
        $default = (isset($config['config_color']) ? $config['config_color'] : 'colored');
        return $default;
    }
    public function colorList()
    {
        $init = array('neutral', 'colored', 'dynamic');
        return $init;
    }

    public function iconDefaut($config)
    {
        $default = (isset($config['config_icon']) ? (($config['config_icon'] == 0) ? 'no' : 'yes')  : 'yes');
        return $default;
    }
    public function iconList()
    {
        $init = array('yes', 'no');
        return $init;
    }

    public function dmyDefaut()
    {
        $default = 'D';
        return $default;
    }

    public function dmyList()
    {
        $init = array('D', 'M', 'Y');
        return $init;
    }




    public function getChoice($var, $list, $default)
    {
        //$filter1 = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        //$filter2 = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING);
        if (!empty($_POST[$var]) && setcookie($var, $_POST[$var], time() + (365 * 24 * 3600))) {
            $result = (in_array($_POST[$var], $list) ? $_POST[$var] : NULL);
        }       
        if (!isset($_POST[$var])) {
            $result = (isset($_COOKIE[$var]) ? $_COOKIE[$var] : $default);
        }
        else {
            $result = (in_array($_POST[$var], $list) ? $_POST[$var] : $default);
        }
        return $result;
    }


    public function allChoice($config)
    {
        $response1 = $this->getChoice('s_temp', $this->tempList(), $this->tempDefaut($config));
        $response2 = $this->getChoice('s_wind', $this->windList(), $this->windDefaut($config));
        $response3 = $this->getChoice('s_rain', $this->rainList(), $this->rainDefaut($config));
        $response4 = $this->getChoice('s_press', $this->pressList(), $this->pressDefaut($config));
        $response5 = $this->getChoice('s_css', $this->cssList(), $this->cssDefaut($config));
        $response6 = $this->getChoice('s_daynight', $this->daynightList(), $this->daynightDefaut($config));
        $response7 = $this->getChoice('s_color', $this->colorList(), $this->colorDefaut($config));
        $response8 = $this->getChoice('s_icon', $this->iconList(), $this->iconDefaut($config));
        $response9 = $this->getChoice('s_dmy', $this->dmyList(), $this->dmyDefaut($config));

        $result = array(
            "s_temp" => $response1, 
            "s_wind" => $response2, 
            "s_rain" => $response3, 
            "s_press" =>  $response4, 
            "s_css" => $response5, 
            "s_daynight" => $response6, 
            "s_color" => $response7, 
            "s_icon" =>  $response8, 
            "s_dmy" => $response9
        );
        return $result;
    }
}
