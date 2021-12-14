<?php

class Lang
{



    public function __construct()
    {
    }

    public function lgDefaut()
    {
        $init['lg_defaut'] = 'en';
        return $init['lg_defaut'];
    }

    public function lgList()
    {
        $init['lg_list'] = array('en', 'fr');
        return $init['lg_list'];
    }

    public function getLg()
    {

        /*LANGUE*/
        if (isset($_GET['lg'])) {
            $lg = (in_array($_GET['lg'], $this->lgList())) ? $_GET['lg'] : $this->lgDefaut();
        } else {
            $lg = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $lg = (in_array($lg, $this->lgList())) ? $lg : $this->lgDefaut();
        }

        return $lg;
    }

    public function trad($text)
    {
        $data = file_get_contents("lang/lg.json");
        $json = json_decode($data);
        $lg = $this->getLg();

        return $json->$text[0]->$lg; // 0 car il n'existe qu'1 seule ligne dans le fichier json pour chaque var $text

        //Méthode ARRAY
        /*$json = json_decode($data, true);
        return $json[$text][0][$lg];*/
    }



    public function degToCompass($wind_deg, $lg)
    {
        if ($lg == 'fr') {
            $arr = array("Nord", "Nord-Est", "Est", "Sud-Est", "Sud", "Sud-Ouest", "Ouest", "Nord-Ouest");
        } elseif ($lg == 'en') {
            $arr = array("North", "North-East", "East", "South-East", "South", "South-West", "West", "North-West");
        }
        $val = floor(($wind_deg / 45) + .5);
        return $arr[($val % 8)];
    }

    public function degToCompassSmall($wind_deg, $lg)
    {
        if ($lg == 'fr') {
            $arr = array("Nord", "NE", "Est", "SE", "Sud", "SO", "Ouest", "NO");
        } elseif ($lg == 'en') {
            $arr = array("North", "NE", "East", "SE", "South", "SW", "West", "NW");
        }
        $val = floor(($wind_deg / 45) + .5);
        return $arr[($val % 8)];
    }


    public function timeTrad($timeData, $lg)
    {
        if ($timeData != '&#8709;') {
            if ($lg == "fr") {
                $timeData = DateTime::createFromFormat('g:ia', $timeData)->format('H:i');
            } elseif ($lg == "en") {
                $timeData = substr($timeData, 0, -1);
            }
        }
        return $timeData;
    }



    public function pressTrad($value, $lg)
    {
            if ($lg == 'en') {
                $value = $value;
            } else if ($lg == 'fr') {
                $value = str_replace("Steady", "Stable", $value);
                $value = str_replace("Falling Slowly", "Baisse Lentement", $value);
                $value = str_replace("Rising Slowly", "Augmente Lentement", $value);
                $value = str_replace("Falling Rapidly", "Baisse Rapidement", $value);
                $value = str_replace("Rising Rapidly", "Augmente Rapidement", $value);
            }
        
        return $value;
    }



    /*function MOON FR-EN
    //Voir dans config/Moon.php class MoonPhase->function phase_name_EN()
    //Voir dans config/Moon.php class MoonPhase->function phase_name_FR()
    //Voir Dans View/StationView.php. function incMidSun() : Moon Prepa
    */
}
