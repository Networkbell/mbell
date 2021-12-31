<?php
class HomeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
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
