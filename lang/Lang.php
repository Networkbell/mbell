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
        /* Language */
        if (isset($_GET['lg'])) {
            $lg = in_array($_GET['lg'], $this->lgList(), true) ? $_GET['lg'] : $this->lgDefaut();
        } else {
            $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
            $lg = substr($acceptLanguage, 0, 2);
            $lg = in_array($lg, $this->lgList(), true) ? $lg : $this->lgDefaut();
        }

        return $lg;
    }

    public function trad($text)
    {
        $data = file_get_contents(MBELLPATH . 'lang/lg.json');
        $json = json_decode((string) $data, true);
        $lg = $this->getLg();

        /* Index 0 is used because only one row exists in lg.json for each translation key. */
        if (!is_array($json) || !isset($json[$text][0][$lg])) {
            return $text;
        }

        return $json[$text][0][$lg];
    }

    public function degToCompass($wind_deg, $lg)
    {
        if ($lg == 'fr') {
            $arr = array('Nord', 'Nord-Est', 'Est', 'Sud-Est', 'Sud', 'Sud-Ouest', 'Ouest', 'Nord-Ouest');
        } elseif ($lg == 'en') {
            $arr = array('North', 'North-East', 'East', 'South-East', 'South', 'South-West', 'West', 'North-West');
        } else {
            $arr = array('North', 'North-East', 'East', 'South-East', 'South', 'South-West', 'West', 'North-West');
        }

        if ($wind_deg != '&#8709;') {
            $val = floor(($wind_deg / 45) + 0.5);
            return $arr[$val % 8];
        }

        return null;
    }

    public function degToCompassSmall($wind_deg, $lg)
    {
        if ($lg == 'fr') {
            $arr = array('Nord', 'NE', 'Est', 'SE', 'Sud', 'SO', 'Ouest', 'NO');
        } elseif ($lg == 'en') {
            $arr = array('North', 'NE', 'East', 'SE', 'South', 'SW', 'West', 'NW');
        } else {
            $arr = array('North', 'NE', 'East', 'SE', 'South', 'SW', 'West', 'NW');
        }

        if ($wind_deg != '&#8709;') {
            $val = floor(($wind_deg / 45) + 0.5);
            return $arr[$val % 8];
        }

        return null;
    }

    public function timeTrad($timeData, $lg)
    {
        if ($timeData != '&#8709;') {
            if ($lg == 'fr') {
                $date = DateTime::createFromFormat('g:ia', $timeData);

                if ($date instanceof DateTime) {
                    $timeData = $date->format('H:i');
                }
            } elseif ($lg == 'en') {
                $timeData = substr($timeData, 0, -1);
            }
        }

        return $timeData;
    }

    public function pressTrad($value, $lg)
    {
        if ($lg == 'en') {
            $value = $value;
        } elseif ($lg == 'fr') {
            $value = str_replace('Steady', 'Stable', $value);
            $value = str_replace('Falling Slowly', 'Baisse Lentement', $value);
            $value = str_replace('Rising Slowly', 'Augmente Lentement', $value);
            $value = str_replace('Falling Rapidly', 'Baisse Rapidement', $value);
            $value = str_replace('Rising Rapidly', 'Augmente Rapidement', $value);
        }

        return $value;
    }

    /* Moon function FR-EN
    See config/Moon.php class MoonPhase->function phase_name_EN()
    See config/Moon.php class MoonPhase->function phase_name_FR()
    See View/StationView.php function incMidSun() for moon preparation
    */
}
