<?php

class StationView extends View
{

    public function __construct()
    {
        parent::__construct();
    }




    public function pressIntoMb($press)
    {
        if ($press != '&#8709;') {
            $mb = round(((floatval($press)) * 33.8639), 1);
            return $mb;
        }
    }

    /**
     * Pour Weatherlink Live
     * calcul sunset-sunrise à partir de timestamp
     */
    public function liveDateSun($time, $latitude, $longitude, $fuseau, $type)
    {
        if ($time != '&#8709;') {
            date_default_timezone_set($fuseau);
            $date = date("h:i a", date_sun_info($time, $latitude, $longitude)[$type]);
            return $date;
        }
    }


    /**
     * Pour API v1
     * calcul time format RCF822 à partir de timestamp
     */
    public function liveDateRFC822($time, $fuseau)
    {
        if ($time != '&#8709;') {
            date_default_timezone_set($fuseau);
            $date = date(DATE_RFC822, $time);
            return $date;
        }
    }

    /**
     * Pour API weewx UTC
     * calcul time format RCF822 à partir de timestamp 
     * avec offset sous forme "CET +0100"
     */
    public function weewxDateRFC822($time, $offset)
    {
        $zero = '&#8709;';
        if ($time != $zero  && $offset != $zero) {
            // CET +0100
            $gmt = substr($offset, 0, -6); // CET
            $off = substr($offset, -5); // +0100

            $dt = new DateTime();
            $dt->setTimeZone(new DateTimeZone($gmt));
            $dt->setTimestamp($time);
            $dt->setTimeZone(new DateTimeZone($off));
            $date = $dt->format(DATE_RFC822);
            return $date;
        }
    }

    /**
     * Pour API weewx
     * calcul fuseau horaire à partir de offset
     */
    public function timeZoneWeewx($offset)
    {
        $zero = '&#8709;';
        if ($offset != $zero) {
            // CET +0100
            $gmt = substr($offset, 0, -6); // CET
            $off = substr($offset, -5); // +0100
            $date1 = DateTime::createFromFormat('O', $gmt)->getOffset();
            $date2 = DateTime::createFromFormat('O', $off)->getOffset();
            $date = $date1 + $date2;
            $timezone = timezone_name_from_abbr("", $date, 0);
            return $timezone;
        }
    }



    /**
     * Pour API v1
     * calcul fuseau horaire à partir de time format RCF822 
     */
    public function timeZone($timeRFC822)
    {
        if ($timeRFC822 != '&#8709;') {
            $dt = new DateTime($timeRFC822);
            /* $tz = $dt->getTimezone();
            $fus = $tz->getName();*/
            $offset =  $dt->getOffset();
            $timezone = timezone_name_from_abbr("", $offset, 0);
            return $timezone;
        }
    }



    /**
     * Création de date à partir de timestamp
     */

    public function DateCreate($datas, $lg, $timeZone)
    {
        $zero = '&#8709;';
        if (isset($datas) && $datas != $zero) {
            date_default_timezone_set($timeZone);
            $date = new DateTime();
            $date->setTimestamp($datas);
            if ($lg == "fr") {
                $date_time =  $date->format('d.m.Y à H:i');
            } elseif ($lg == "en") {
                $date_time = $date->format('m/d/Y @ h:ia');
            }
        } else {
            $date_time =  $zero;
        }

        return $date_time;
    }


    /**
     * Création de date à partir de format RFC822
     */

    public function DateStation($datas, $lg)
    {
        $zero = '&#8709;';
        if ($datas != $zero) {
            $tmp_date = date_create($datas);
            if (isset($tmp_date)) {
                if ($lg == "fr") {
                    $date_time = date_format($tmp_date, "d.m.Y à H:i");
                } elseif ($lg == "en") {
                    $date_time = date_format($tmp_date, "m/d/Y @ h:ia");
                }
            }
            return $date_time;
        }
    }


    /**
     * format de $datas doit être "h:i a"
     *
     * */

    public function TimeStation($datas)
    {
        $zero = '&#8709;';
        if ($datas != $zero) {
            $tmp_date = date_create($datas);
            if (isset($tmp_date)) {
                $time = date_format($tmp_date, "Hi");
            }
            return $time;
        }
    }

    /* 
    Pour Weatherlink Live
    Calcul pressure string au format Api v1 à partir de bar_trend
    
    if bar_trend >= 0.060 then it is Rising Rapidly
        if bar_trend >= 0.020 then it is Rising Slowly
        if bar_trend < 0.020 and bar_trend > -0.020 then it is Steady
        if bar_trend <= -0.020 then it is Falling Slowly
        if bar_trend <= -0.060 then it is Falling Rapidly
    */
    public function livePressTrend($value)
    {

        $zero = '&#8709;';
        $value = strval($value);
        if ($value != $zero) {
            if ($value >= '0.060') {
                $value = str_replace($value, "Rising Rapidly", $value);
            }
            if ($value >= '0.020' && $value < '0.060') {
                $value = str_replace($value, "Rising Slowly", $value);
            }
            if ($value > '-0.020' && $value < '0.020') {
                $value = str_replace($value, "Steady", $value);
            }
            if ($value > '-0.060' && $value <= '-0.020') {
                $value = str_replace($value, "Falling Slowly", $value);
            }
            if ($value <= '-0.060') {
                $value = str_replace($value, "Falling Rapidly", $value);
            }
            return $value;
        }
    }

    /* 
    Remplace le string pressure par une image
    */
    function pressImg($value)
    {
        $value = str_replace("Steady", "images/stable.png", $value);
        $value = str_replace("Falling Slowly", "images/fleche_bas.png", $value);
        $value = str_replace("Rising Slowly", "images/fleche_haut.png", $value);
        $value = str_replace("Falling Rapidly", "images/fleche_bas_1.png", $value);
        $value = str_replace("Rising Rapidly", "images/fleche_haut_1.png", $value);

        return $value;
    }

    /* 
    Test de tous les datas Json
    */
    public function getAPIDatas($datas, $station, $livestation, $livenbr, $livetab)
    {
        $zero = '&#8709;';

        $type = (isset($station['stat_type'])) ? $station['stat_type'] : $zero;

        //on transforme $datas en $data si array
        if ($type == 'live' || $type == 'weewx') {
            $data = (is_array($datas)) ? $datas : $zero;
            $dat = $da = $zero;
            //on transforme $datas en $da + $dat si object
        } elseif ($type == 'v1' || $type == 'v2') {
            $da = (is_object($datas)) ? $datas : $zero;
            $dat = $da->davis_current_observation ?? $zero;
            $data = $zero;
        } else {
            $dat = $da = $data = $zero;
        }



        $response = array(
            //V1
            "temp_c" => ($type == 'live') ?  $zero : ($da->temp_c ?? $zero),
            "pressure_mb" => ($type == 'live') ? $zero  : ($da->pressure_mb ?? $zero),
            "pressure_tendency_string" => ($type == 'live' || $type == 'weewx') ? $zero : ($dat->pressure_tendency_string ?? $zero),
            "time_RFC822" => ($type == 'live' || $type == 'weewx') ? $zero : ($da->observation_time_rfc822 ?? $zero),
            "sunset" => ($type == 'live' || $type == 'weewx') ? $zero : ($dat->sunset ?? $zero),
            "sunrise" => ($type == 'live' || $type == 'weewx') ? $zero : ($dat->sunrise ?? $zero),
            "wind_day_high_mph" => ($type == 'live' || $type == 'weewx') ?  $zero : ($dat->wind_day_high_mph ?? $zero),
            "wind_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : ($dat->wind_day_high_time ?? $zero),
            "wind_month_high_mph" => ($type == 'live' || $type == 'weewx') ?  $zero : ($dat->wind_month_high_mph ?? $zero),
            "wind_year_high_mph" => ($type == 'live' || $type == 'weewx') ?  $zero : ($dat->wind_year_high_mph ?? $zero),

            "pressure_day_low_in" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_day_low_in) ? $dat->pressure_day_low_in : $zero),
            "pressure_month_low_in" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_month_low_in) ? $dat->pressure_month_low_in : $zero),
            "pressure_year_low_in" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_year_low_in) ? $dat->pressure_year_low_in : $zero),
            "pressure_day_high_in" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_day_high_in) ? $dat->pressure_day_high_in : $zero),
            "pressure_month_high_in" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_month_high_in) ? $dat->pressure_month_high_in : $zero),
            "pressure_year_high_in" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_year_high_in) ? $dat->pressure_year_high_in : $zero),
            "pressure_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_day_low_time) ? $dat->pressure_day_low_time : $zero),
            "pressure_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->pressure_day_high_time) ? $dat->pressure_day_high_time : $zero),
            "dewpoint_day_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_day_low_f) ? $dat->dewpoint_day_low_f : $zero),
            "dewpoint_month_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_month_low_f) ? $dat->dewpoint_month_low_f : $zero),
            "dewpoint_year_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_year_low_f) ? $dat->dewpoint_year_low_f : $zero),
            "dewpoint_day_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_day_high_f) ? $dat->dewpoint_day_high_f : $zero),
            "dewpoint_month_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_month_high_f) ? $dat->dewpoint_month_high_f : $zero),
            "dewpoint_year_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_year_high_f) ? $dat->dewpoint_year_high_f : $zero),
            "dewpoint_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_day_low_time) ? $dat->dewpoint_day_low_time : $zero),
            "dewpoint_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->dewpoint_day_high_time) ? $dat->dewpoint_day_high_time : $zero),
            "relative_humidity_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_day_low) ? $dat->relative_humidity_day_low : $zero),
            "relative_humidity_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_month_low) ? $dat->relative_humidity_month_low : $zero),
            "relative_humidity_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_year_low) ? $dat->relative_humidity_year_low : $zero),
            "relative_humidity_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_day_high) ? $dat->relative_humidity_day_high : $zero),
            "relative_humidity_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_month_high) ? $dat->relative_humidity_month_high : $zero),
            "relative_humidity_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_year_high) ? $dat->relative_humidity_year_high : $zero),
            "relative_humidity_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_day_low_time) ? $dat->relative_humidity_day_low_time : $zero),
            "relative_humidity_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_day_high_time) ? $dat->relative_humidity_day_high_time : $zero),
            "temp_extra_1_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_day_low) ? $dat->temp_extra_1_day_low : $zero),
            "temp_extra_1_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_month_low) ? $dat->temp_extra_1_month_low : $zero),
            "temp_extra_1_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_year_low) ? $dat->temp_extra_1_year_low : $zero),
            "temp_extra_1_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_day_high) ? $dat->temp_extra_1_day_high : $zero),
            "temp_extra_1_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_month_high) ? $dat->temp_extra_1_month_high : $zero),
            "temp_extra_1_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_year_high) ? $dat->temp_extra_1_year_high : $zero),
            "temp_extra_1_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_day_low_time) ? $dat->temp_extra_1_day_low_time : $zero),
            "temp_extra_1_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_1_day_high_time) ? $dat->temp_extra_1_day_high_time : $zero),
            "temp_extra_2_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_day_low) ? $dat->temp_extra_2_day_low : $zero),
            "temp_extra_2_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_month_low) ? $dat->temp_extra_2_month_low : $zero),
            "temp_extra_2_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_year_low) ? $dat->temp_extra_2_year_low : $zero),
            "temp_extra_2_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_day_high) ? $dat->temp_extra_2_day_high : $zero),
            "temp_extra_2_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_month_high) ? $dat->temp_extra_2_month_high : $zero),
            "temp_extra_2_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_year_high) ? $dat->temp_extra_2_year_high : $zero),
            "temp_extra_2_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_day_low_time) ? $dat->temp_extra_2_day_low_time : $zero),
            "temp_extra_2_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_2_day_high_time) ? $dat->temp_extra_2_day_high_time : $zero),
            "temp_extra_3_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_day_low) ? $dat->temp_extra_3_day_low : $zero),
            "temp_extra_3_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_month_low) ? $dat->temp_extra_3_month_low : $zero),
            "temp_extra_3_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_year_low) ? $dat->temp_extra_3_year_low : $zero),
            "temp_extra_3_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_day_high) ? $dat->temp_extra_3_day_high : $zero),
            "temp_extra_3_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_month_high) ? $dat->temp_extra_3_month_high : $zero),
            "temp_extra_3_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_year_high) ? $dat->temp_extra_3_year_high : $zero),
            "temp_extra_3_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_day_low_time) ? $dat->temp_extra_3_day_low_time : $zero),
            "temp_extra_3_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_3_day_high_time) ? $dat->temp_extra_3_day_high_time : $zero),
            "temp_extra_4_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_day_low) ? $dat->temp_extra_4_day_low : $zero),
            "temp_extra_4_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_month_low) ? $dat->temp_extra_4_month_low : $zero),
            "temp_extra_4_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_year_low) ? $dat->temp_extra_4_year_low : $zero),
            "temp_extra_4_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_day_high) ? $dat->temp_extra_4_day_high : $zero),
            "temp_extra_4_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_month_high) ? $dat->temp_extra_4_month_high : $zero),
            "temp_extra_4_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_year_high) ? $dat->temp_extra_4_year_high : $zero),
            "temp_extra_4_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_day_low_time) ? $dat->temp_extra_4_day_low_time : $zero),
            "temp_extra_4_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_4_day_high_time) ? $dat->temp_extra_4_day_high_time : $zero),
            "temp_extra_5_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_day_low) ? $dat->temp_extra_5_day_low : $zero),
            "temp_extra_5_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_month_low) ? $dat->temp_extra_5_month_low : $zero),
            "temp_extra_5_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_year_low) ? $dat->temp_extra_5_year_low : $zero),
            "temp_extra_5_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_day_high) ? $dat->temp_extra_5_day_high : $zero),
            "temp_extra_5_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_month_high) ? $dat->temp_extra_5_month_high : $zero),
            "temp_extra_5_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_year_high) ? $dat->temp_extra_5_year_high : $zero),
            "temp_extra_5_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_day_low_time) ? $dat->temp_extra_5_day_low_time : $zero),
            "temp_extra_5_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_5_day_high_time) ? $dat->temp_extra_5_day_high_time : $zero),
            "temp_extra_6_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_day_low) ? $dat->temp_extra_6_day_low : $zero),
            "temp_extra_6_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_month_low) ? $dat->temp_extra_6_month_low : $zero),
            "temp_extra_6_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_year_low) ? $dat->temp_extra_6_year_low : $zero),
            "temp_extra_6_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_day_high) ? $dat->temp_extra_6_day_high : $zero),
            "temp_extra_6_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_month_high) ? $dat->temp_extra_6_month_high : $zero),
            "temp_extra_6_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_year_high) ? $dat->temp_extra_6_year_high : $zero),
            "temp_extra_6_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_day_low_time) ? $dat->temp_extra_6_day_low_time : $zero),
            "temp_extra_6_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_6_day_high_time) ? $dat->temp_extra_6_day_high_time : $zero),
            "temp_extra_7_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_day_low) ? $dat->temp_extra_7_day_low : $zero),
            "temp_extra_7_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_month_low) ? $dat->temp_extra_7_month_low : $zero),
            "temp_extra_7_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_year_low) ? $dat->temp_extra_7_year_low : $zero),
            "temp_extra_7_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_day_high) ? $dat->temp_extra_7_day_high : $zero),
            "temp_extra_7_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_month_high) ? $dat->temp_extra_7_month_high : $zero),
            "temp_extra_7_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_year_high) ? $dat->temp_extra_7_year_high : $zero),
            "temp_extra_7_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_day_low_time) ? $dat->temp_extra_7_day_low_time : $zero),
            "temp_extra_7_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_extra_7_day_high_time) ? $dat->temp_extra_7_day_high_time : $zero),
            "temp_leaf_1_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_day_low) ? $dat->temp_leaf_1_day_low : $zero),
            "temp_leaf_1_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_month_low) ? $dat->temp_leaf_1_month_low : $zero),
            "temp_leaf_1_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_year_low) ? $dat->temp_leaf_1_year_low : $zero),
            "temp_leaf_1_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_day_high) ? $dat->temp_leaf_1_day_high : $zero),
            "temp_leaf_1_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_month_high) ? $dat->temp_leaf_1_month_high : $zero),
            "temp_leaf_1_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_year_high) ? $dat->temp_leaf_1_year_high : $zero),
            "temp_leaf_1_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_day_low_time) ? $dat->temp_leaf_1_day_low_time : $zero),
            "temp_leaf_1_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_1_day_high_time) ? $dat->temp_leaf_1_day_high_time : $zero),
            "temp_leaf_2_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_day_low) ? $dat->temp_leaf_2_day_low : $zero),
            "temp_leaf_2_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_month_low) ? $dat->temp_leaf_2_month_low : $zero),
            "temp_leaf_2_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_year_low) ? $dat->temp_leaf_2_year_low : $zero),
            "temp_leaf_2_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_day_high) ? $dat->temp_leaf_2_day_high : $zero),
            "temp_leaf_2_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_month_high) ? $dat->temp_leaf_2_month_high : $zero),
            "temp_leaf_2_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_year_high) ? $dat->temp_leaf_2_year_high : $zero),
            "temp_leaf_2_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_day_low_time) ? $dat->temp_leaf_2_day_low_time : $zero),
            "temp_leaf_2_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_2_day_high_time) ? $dat->temp_leaf_2_day_high_time : $zero),
            "temp_leaf_3_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_day_low) ? $dat->temp_leaf_3_day_low : $zero),
            "temp_leaf_3_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_month_low) ? $dat->temp_leaf_3_month_low : $zero),
            "temp_leaf_3_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_year_low) ? $dat->temp_leaf_3_year_low : $zero),
            "temp_leaf_3_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_day_high) ? $dat->temp_leaf_3_day_high : $zero),
            "temp_leaf_3_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_month_high) ? $dat->temp_leaf_3_month_high : $zero),
            "temp_leaf_3_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_year_high) ? $dat->temp_leaf_3_year_high : $zero),
            "temp_leaf_3_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_day_low_time) ? $dat->temp_leaf_3_day_low_time : $zero),
            "temp_leaf_3_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_3_day_high_time) ? $dat->temp_leaf_3_day_high_time : $zero),
            "temp_leaf_4_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_day_low) ? $dat->temp_leaf_4_day_low : $zero),
            "temp_leaf_4_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_month_low) ? $dat->temp_leaf_4_month_low : $zero),
            "temp_leaf_4_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_year_low) ? $dat->temp_leaf_4_year_low : $zero),
            "temp_leaf_4_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_day_high) ? $dat->temp_leaf_4_day_high : $zero),
            "temp_leaf_4_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_month_high) ? $dat->temp_leaf_4_month_high : $zero),
            "temp_leaf_4_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_year_high) ? $dat->temp_leaf_4_year_high : $zero),
            "temp_leaf_4_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_day_low_time) ? $dat->temp_leaf_4_day_low_time : $zero),
            "temp_leaf_4_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_leaf_4_day_high_time) ? $dat->temp_leaf_4_day_high_time : $zero),
            "temp_soil_1_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_day_low) ? $dat->temp_soil_1_day_low : $zero),
            "temp_soil_1_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_month_low) ? $dat->temp_soil_1_month_low : $zero),
            "temp_soil_1_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_year_low) ? $dat->temp_soil_1_year_low : $zero),
            "temp_soil_1_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_day_high) ? $dat->temp_soil_1_day_high : $zero),
            "temp_soil_1_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_month_high) ? $dat->temp_soil_1_month_high : $zero),
            "temp_soil_1_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_year_high) ? $dat->temp_soil_1_year_high : $zero),
            "temp_soil_1_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_day_low_time) ? $dat->temp_soil_1_day_low_time : $zero),
            "temp_soil_1_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_1_day_high_time) ? $dat->temp_soil_1_day_high_time : $zero),
            "temp_soil_2_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_day_low) ? $dat->temp_soil_2_day_low : $zero),
            "temp_soil_2_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_month_low) ? $dat->temp_soil_2_month_low : $zero),
            "temp_soil_2_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_year_low) ? $dat->temp_soil_2_year_low : $zero),
            "temp_soil_2_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_day_high) ? $dat->temp_soil_2_day_high : $zero),
            "temp_soil_2_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_month_high) ? $dat->temp_soil_2_month_high : $zero),
            "temp_soil_2_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_year_high) ? $dat->temp_soil_2_year_high : $zero),
            "temp_soil_2_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_day_low_time) ? $dat->temp_soil_2_day_low_time : $zero),
            "temp_soil_2_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_2_day_high_time) ? $dat->temp_soil_2_day_high_time : $zero),
            "temp_soil_3_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_day_low) ? $dat->temp_soil_3_day_low : $zero),
            "temp_soil_3_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_month_low) ? $dat->temp_soil_3_month_low : $zero),
            "temp_soil_3_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_year_low) ? $dat->temp_soil_3_year_low : $zero),
            "temp_soil_3_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_day_high) ? $dat->temp_soil_3_day_high : $zero),
            "temp_soil_3_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_month_high) ? $dat->temp_soil_3_month_high : $zero),
            "temp_soil_3_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_year_high) ? $dat->temp_soil_3_year_high : $zero),
            "temp_soil_3_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_day_low_time) ? $dat->temp_soil_3_day_low_time : $zero),
            "temp_soil_3_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_3_day_high_time) ? $dat->temp_soil_3_day_high_time : $zero),
            "temp_soil_4_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_day_low) ? $dat->temp_soil_4_day_low : $zero),
            "temp_soil_4_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_month_low) ? $dat->temp_soil_4_month_low : $zero),
            "temp_soil_4_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_year_low) ? $dat->temp_soil_4_year_low : $zero),
            "temp_soil_4_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_day_high) ? $dat->temp_soil_4_day_high : $zero),
            "temp_soil_4_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_month_high) ? $dat->temp_soil_4_month_high : $zero),
            "temp_soil_4_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_year_high) ? $dat->temp_soil_4_year_high : $zero),
            "temp_soil_4_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_day_low_time) ? $dat->temp_soil_4_day_low_time : $zero),
            "temp_soil_4_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_soil_4_day_high_time) ? $dat->temp_soil_4_day_high_time : $zero),
            "relative_humidity_1_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_day_low) ? $dat->relative_humidity_1_day_low : $zero),
            "relative_humidity_1_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_month_low) ? $dat->relative_humidity_1_month_low : $zero),
            "relative_humidity_1_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_year_low) ? $dat->relative_humidity_1_year_low : $zero),
            "relative_humidity_1_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_day_high) ? $dat->relative_humidity_1_day_high : $zero),
            "relative_humidity_1_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_month_high) ? $dat->relative_humidity_1_month_high : $zero),
            "relative_humidity_1_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_year_high) ? $dat->relative_humidity_1_year_high : $zero),
            "relative_humidity_1_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_day_low_time) ? $dat->relative_humidity_1_day_low_time : $zero),
            "relative_humidity_1_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_1_day_high_time) ? $dat->relative_humidity_1_day_high_time : $zero),
            "relative_humidity_2_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_day_low) ? $dat->relative_humidity_2_day_low : $zero),
            "relative_humidity_2_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_month_low) ? $dat->relative_humidity_2_month_low : $zero),
            "relative_humidity_2_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_year_low) ? $dat->relative_humidity_2_year_low : $zero),
            "relative_humidity_2_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_day_high) ? $dat->relative_humidity_2_day_high : $zero),
            "relative_humidity_2_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_month_high) ? $dat->relative_humidity_2_month_high : $zero),
            "relative_humidity_2_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_year_high) ? $dat->relative_humidity_2_year_high : $zero),
            "relative_humidity_2_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_day_low_time) ? $dat->relative_humidity_2_day_low_time : $zero),
            "relative_humidity_2_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_2_day_high_time) ? $dat->relative_humidity_2_day_high_time : $zero),
            "relative_humidity_3_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_day_low) ? $dat->relative_humidity_3_day_low : $zero),
            "relative_humidity_3_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_month_low) ? $dat->relative_humidity_3_month_low : $zero),
            "relative_humidity_3_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_year_low) ? $dat->relative_humidity_3_year_low : $zero),
            "relative_humidity_3_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_day_high) ? $dat->relative_humidity_3_day_high : $zero),
            "relative_humidity_3_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_month_high) ? $dat->relative_humidity_3_month_high : $zero),
            "relative_humidity_3_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_year_high) ? $dat->relative_humidity_3_year_high : $zero),
            "relative_humidity_3_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_day_low_time) ? $dat->relative_humidity_3_day_low_time : $zero),
            "relative_humidity_3_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_3_day_high_time) ? $dat->relative_humidity_3_day_high_time : $zero),
            "relative_humidity_4_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_day_low) ? $dat->relative_humidity_4_day_low : $zero),
            "relative_humidity_4_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_month_low) ? $dat->relative_humidity_4_month_low : $zero),
            "relative_humidity_4_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_year_low) ? $dat->relative_humidity_4_year_low : $zero),
            "relative_humidity_4_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_day_high) ? $dat->relative_humidity_4_day_high : $zero),
            "relative_humidity_4_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_month_high) ? $dat->relative_humidity_4_month_high : $zero),
            "relative_humidity_4_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_year_high) ? $dat->relative_humidity_4_year_high : $zero),
            "relative_humidity_4_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_day_low_time) ? $dat->relative_humidity_4_day_low_time : $zero),
            "relative_humidity_4_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_4_day_high_time) ? $dat->relative_humidity_4_day_high_time : $zero),
            "relative_humidity_5_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_day_low) ? $dat->relative_humidity_5_day_low : $zero),
            "relative_humidity_5_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_month_low) ? $dat->relative_humidity_5_month_low : $zero),
            "relative_humidity_5_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_year_low) ? $dat->relative_humidity_5_year_low : $zero),
            "relative_humidity_5_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_day_high) ? $dat->relative_humidity_5_day_high : $zero),
            "relative_humidity_5_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_month_high) ? $dat->relative_humidity_5_month_high : $zero),
            "relative_humidity_5_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_year_high) ? $dat->relative_humidity_5_year_high : $zero),
            "relative_humidity_5_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_day_low_time) ? $dat->relative_humidity_5_day_low_time : $zero),
            "relative_humidity_5_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_5_day_high_time) ? $dat->relative_humidity_5_day_high_time : $zero),
            "relative_humidity_6_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_day_low) ? $dat->relative_humidity_6_day_low : $zero),
            "relative_humidity_6_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_month_low) ? $dat->relative_humidity_6_month_low : $zero),
            "relative_humidity_6_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_year_low) ? $dat->relative_humidity_6_year_low : $zero),
            "relative_humidity_6_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_day_high) ? $dat->relative_humidity_6_day_high : $zero),
            "relative_humidity_6_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_month_high) ? $dat->relative_humidity_6_month_high : $zero),
            "relative_humidity_6_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_year_high) ? $dat->relative_humidity_6_year_high : $zero),
            "relative_humidity_6_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_day_low_time) ? $dat->relative_humidity_6_day_low_time : $zero),
            "relative_humidity_6_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_6_day_high_time) ? $dat->relative_humidity_6_day_high_time : $zero),
            "relative_humidity_7_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_day_low) ? $dat->relative_humidity_7_day_low : $zero),
            "relative_humidity_7_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_month_low) ? $dat->relative_humidity_7_month_low : $zero),
            "relative_humidity_7_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_year_low) ? $dat->relative_humidity_7_year_low : $zero),
            "relative_humidity_7_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_day_high) ? $dat->relative_humidity_7_day_high : $zero),
            "relative_humidity_7_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_month_high) ? $dat->relative_humidity_7_month_high : $zero),
            "relative_humidity_7_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_year_high) ? $dat->relative_humidity_7_year_high : $zero),
            "relative_humidity_7_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_day_low_time) ? $dat->relative_humidity_7_day_low_time : $zero),
            "relative_humidity_7_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_7_day_high_time) ? $dat->relative_humidity_7_day_high_time : $zero),
            "leaf_wetness_1_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_day_low) ? $dat->leaf_wetness_1_day_low : $zero),
            "leaf_wetness_1_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_month_low) ? $dat->leaf_wetness_1_month_low : $zero),
            "leaf_wetness_1_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_year_low) ? $dat->leaf_wetness_1_year_low : $zero),
            "leaf_wetness_1_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_day_high) ? $dat->leaf_wetness_1_day_high : $zero),
            "leaf_wetness_1_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_month_high) ? $dat->leaf_wetness_1_month_high : $zero),
            "leaf_wetness_1_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_year_high) ? $dat->leaf_wetness_1_year_high : $zero),
            "leaf_wetness_1_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_day_low_time) ? $dat->leaf_wetness_1_day_low_time : $zero),
            "leaf_wetness_1_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_1_day_high_time) ? $dat->leaf_wetness_1_day_high_time : $zero),
            "leaf_wetness_2_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_day_low) ? $dat->leaf_wetness_2_day_low : $zero),
            "leaf_wetness_2_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_month_low) ? $dat->leaf_wetness_2_month_low : $zero),
            "leaf_wetness_2_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_year_low) ? $dat->leaf_wetness_2_year_low : $zero),
            "leaf_wetness_2_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_day_high) ? $dat->leaf_wetness_2_day_high : $zero),
            "leaf_wetness_2_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_month_high) ? $dat->leaf_wetness_2_month_high : $zero),
            "leaf_wetness_2_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_year_high) ? $dat->leaf_wetness_2_year_high : $zero),
            "leaf_wetness_2_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_day_low_time) ? $dat->leaf_wetness_2_day_low_time : $zero),
            "leaf_wetness_2_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_2_day_high_time) ? $dat->leaf_wetness_2_day_high_time : $zero),
            "leaf_wetness_3_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_day_low) ? $dat->leaf_wetness_3_day_low : $zero),
            "leaf_wetness_3_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_month_low) ? $dat->leaf_wetness_3_month_low : $zero),
            "leaf_wetness_3_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_year_low) ? $dat->leaf_wetness_3_year_low : $zero),
            "leaf_wetness_3_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_day_high) ? $dat->leaf_wetness_3_day_high : $zero),
            "leaf_wetness_3_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_month_high) ? $dat->leaf_wetness_3_month_high : $zero),
            "leaf_wetness_3_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_year_high) ? $dat->leaf_wetness_3_year_high : $zero),
            "leaf_wetness_3_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_day_low_time) ? $dat->leaf_wetness_3_day_low_time : $zero),
            "leaf_wetness_3_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_3_day_high_time) ? $dat->leaf_wetness_3_day_high_time : $zero),
            "leaf_wetness_4_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_day_low) ? $dat->leaf_wetness_4_day_low : $zero),
            "leaf_wetness_4_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_month_low) ? $dat->leaf_wetness_4_month_low : $zero),
            "leaf_wetness_4_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_year_low) ? $dat->leaf_wetness_4_year_low : $zero),
            "leaf_wetness_4_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_day_high) ? $dat->leaf_wetness_4_day_high : $zero),
            "leaf_wetness_4_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_month_high) ? $dat->leaf_wetness_4_month_high : $zero),
            "leaf_wetness_4_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_year_high) ? $dat->leaf_wetness_4_year_high : $zero),
            "leaf_wetness_4_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_day_low_time) ? $dat->leaf_wetness_4_day_low_time : $zero),
            "leaf_wetness_4_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->leaf_wetness_4_day_high_time) ? $dat->leaf_wetness_4_day_high_time : $zero),
            "soil_moisture_1_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_day_low) ? $dat->soil_moisture_1_day_low : $zero),
            "soil_moisture_1_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_month_low) ? $dat->soil_moisture_1_month_low : $zero),
            "soil_moisture_1_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_year_low) ? $dat->soil_moisture_1_year_low : $zero),
            "soil_moisture_1_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_day_high) ? $dat->soil_moisture_1_day_high : $zero),
            "soil_moisture_1_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_month_high) ? $dat->soil_moisture_1_month_high : $zero),
            "soil_moisture_1_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_year_high) ? $dat->soil_moisture_1_year_high : $zero),
            "soil_moisture_1_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_day_low_time) ? $dat->soil_moisture_1_day_low_time : $zero),
            "soil_moisture_1_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_1_day_high_time) ? $dat->soil_moisture_1_day_high_time : $zero),
            "soil_moisture_2_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_day_low) ? $dat->soil_moisture_2_day_low : $zero),
            "soil_moisture_2_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_month_low) ? $dat->soil_moisture_2_month_low : $zero),
            "soil_moisture_2_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_year_low) ? $dat->soil_moisture_2_year_low : $zero),
            "soil_moisture_2_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_day_high) ? $dat->soil_moisture_2_day_high : $zero),
            "soil_moisture_2_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_month_high) ? $dat->soil_moisture_2_month_high : $zero),
            "soil_moisture_2_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_year_high) ? $dat->soil_moisture_2_year_high : $zero),
            "soil_moisture_2_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_day_low_time) ? $dat->soil_moisture_2_day_low_time : $zero),
            "soil_moisture_2_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_2_day_high_time) ? $dat->soil_moisture_2_day_high_time : $zero),
            "soil_moisture_3_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_day_low) ? $dat->soil_moisture_3_day_low : $zero),
            "soil_moisture_3_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_month_low) ? $dat->soil_moisture_3_month_low : $zero),
            "soil_moisture_3_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_year_low) ? $dat->soil_moisture_3_year_low : $zero),
            "soil_moisture_3_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_day_high) ? $dat->soil_moisture_3_day_high : $zero),
            "soil_moisture_3_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_month_high) ? $dat->soil_moisture_3_month_high : $zero),
            "soil_moisture_3_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_year_high) ? $dat->soil_moisture_3_year_high : $zero),
            "soil_moisture_3_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_day_low_time) ? $dat->soil_moisture_3_day_low_time : $zero),
            "soil_moisture_3_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_3_day_high_time) ? $dat->soil_moisture_3_day_high_time : $zero),
            "soil_moisture_4_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_day_low) ? $dat->soil_moisture_4_day_low : $zero),
            "soil_moisture_4_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_month_low) ? $dat->soil_moisture_4_month_low : $zero),
            "soil_moisture_4_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_year_low) ? $dat->soil_moisture_4_year_low : $zero),
            "soil_moisture_4_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_day_high) ? $dat->soil_moisture_4_day_high : $zero),
            "soil_moisture_4_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_month_high) ? $dat->soil_moisture_4_month_high : $zero),
            "soil_moisture_4_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_year_high) ? $dat->soil_moisture_4_year_high : $zero),
            "soil_moisture_4_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_day_low_time) ? $dat->soil_moisture_4_day_low_time : $zero),
            "soil_moisture_4_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->soil_moisture_4_day_high_time) ? $dat->soil_moisture_4_day_high_time : $zero),
            "temp_in_day_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_day_low_f) ? $dat->temp_in_day_low_f : $zero),
            "temp_in_month_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_month_low_f) ? $dat->temp_in_month_low_f : $zero),
            "temp_in_year_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_year_low_f) ? $dat->temp_in_year_low_f : $zero),
            "temp_in_day_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_day_high_f) ? $dat->temp_in_day_high_f : $zero),
            "temp_in_month_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_month_high_f) ? $dat->temp_in_month_high_f : $zero),
            "temp_in_year_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_year_high_f) ? $dat->temp_in_year_high_f : $zero),
            "temp_in_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_day_low_time) ? $dat->temp_in_day_low_time : $zero),
            "temp_in_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_in_day_high_time) ? $dat->temp_in_day_high_time : $zero),
            "relative_humidity_in_day_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_day_low) ? $dat->relative_humidity_in_day_low : $zero),
            "relative_humidity_in_month_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_month_low) ? $dat->relative_humidity_in_month_low : $zero),
            "relative_humidity_in_year_low" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_year_low) ? $dat->relative_humidity_in_year_low : $zero),
            "relative_humidity_in_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_day_high) ? $dat->relative_humidity_in_day_high : $zero),
            "relative_humidity_in_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_month_high) ? $dat->relative_humidity_in_month_high : $zero),
            "relative_humidity_in_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_year_high) ? $dat->relative_humidity_in_year_high : $zero),
            "relative_humidity_in_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_day_low_time) ? $dat->relative_humidity_in_day_low_time : $zero),
            "relative_humidity_in_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->relative_humidity_in_day_high_time) ? $dat->relative_humidity_in_day_high_time : $zero),
            "temp_day_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_day_high_f) ? $dat->temp_day_high_f : $zero),
            "temp_month_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_month_high_f) ? $dat->temp_month_high_f : $zero),
            "temp_year_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_year_high_f) ? $dat->temp_year_high_f : $zero),
            "temp_day_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_day_low_f) ? $dat->temp_day_low_f : $zero),
            "temp_month_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_month_low_f) ? $dat->temp_month_low_f : $zero),
            "temp_year_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_year_low_f) ? $dat->temp_year_low_f : $zero),
            "temp_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_day_low_time) ? $dat->temp_day_low_time : $zero),
            "temp_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->temp_day_high_time) ? $dat->temp_day_high_time : $zero),
            "windchill_day_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->windchill_day_low_f) ? $dat->windchill_day_low_f : $zero),
            "windchill_day_low_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->windchill_day_low_time) ? $dat->windchill_day_low_time : $zero),
            "windchill_month_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->windchill_month_low_f) ? $dat->windchill_month_low_f : $zero),
            "windchill_year_low_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->windchill_year_low_f) ? $dat->windchill_year_low_f : $zero),
            "heat_index_day_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->heat_index_day_high_f) ? $dat->heat_index_day_high_f : $zero),
            "heat_index_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->heat_index_day_high_time) ? $dat->heat_index_day_high_time : $zero),
            "heat_index_month_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->heat_index_month_high_f) ? $dat->heat_index_month_high_f : $zero),
            "heat_index_year_high_f" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->heat_index_year_high_f) ? $dat->heat_index_year_high_f : $zero),
            "solar_radiation_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->solar_radiation_day_high) ? $dat->solar_radiation_day_high : $zero),
            "solar_radiation_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->solar_radiation_day_high_time) ? $dat->solar_radiation_day_high_time : $zero),
            "uv_index_day_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->uv_index_day_high) ? $dat->uv_index_day_high : $zero),
            "uv_index_day_high_time" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->uv_index_day_high_time) ? $dat->uv_index_day_high_time : $zero),
            "solar_radiation_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->solar_radiation_month_high) ? $dat->solar_radiation_month_high : $zero),
            "solar_radiation_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->solar_radiation_year_high) ? $dat->solar_radiation_year_high : $zero),
            "uv_index_month_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->uv_index_month_high) ? $dat->uv_index_month_high : $zero),
            "uv_index_year_high" => ($type == 'live' || $type == 'weewx') ?  $zero : (isset($dat->uv_index_year_high) ? $dat->uv_index_year_high : $zero),
            "rain_rate_hour_high_in_per_hr" => ($type == 'live' || $type == 'weewx') ? $zero : (isset($dat->rain_rate_hour_high_in_per_hr) ? $dat->rain_rate_hour_high_in_per_hr : $zero),

            //V2 + WX
            "time_unix" => ($type == 'live') ? (isset($data['ts'][0]) ? $data['ts'][0] : $zero) : (($type == 'weewx') ? (isset($data['datetime'][0]) ? $data['datetime'][0] : $zero) : $zero),
            "bar_trend" => ($type == 'live' || $type == 'weewx') ? (isset($data['bar_trend'][$livetab]) ? $data['bar_trend'][$livetab] : $zero) : $zero,

            //V2
           "rainfall_last_15_min_mm" => ($type == 'live') ? (isset($data['rainfall_last_15_min_mm'][$livetab]) ? $data['rainfall_last_15_min_mm'][$livetab] : $zero) : $zero,
            "rain_rate_hi_last_15_min_mm" => ($type == 'live') ? (isset($data['rain_rate_hi_last_15_min_mm'][$livetab]) ? $data['rain_rate_hi_last_15_min_mm'][$livetab] : $zero) : $zero,
            "time_zone" => ($type == 'live') ? (isset($livestation['stations'][$livenbr]['time_zone']) ? $livestation['stations'][$livenbr]['time_zone'] : $zero) : $zero,
            "et_last" => ($type == 'live') ?  (isset($data['et_last'][$livetab]) ? $data['et_last'][$livetab] : $zero) : $zero,

            //V1 + V2
            "station_id" => ($type == 'live') ? (isset($livestation['stations'][$livenbr]['station_id']) ? $livestation['stations'][$livenbr]['station_id'] : $zero) : (isset($da->station_id) ? $da->station_id : $zero),
            "et_day" => ($type == 'live') ?  (isset($data['et_day'][$livetab]) ? $data['et_day'][$livetab] : $zero) : (isset($dat->et_day) ? $dat->et_day : $zero),
            "et_month" => ($type == 'live') ?  (isset($data['et_month'][$livetab]) ? $data['et_month'][$livetab] : $zero) : (isset($dat->et_month) ? $dat->et_month : $zero),
            "et_year" => ($type == 'live') ?  (isset($data['et_year'][$livetab]) ? $data['et_year'][$livetab] : $zero) : (isset($dat->et_year) ? $dat->et_year : $zero),
            "temp_extra_1" => ($type == 'live') ? (isset($data['temp_extra_1'][$livetab]) ? $data['temp_extra_1'][$livetab] : (isset($data['temp_1'][$livetab]) ? $data['temp_1'][$livetab] : $zero)) : (isset($dat->temp_extra_1) ? $dat->temp_extra_1 : $zero),
            "temp_extra_2" => ($type == 'live') ? (isset($data['temp_extra_2'][$livetab]) ? $data['temp_extra_2'][$livetab] : (isset($data['temp_2'][$livetab]) ? $data['temp_2'][$livetab] : $zero)) : (isset($dat->temp_extra_2) ? $dat->temp_extra_2 : $zero),
            "temp_extra_3" => ($type == 'live') ? (isset($data['temp_extra_3'][$livetab]) ? $data['temp_extra_3'][$livetab] : (isset($data['temp_3'][$livetab]) ? $data['temp_3'][$livetab] : $zero)) : (isset($dat->temp_extra_3) ? $dat->temp_extra_3 : $zero),
            "temp_extra_4" => ($type == 'live') ? (isset($data['temp_extra_4'][$livetab]) ? $data['temp_extra_4'][$livetab] : (isset($data['temp_4'][$livetab]) ? $data['temp_4'][$livetab] : $zero)) : (isset($dat->temp_extra_4) ? $dat->temp_extra_4 : $zero),
            "temp_extra_5" => ($type == 'live') ? (isset($data['temp_extra_5'][$livetab]) ? $data['temp_extra_5'][$livetab] : (isset($data['temp_5'][$livetab]) ? $data['temp_5'][$livetab] : $zero)) : (isset($dat->temp_extra_5) ? $dat->temp_extra_5 : $zero),
            "temp_extra_6" => ($type == 'live') ? (isset($data['temp_extra_6'][$livetab]) ? $data['temp_extra_6'][$livetab] : (isset($data['temp_6'][$livetab]) ? $data['temp_6'][$livetab] : $zero)) : (isset($dat->temp_extra_6) ? $dat->temp_extra_6 : $zero),
            "temp_extra_7" => ($type == 'live') ? (isset($data['temp_extra_7'][$livetab]) ? $data['temp_extra_7'][$livetab] : (isset($data['temp_7'][$livetab]) ? $data['temp_7'][$livetab] : $zero)) : (isset($dat->temp_extra_7) ? $dat->temp_extra_7 : $zero),
            "relative_humidity_1" => ($type == 'live') ?  (isset($data['hum_extra_1'][$livetab]) ? $data['hum_extra_1'][$livetab] : $zero) : (isset($dat->relative_humidity_1) ? $dat->relative_humidity_1 : $zero),
            "relative_humidity_2" => ($type == 'live') ?  (isset($data['hum_extra_2'][$livetab]) ? $data['hum_extra_2'][$livetab] : $zero) : (isset($dat->relative_humidity_2) ? $dat->relative_humidity_2 : $zero),
            "relative_humidity_3" => ($type == 'live') ?  (isset($data['hum_extra_3'][$livetab]) ? $data['hum_extra_3'][$livetab] : $zero) : (isset($dat->relative_humidity_3) ? $dat->relative_humidity_3 : $zero),
            "relative_humidity_4" => ($type == 'live') ?  (isset($data['hum_extra_4'][$livetab]) ? $data['hum_extra_4'][$livetab] : $zero) : (isset($dat->relative_humidity_4) ? $dat->relative_humidity_4 : $zero),
            "relative_humidity_5" => ($type == 'live') ?  (isset($data['hum_extra_5'][$livetab]) ? $data['hum_extra_5'][$livetab] : $zero) : (isset($dat->relative_humidity_5) ? $dat->relative_humidity_5 : $zero),
            "relative_humidity_6" => ($type == 'live') ?  (isset($data['hum_extra_6'][$livetab]) ? $data['hum_extra_6'][$livetab] : $zero) : (isset($dat->relative_humidity_6) ? $dat->relative_humidity_6 : $zero),
            "relative_humidity_7" => ($type == 'live') ?  (isset($data['hum_extra_7'][$livetab]) ? $data['hum_extra_7'][$livetab] : $zero) : (isset($dat->relative_humidity_7) ? $dat->relative_humidity_7 : $zero),
            "temp_leaf_1" => ($type == 'live') ?  (isset($data['temp_leaf_1'][$livetab]) ? $data['temp_leaf_1'][$livetab] : $zero) : (isset($dat->temp_leaf_1) ? $dat->temp_leaf_1 : $zero),
            "temp_leaf_2" => ($type == 'live') ?  (isset($data['temp_leaf_2'][$livetab]) ? $data['temp_leaf_2'][$livetab] : $zero) : (isset($dat->temp_leaf_2) ? $dat->temp_leaf_2 : $zero),
            "temp_leaf_3" => ($type == 'live') ?  (isset($data['temp_leaf_3'][$livetab]) ? $data['temp_leaf_3'][$livetab] : $zero) : (isset($dat->temp_leaf_3) ? $dat->temp_leaf_3 : $zero),
            "temp_leaf_4" => ($type == 'live') ?  (isset($data['temp_leaf_4'][$livetab]) ? $data['temp_leaf_4'][$livetab] : $zero) : (isset($dat->temp_leaf_4) ? $dat->temp_leaf_4 : $zero),
            "temp_soil_1" => ($type == 'live') ?  (isset($data['temp_soil_1'][$livetab]) ? $data['temp_soil_1'][$livetab] : $zero) : (isset($dat->temp_soil_1) ? $dat->temp_soil_1 : $zero),
            "temp_soil_2" => ($type == 'live') ?  (isset($data['temp_soil_2'][$livetab]) ? $data['temp_soil_2'][$livetab] : $zero) : (isset($dat->temp_soil_2) ? $dat->temp_soil_2 : $zero),
            "temp_soil_3" => ($type == 'live') ?  (isset($data['temp_soil_3'][$livetab]) ? $data['temp_soil_3'][$livetab] : $zero) : (isset($dat->temp_soil_3) ? $dat->temp_soil_3 : $zero),
            "temp_soil_4" => ($type == 'live') ?  (isset($data['temp_soil_4'][$livetab]) ? $data['temp_soil_4'][$livetab] : $zero) : (isset($dat->temp_soil_4) ? $dat->temp_soil_4 : $zero),
            "leaf_wetness_1" => ($type == 'live') ?  (isset($data['wet_leaf_1'][$livetab]) ? $data['wet_leaf_1'][$livetab] : $zero) : (isset($dat->leaf_wetness_1) ? $dat->leaf_wetness_1 : $zero),
            "leaf_wetness_2" => ($type == 'live') ?  (isset($data['wet_leaf_2'][$livetab]) ? $data['wet_leaf_2'][$livetab] : $zero) : (isset($dat->leaf_wetness_2) ? $dat->leaf_wetness_2 : $zero),
            "leaf_wetness_3" => ($type == 'live') ?  (isset($data['wet_leaf_3'][$livetab]) ? $data['wet_leaf_3'][$livetab] : $zero) : (isset($dat->leaf_wetness_3) ? $dat->leaf_wetness_3 : $zero),
            "leaf_wetness_4" => ($type == 'live') ?  (isset($data['wet_leaf_4'][$livetab]) ? $data['wet_leaf_4'][$livetab] : $zero) : (isset($dat->leaf_wetness_4) ? $dat->leaf_wetness_4 : $zero),
            "soil_moisture_1" => ($type == 'live') ?  (isset($data['moist_soil_1'][$livetab]) ? $data['moist_soil_1'][$livetab] : $zero) : (isset($dat->soil_moisture_1) ? $dat->soil_moisture_1 : $zero),
            "soil_moisture_2" => ($type == 'live') ?  (isset($data['moist_soil_2'][$livetab]) ? $data['moist_soil_2'][$livetab] : $zero) : (isset($dat->soil_moisture_2) ? $dat->soil_moisture_2 : $zero),
            "soil_moisture_3" => ($type == 'live') ?  (isset($data['moist_soil_3'][$livetab]) ? $data['moist_soil_3'][$livetab] : $zero) : (isset($dat->soil_moisture_3) ? $dat->soil_moisture_3 : $zero),
            "soil_moisture_4" => ($type == 'live') ?  (isset($data['moist_soil_4'][$livetab]) ? $data['moist_soil_4'][$livetab] : $zero) : (isset($dat->soil_moisture_4) ? $dat->soil_moisture_4 : $zero),

            //V1 + V2 + WX
            
            
            "pressure_in" => ($type == 'live') ? (isset($data['bar_absolute'][$livetab]) ? $data['bar_absolute'][$livetab] : (isset($data['bar'][$livetab]) ? $data['bar'][$livetab] : $zero)) : (($type == 'weewx') ? (isset($data['altimeter_inHg'][0]) ? $data['altimeter_inHg'][0] :  $zero) : (isset($da->pressure_in) ? $da->pressure_in : $zero)),
            "temp_f" => ($type == 'live') ? (isset($data['temp'][$livetab]) ? $data['temp'][$livetab] : (isset($data['temp_out'][$livetab]) ? $data['temp_out'][$livetab] : $zero)) : (($type == 'weewx') ? (isset($data['temp_F'][0]) ? $data['temp_F'][0] : $zero) : (isset($da->temp_f) ? $da->temp_f : $zero)),
            "temp_in_f" => ($type == 'live') ? (isset($data['temp_in'][$livetab]) ? $data['temp_in'][$livetab] : $zero) : (($type == 'weewx') ? (isset($data['temp_in_F'][0]) ? $data['temp_in_F'][0] : $zero) : (isset($dat->temp_in_f) ? $dat->temp_in_f : $zero)),
            "windchill_f" => ($type == 'live') ? (isset($data['wind_chill'][$livetab]) ? $data['wind_chill'][$livetab] : $zero) : (($type == 'weewx') ? (isset($data['wind_chill_F'][0]) ? $data['wind_chill_F'][0] : $zero) : (isset($da->windchill_f) ? $da->windchill_f : $zero)),
            "heat_index_f" => ($type == 'live') ? (isset($data['heat_index'][$livetab]) ? $data['heat_index'][$livetab] : $zero) : (($type == 'weewx') ? (isset($data['heat_index_F'][0]) ? $data['heat_index_F'][0] : $zero) : (isset($da->heat_index_f) ? $da->heat_index_f : $zero)),
            "dewpoint_f" => ($type == 'live') ? (isset($data['dew_point'][$livetab]) ? $data['dew_point'][$livetab] : $zero) : (($type == 'weewx') ? (isset($data['dew_point_F'][0]) ? $data['dew_point_F'][0] : $zero) : (isset($da->dewpoint_f) ? $da->dewpoint_f : $zero)),
            "latitude" => ($type == 'live') ? (isset($livestation['stations'][$livenbr]['latitude']) ? $livestation['stations'][$livenbr]['latitude'] : $zero) : (($type == 'weewx') ? (isset($data['latitude'][0]) ? $data['latitude'][0] : $zero) : (isset($da->latitude) ? $da->latitude : $zero)),
            "longitude" => ($type == 'live') ? (isset($livestation['stations'][$livenbr]['longitude']) ? $livestation['stations'][$livenbr]['longitude'] : $zero) : (($type == 'weewx') ? (isset($data['longitude'][0]) ? $data['longitude'][0] : $zero) : (isset($da->longitude) ? $da->longitude : $zero)),
            "station_name" => ($type == 'live') ? (isset($livestation['stations'][$livenbr]['station_name']) ? $livestation['stations'][$livenbr]['station_name'] : $zero) : (($type == 'weewx') ? (isset($data['station'][0]) ? $data['station'][0] : $zero) : (isset($dat->station_name) ? $dat->station_name : $zero)),
            "location" => ($type == 'live') ? (isset($livestation['stations'][$livenbr]['city']) ? $livestation['stations'][$livenbr]['city'] : $zero) : (($type == 'weewx') ? (isset($data['station'][0]) ? $data['station'][0] : $zero) : (isset($da->location) ? $da->location : $zero)),
            "relative_humidity" => ($type == 'live' || $type == 'weewx') ? (isset($data['hum'][$livetab]) ? round((floatval($data['hum'][$livetab])), 0)  : (isset($data['hum_out'][$livetab]) ? $data['hum_out'][$livetab] : $zero)) : (isset($da->relative_humidity) ? $da->relative_humidity : $zero),
            "relative_humidity_in" => ($type == 'live' || $type == 'weewx') ? (isset($data['hum_in'][$livetab]) ? round((floatval($data['hum_in'][$livetab])), 0) : $zero) : (isset($dat->relative_humidity_in) ? $dat->relative_humidity_in : $zero),
            "solar_radiation" => ($type == 'live' || $type == 'weewx') ? (isset($data['solar_rad'][$livetab]) ? $data['solar_rad'][$livetab] : $zero) : (isset($dat->solar_radiation) ? $dat->solar_radiation : $zero),
            "uv_index" => ($type == 'live' || $type == 'weewx') ? (isset($data['uv_index'][$livetab]) ? $data['uv_index'][$livetab] : (isset($data['uv'][$livetab]) ? $data['uv'][$livetab] : $zero)) : (isset($dat->uv_index) ? $dat->uv_index : $zero),
            "rain_day_in" => ($type == 'live' || $type == 'weewx') ? (isset($data['rainfall_last_24_hr_in'][$livetab]) ? $data['rainfall_last_24_hr_in'][$livetab] : (isset($data['rain_day_in'][$livetab]) ? $data['rain_day_in'][$livetab] : $zero)) : (isset($dat->rain_day_in) ? $dat->rain_day_in : $zero),
            "rain_month_in" => ($type == 'live') ? (isset($data['rainfall_monthly_in'][$livetab]) ? $data['rainfall_monthly_in'][$livetab] : (isset($data['rain_month_in'][$livetab]) ? $data['rain_month_in'][$livetab] : $zero)) : (($type == 'weewx') ? (isset($data['rain_month_in'][0]) ? $data['rain_month_in'][0] : $zero) : (isset($dat->rain_month_in) ? $dat->rain_month_in : $zero)),
            "rain_year_in" => ($type == 'live') ? (isset($data['rainfall_year_in'][$livetab]) ? $data['rainfall_year_in'][$livetab] : (isset($data['rain_year_in'][$livetab]) ? $data['rain_year_in'][$livetab] : $zero)) : (($type == 'weewx') ? (isset($data['rain_year_in'][0]) ? $data['rain_year_in'][0] : $zero) : (isset($dat->rain_year_in) ? $dat->rain_year_in : $zero)),
            "rain_rate_in_per_hr" => ($type == 'live' || $type == 'weewx') ? (isset($data['rain_rate_last_in'][$livetab]) ? $data['rain_rate_last_in'][$livetab] : ($type == 'live' ? (isset($data['rain_rate_in'][$livetab]) ? $data['rain_rate_in'][$livetab] : $zero) : $zero)) : (isset($dat->rain_rate_in_per_hr) ? $dat->rain_rate_in_per_hr : $zero),
            "wind_degrees" => ($type == 'live' || $type == 'weewx') ? (isset($data['wind_dir_last'][$livetab]) ? $data['wind_dir_last'][$livetab] : (isset($data['wind_dir'][$livetab]) ? $data['wind_dir'][$livetab] : $zero)) : (isset($da->wind_degrees) ? $da->wind_degrees : $zero),
            "wind_ten_min_gust_mph" => ($type == 'live') ? (isset($data['wind_speed_hi_last_10_min'][$livetab]) ? $data['wind_speed_hi_last_10_min'][$livetab] : (isset($data['wind_gust_10_min'][$livetab]) ? $data['wind_gust_10_min'][$livetab] : $zero)) : (($type == 'weewx') ? (isset($data['wind_speed_hi_last_10_min_mph'][0]) ? $data['wind_speed_hi_last_10_min_mph'][0] : $zero) : (isset($dat->wind_ten_min_gust_mph) ? $dat->wind_ten_min_gust_mph : $zero)),
            "wind_ten_min_avg_mph" => ($type == 'live') ? (isset($data['wind_speed_avg_last_10_min'][$livetab]) ? $data['wind_speed_avg_last_10_min'][$livetab] : (isset($data['wind_speed_10_min_avg'][$livetab]) ? $data['wind_speed_10_min_avg'][$livetab] : $zero)) : (($type == 'weewx') ? (isset($data['wind_speed_avg_last_10_min_mph'][0]) ? $data['wind_speed_avg_last_10_min_mph'][0] :  $zero) : (isset($dat->wind_ten_min_avg_mph) ? $dat->wind_ten_min_avg_mph : $zero)),


            //WEEWX
            "offset" => ($type == 'weewx') ? (isset($data['time_zone'][0]) ? $data['time_zone'][0] : $zero) : $zero,

        );

        return $response;
    }

    /**
     * Test datas Json amélioré pour uniformisé Apiv1 - Weatherlink Live - Weewx
     */
    public function getAPIDatasUp($datas, $station, $livestation, $livenbr, $livetab)
    {
        $zero = '&#8709;';
        $apiDatas = $this->getAPIDatas($datas, $station, $livestation, $livenbr, $livetab);
        $type = (isset($station['stat_type'])) ? $station['stat_type'] : $zero;

        $timezone = ($type == 'weewx') ? $this->timeZoneWeewx($apiDatas['offset']) : $apiDatas['time_zone'];

        $data = array(
            "time" => ($type == 'live') ? $this->liveDateRFC822($apiDatas['time_unix'], $timezone) : (($type == 'weewx') ? $this->weewxDateRFC822($apiDatas['time_unix'], $apiDatas['offset']) : $apiDatas['time_RFC822']),
            "pressure_tendency" => ($type == 'live' || $type == 'weewx') ? $this->livePressTrend($apiDatas['bar_trend']) : $apiDatas['pressure_tendency_string'],
            "fuseau" => ($type == 'live' || $type == 'weewx') ? $timezone : $this->timeZone($apiDatas['time_RFC822']),
            "time_sunset" => ($type == 'live' || $type == 'weewx') ? $this->liveDateSun($apiDatas['time_unix'], $apiDatas['latitude'], $apiDatas['longitude'], $timezone, 'sunset') : $apiDatas['sunset'],
            "time_sunrise" => ($type == 'live' || $type == 'weewx') ? $this->liveDateSun($apiDatas['time_unix'], $apiDatas['latitude'], $apiDatas['longitude'], $timezone, 'sunrise') : $apiDatas['sunrise'],
            "mb_pressure" => ($type == 'live' || $type == 'weewx') ? $this->pressIntoMb($apiDatas['pressure_in']) : $apiDatas['pressure_in'],
            "c_temp" => ($type == 'live' || $type == 'weewx') ?  $this->getTempFtoC($apiDatas['temp_f'])  : $apiDatas['temp_c'],
        );

        return $data;
    }

    /**
     * TITRES CASES POUR SELECT PREF
     */

    public function tabTxt($config, $tab)
    {
        $tab_txt = array(
            "0" => array(
                "txt" => '',
                "text" => ''
            ),
            "1" => array(
                "txt" => $this->l->trad('AVG_WIND'),
                "text" => $this->l->trad('AVERAGE_WIND')
            ),
            "2" => array(
                "txt" => $this->l->trad('TEMP'),
                "text" => $this->l->trad('TEMPERATURE')
            ),
            "3" => array(
                "txt" => $this->l->trad('RAIN_RATE'),
                "text" => $this->l->trad('RAINY_INTENSITY')
            ),
            "4" => array(
                "txt" => $this->l->trad('GUST'),
                "text" => $this->l->trad('GUST_WIND')
            ),
            "5" => array(
                "txt" => $this->l->trad('FEELS_LIKE'),
                "text" => $this->l->trad('FEELS_LIKE_TEMPERATURE')
            ),
            "6" => array(
                "txt" => $this->l->trad('CUMULATIVE_RAIN'),
                "text" => $this->l->trad('CUMULATIVE_RAINFALL')
            ),
            "7" => array(
                "txt" => $this->l->trad('MAX_GUST'),
                "text" => $this->l->trad('HIGHEST_GUST')
            ),
            "8" => array(
                "txt" => $this->SunTxt($config, $tab),
                "text" => $this->SunText($config, $tab)
            ),
            "9" => array(
                "txt" => $this->l->trad('EVAPOT'),
                "text" => $this->l->trad('EVAPOTRANSPIRATION')
            ),
            "10" => array(
                "txt" => $this->l->trad('PRESSURE'),
                "text" => $this->l->trad('PRESSURE')
            ),
            "11" => array(
                "txt" => $this->l->trad('DEWPT'),
                "text" => $this->l->trad('DEW_POINT')
            ),
            "12" => array(
                "txt" => $this->l->trad('HUMIDITY'),
                "text" => $this->l->trad('RELATIVE_HUMIDITY')
            ),
            "13" => array(
                "txt" => $this->l->trad('MONTH_PRECIP'),
                "text" => $this->l->trad('MONTHLY_PRECIPITATION')
            ),
            "14" => array(
                "txt" => $this->l->trad('YEAR_PRECIP'),
                "text" => $this->l->trad('ANNUAL_PRECIPITATION')
            ),
            "15" => array(
                "txt" => $this->l->trad('EXTRA_TEMP') . ' 1',
                "text" => $this->l->trad('EXTRA_TEMPERATURE') . ' 1'
            ),
            "16" => array(
                "txt" => $this->l->trad('EXTRA_TEMP') . ' 2',
                "text" => $this->l->trad('EXTRA_TEMPERATURE') . ' 2'
            ),
            "17" => array(
                "txt" => $this->l->trad('EXTRA_TEMP') . ' 3',
                "text" => $this->l->trad('EXTRA_TEMPERATURE') . ' 3'
            ),
            "18" => array(
                "txt" => $this->l->trad('EXTRA_TEMP') . ' 4',
                "text" => $this->l->trad('EXTRA_TEMPERATURE') . ' 4'
            ),
            "19" => array(
                "txt" => $this->l->trad('EXTRA_TEMP') . ' 5',
                "text" => $this->l->trad('EXTRA_TEMPERATURE') . ' 5'
            ),
            "20" => array(
                "txt" => $this->l->trad('EXTRA_TEMP') . ' 6',
                "text" => $this->l->trad('EXTRA_TEMPERATURE') . ' 6'
            ),
            "21" => array(
                "txt" => $this->l->trad('EXTRA_TEMP') . ' 7',
                "text" => $this->l->trad('EXTRA_TEMPERATURE') . ' 7'
            ),
            "22" => array(
                "txt" => $this->l->trad('SUN'),
                "text" => $this->l->trad('SOLAR_RADIATIONS')
            ),
            "23" => array(
                "txt" => $this->l->trad('UV'),
                "text" => $this->l->trad('UV_INDEX')
            ),
            "24" => array(
                "txt" => $this->l->trad('TEMP_L') . ' 1',
                "text" => $this->l->trad('TEMPERATURE_LEAF') . ' 1'
            ),
            "25" => array(
                "txt" => $this->l->trad('TEMP_L') . ' 2',
                "text" => $this->l->trad('TEMPERATURE_LEAF') . ' 2'
            ),
            "26" => array(
                "txt" => $this->l->trad('TEMP_S') . ' 1',
                "text" => $this->l->trad('TEMPERATURE_SOIL') . ' 1'
            ),
            "27" => array(
                "txt" => $this->l->trad('TEMP_S') . ' 2',
                "text" => $this->l->trad('TEMPERATURE_SOIL') . ' 2'
            ),
            "28" => array(
                "txt" => $this->l->trad('TEMP_S') . ' 3',
                "text" => $this->l->trad('TEMPERATURE_SOIL') . ' 3'
            ),
            "29" => array(
                "txt" => $this->l->trad('TEMP_S') . ' 4',
                "text" => $this->l->trad('TEMPERATURE_SOIL') . ' 4'
            ),
            "30" => array(
                "txt" => $this->l->trad('HUMIDITY') . ' 1',
                "text" => $this->l->trad('RELATIVE_HUMIDITY') . ' 1'
            ),
            "31" => array(
                "txt" => $this->l->trad('HUMIDITY') . ' 2',
                "text" => $this->l->trad('RELATIVE_HUMIDITY') . ' 2'
            ),
            "32" => array(
                "txt" => $this->l->trad('HUMIDITY') . ' 3',
                "text" => $this->l->trad('RELATIVE_HUMIDITY') . ' 3',
                "icon" => '<i class="wi wi-strong-wind"></i>'
            ),
            "33" => array(
                "txt" => $this->l->trad('HUMIDITY') . ' 4',
                "text" => $this->l->trad('RELATIVE_HUMIDITY') . ' 4'
            ),
            "34" => array(
                "txt" => $this->l->trad('HUMIDITY') . ' 5',
                "text" => $this->l->trad('RELATIVE_HUMIDITY') . ' 5'
            ),
            "35" => array(
                "txt" => $this->l->trad('HUMIDITY') . ' 6',
                "text" => $this->l->trad('RELATIVE_HUMIDITY') . ' 6'
            ),
            "36" => array(
                "txt" => $this->l->trad('HUMIDITY') . ' 7',
                "text" => $this->l->trad('RELATIVE_HUMIDITY') . ' 7'
            ),
            "37" => array(
                "txt" => $this->l->trad('LWET') . ' 1',
                "text" => $this->l->trad('LEAF_WETNESS') . ' 1'
            ),
            "38" => array(
                "txt" => $this->l->trad('LWET') . ' 2',
                "text" => $this->l->trad('LEAF_WETNESS') . ' 2'
            ),
            "39" => array(
                "txt" => $this->l->trad('SOIL') . ' 1',
                "text" => $this->l->trad('SOIL_MOISTURE') . ' 1'
            ),
            "40" => array(
                "txt" => $this->l->trad('SOIL') . ' 2',
                "text" => $this->l->trad('SOIL_MOISTURE') . ' 2'
            ),
            "41" => array(
                "txt" => $this->l->trad('SOIL') . ' 3',
                "text" => $this->l->trad('SOIL_MOISTURE') . ' 3'
            ),
            "42" => array(
                "txt" => $this->l->trad('SOIL') . ' 4',
                "text" => $this->l->trad('SOIL_MOISTURE') . ' 4'
            ),
            "43" => array(
                "txt" => $this->l->trad('TEMP_IN'),
                "text" => $this->l->trad('TEMPERATURE_IN')
            ),
            "44" => array(
                "txt" => $this->l->trad('RH_IN'),
                "text" => $this->l->trad('HUMIDITY_IN')
            ),
            "46" => array(
                "txt" => $this->l->trad('WIND_DIR'),
                "text" => $this->l->trad('WIND_DIRECTION')
            ),
            "47" => array(
                "txt" => $this->l->trad('TEMP_L') . ' 3',
                "text" => $this->l->trad('TEMPERATURE_LEAF') . ' 3'
            ),
            "48" => array(
                "txt" => $this->l->trad('TEMP_L') . ' 4',
                "text" => $this->l->trad('TEMPERATURE_LEAF') . ' 4'
            ),
            "49" => array(
                "txt" => $this->l->trad('LWET') . ' 3',
                "text" => $this->l->trad('LEAF_WETNESS') . ' 3'
            ),
            "50" => array(
                "txt" => $this->l->trad('LWET') . ' 4',
                "text" => $this->l->trad('LEAF_WETNESS') . ' 4'
            ),
        );

        return $tab_txt;
    }

    /**
     * TITRES CASE POUR HOME version 1
     */
    public function incUp1($datas, $switch, $config, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas =  $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);
        $apiDatasUP =  $this->getAPIDatasUp($datas, $info, $livestation, $livenbr, $livetab);

        $inc = array(
            "0" => array(
                "H2_TXT" => '',
                "H2_TEXT" => '',
                "ICON" =>  '',
                "ICON_TOOLTIP" => '',
            ),
            "1" => array(
                "H2_TXT" => $this->l->trad('AVG_WIND'),
                "H2_TEXT" => $this->l->trad('AVERAGE_WIND'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-strong-wind"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-strong-wind"></i>', $this->l->trad('AVG_WIND'), ''),
            ),
            "2" => array(
                "H2_TXT" => $this->l->trad('TEMP'),
                "H2_TEXT" => $this->l->trad('TEMPERATURE'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i>', $this->l->trad('TEMP'), ''),
            ),
            "3" => array(
                "H2_TXT" => $this->l->trad('RAIN_RATE'),
                "H2_TEXT" => $this->l->trad('RAINY_INTENSITY'),
                "ICON" => $this->getIcon($switch, '<i class="tab_mid_size_26 top_5 wi wi-raindrops"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="tab_mid_size_26 top_5 wi wi-raindrops"></i>', $this->l->trad('RAIN_RATE'), ''),
            ),
            "4" => array(
                "H2_TXT" => $this->l->trad('GUST'),
                "H2_TEXT" => $this->l->trad('GUST_WIND'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-cloudy-gusts"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-cloudy-gusts"></i>', $this->l->trad('GUST'), ''),
            ),
            "5" => array(
                "H2_TXT" => $this->l->trad('FEEL_LIKE'),
                "H2_TEXT" => $this->l->trad('FEEL_LIKE_TEMP'),
                "ICON" => $this->getIcon($switch, '\'<i class="wi wi-thermometer-exterior"></i>\''),
                "ICON_TOOLTIP" => ($this->is_Temp('59', $apiDatas['temp_f']) == true) ? $this->getIconTooltip($switch, '\'<i class="wi wi-thermometer-exterior"></i>\'', $this->l->trad('WINDCHILL_SMALL'), '') : $this->getIconTooltip($switch, '\'<i class="wi wi-thermometer-exterior"></i>\'', $this->l->trad('HEAT'), ''),
            ),
            "6" => array(
                "H2_TXT" => $this->l->trad('CUMULATIVE_RAIN'),
                "H2_TEXT" => $this->l->trad('CUMULATIVE_RAINFALL'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-umbrella"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-umbrella"></i>', $this->l->trad('CUMULATIVE_RAIN'), ''),
            ),
            "7" => array(
                "H2_TXT" => $this->l->trad('MAX_GUST'),
                "H2_TEXT" => $this->l->trad('HIGHEST_GUST'),
                "ICON" => $this->getIcon($switch, '+<i class="wi wi-cloudy-gusts"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '+<i class="wi wi-cloudy-gusts"></i>', $this->l->trad('MAX_GUST'), ''),
            ),
            "9" => array(
                "H2_TXT" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->l->trad('PET') : $this->l->trad('ETA'),
                "H2_TEXT" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->l->trad('POTENTIAL_EVAPO') : $this->l->trad('ACTUAL_EVAPO'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-cloud-up"></i>'),
                "ICON_TOOLTIP" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->getIconTooltip($switch, '<i class="wi wi-cloud-up"></i>', $this->l->trad('PET'), '') : $this->getIconTooltip($switch, '<i class="wi wi-cloud-up"></i>', $this->l->trad('ETA'), ''),
            ),
            "10" => array(
                "H2_TXT" => '<img class="arrowpress" alt="arrow"  src="' . $this->pressImg($apiDatasUP['pressure_tendency'])  . '" />',
                "H2_TEXT" => $this->l->trad('PRESSURE'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-barometer"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-barometer"></i>', $this->l->pressTrad($apiDatasUP['pressure_tendency'], $this->l->getLg()), ' <img class="arrowpress" alt="arrow"  src="' . $this->pressImg($apiDatasUP['pressure_tendency'])  . '" />'),
            ),
            "11" => array(
                "H2_TXT" => $this->l->trad('DEWPT'),
                "H2_TEXT" => $this->l->trad('DEW_POINT'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><i class="wi wi-raindrops"></i>'),
                "ICON_TOOLTIP" =>  $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><i class="wi wi-raindrops"></i>', $this->l->trad('DEWPT'), ''),
            ),
            "12" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY'),
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i>'),
                "ICON_TOOLTIP" =>  $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i>', $this->l->trad('HUMIDITY'), ''),
            ),
            "13" => array(
                "H2_TXT" => $this->l->trad('MONTH_PRECIP'),
                "H2_TEXT" => $this->l->trad('MONTHLY_PRECIPITATION'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-raindrop"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-raindrop"></i>', $this->l->trad('MONTH_PRECIP'), ' ' . $this->l->trad('M')),
            ),
            "14" => array(
                "H2_TXT" => $this->l->trad('YEAR_PRECIP'),
                "H2_TEXT" => $this->l->trad('ANNUAL_PRECIPITATION'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-raindrop"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-raindrop"></i>', $this->l->trad('YEAR_PRECIP'), ' ' . $this->l->trad('Y')),
            ),
            "15" => array(
                "H2_TXT" => $this->l->trad('EXTRA_TEMP') . ' 1',
                "H2_TEXT" => $this->l->trad('EXTRA_TEMPERATURE') . ' 1',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">1</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">1</span>', $this->l->trad('EXTRA_TEMP') . ' 1', ''),
            ),
            "16" => array(
                "H2_TXT" => $this->l->trad('EXTRA_TEMP') . ' 2',
                "H2_TEXT" => $this->l->trad('EXTRA_TEMPERATURE') . ' 2',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">2</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">2</span>', $this->l->trad('EXTRA_TEMP') . ' 2', ''),
            ),
            "17" => array(
                "H2_TXT" => $this->l->trad('EXTRA_TEMP') . ' 3',
                "H2_TEXT" => $this->l->trad('EXTRA_TEMPERATURE') . ' 3',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">3</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">3</span>', $this->l->trad('EXTRA_TEMP') . ' 3', ''),
            ),
            "18" => array(
                "H2_TXT" => $this->l->trad('EXTRA_TEMP') . ' 4',
                "H2_TEXT" => $this->l->trad('EXTRA_TEMPERATURE') . ' 4',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">4</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">4</span>', $this->l->trad('EXTRA_TEMP') . ' 4', ''),
            ),
            "19" => array(
                "H2_TXT" => $this->l->trad('EXTRA_TEMP') . ' 5',
                "H2_TEXT" => $this->l->trad('EXTRA_TEMPERATURE') . ' 5',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">5</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">5</span>', $this->l->trad('EXTRA_TEMP') . ' 5', ''),
            ),
            "20" => array(
                "H2_TXT" => $this->l->trad('EXTRA_TEMP') . ' 6',
                "H2_TEXT" => $this->l->trad('EXTRA_TEMPERATURE') . ' 6',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">6</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">6</span>', $this->l->trad('EXTRA_TEMP') . ' 6', ''),
            ),
            "21" => array(
                "H2_TXT" => $this->l->trad('EXTRA_TEMP') . ' 7',
                "H2_TEXT" => $this->l->trad('EXTRA_TEMPERATURE') . ' 7',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">7</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-thermometer"></i><span class="tab_mid_size_11 left_1 top_5">7</span>', $this->l->trad('EXTRA_TEMP') . ' 7', ''),
            ),
            "22" => array(
                "H2_TXT" => $this->l->trad('SUN'),
                "H2_TEXT" => $this->l->trad('SOLAR_RADIATIONS'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-day-sunny"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-day-sunny"></i>', $this->l->trad('SUN'), ''),
            ),
            "23" => array(
                "H2_TXT" => $this->l->trad('UV'),
                "H2_TEXT" => $this->l->trad('UV_INDEX'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-hot"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-hot"></i>', $this->l->trad('UV'), ''),
            ),
            "24" => array(
                "H2_TXT" => $this->l->trad('TEMP_L') . ' 1',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_LEAF') . ' 1',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">1</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i></i><span class="right_5 tab_mid_size_11 top_5">1</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>', $this->l->trad('TEMP_L') . ' 1', ''),
            ),
            "25" => array(
                "H2_TXT" => $this->l->trad('TEMP_L') . ' 2',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_LEAF') . ' 2',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i></i><span class="right_5 tab_mid_size_11 top_5">2</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i></i><span class="right_5 tab_mid_size_11 top_5">2</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>', $this->l->trad('TEMP_L') . ' 2', ''),
            ),
            "26" => array(
                "H2_TXT" => $this->l->trad('TEMP_S') . ' 1',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_SOIL') . ' 1',
                "ICON" => $this->getIcon($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">1</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">1</span>', $this->l->trad('TEMP_S') . ' 1', ''),
            ),
            "27" => array(
                "H2_TXT" => $this->l->trad('TEMP_S') . ' 2',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_SOIL') . ' 2',
                "ICON" => $this->getIcon($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">2</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">2</span>', $this->l->trad('TEMP_S') . ' 2', ''),
            ),
            "28" => array(
                "H2_TXT" => $this->l->trad('TEMP_S') . ' 3',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_SOIL') . ' 3',
                "ICON" => $this->getIcon($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">3</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">3</span>', $this->l->trad('TEMP_S') . ' 3', ''),
            ),
            "29" => array(
                "H2_TXT" => $this->l->trad('TEMP_S') . ' 4',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_SOIL') . ' 4',
                "ICON" => $this->getIcon($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">4</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="wi wi-thermometer"></i>-<span class="right_1 tab_mid_size_11 top_5">4</span>', $this->l->trad('TEMP_S') . ' 4', ''),
            ),
            "30" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY') . ' 1',
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY') . ' 1',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">1</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">1</span>', $this->l->trad('HUMIDITY') . ' 1', ''),
            ),
            "31" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY') . ' 2',
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY') . ' 2',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">2</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">2</span>', $this->l->trad('HUMIDITY') . ' 2', ''),
            ),
            "32" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY') . ' 3',
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY') . ' 3',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">3</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">3</span>', $this->l->trad('HUMIDITY') . ' 3', ''),
            ),
            "33" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY') . ' 4',
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY') . ' 4',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">4</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">4</span>', $this->l->trad('HUMIDITY') . ' 4', ''),
            ),
            "34" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY') . ' 5',
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY') . ' 5',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">5</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">5</span>', $this->l->trad('HUMIDITY') . ' 5', ''),
            ),
            "35" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY') . ' 6',
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY') . ' 6',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">6</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">6</span>', $this->l->trad('HUMIDITY') . ' 6', ''),
            ),
            "36" => array(
                "H2_TXT" => $this->l->trad('HUMIDITY') . ' 7',
                "H2_TEXT" => $this->l->trad('RELATIVE_HUMIDITY') . ' 7',
                "ICON" => $this->getIcon($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">7</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-humidity"></i><span class="tab_mid_size_11 left_1 top_5">7</span>', $this->l->trad('HUMIDITY') . ' 7', ''),
            ),
            "37" => array(
                "H2_TXT" => $this->l->trad('LWET') . ' 1',
                "H2_TEXT" => $this->l->trad('LEAF_WETNESS') . ' 1',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">1</span><i class="right_6 wi wi-raindrops"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">1</span><i class="right_6 wi wi-raindrops"></i>', $this->l->trad('LWET') . ' 1', ''),
            ),
            "38" => array(
                "H2_TXT" => $this->l->trad('LWET') . ' 2',
                "H2_TEXT" => $this->l->trad('LEAF_WETNESS') . ' 2',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">2</span><i class="right_6 wi wi-raindrops"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">2</span><i class="right_6 wi wi-raindrops"></i>', $this->l->trad('LWET') . ' 2', ''),
            ),
            "39" => array(
                "H2_TXT" => $this->l->trad('SOIL') . ' 1',
                "H2_TEXT" => $this->l->trad('SOIL_MOISTURE') . ' 1',
                "ICON" => $this->getIcon($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">1</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">1</span>', $this->l->trad('SOIL') . ' 1', ''),
            ),
            "40" => array(
                "H2_TXT" => $this->l->trad('SOIL') . ' 2',
                "H2_TEXT" => $this->l->trad('SOIL_MOISTURE') . ' 2',
                "ICON" => $this->getIcon($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">2</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">2</span>', $this->l->trad('SOIL') . ' 2', ''),
            ),
            "41" => array(
                "H2_TXT" => $this->l->trad('SOIL') . ' 3',
                "H2_TEXT" => $this->l->trad('SOIL_MOISTURE') . ' 3',
                "ICON" => $this->getIcon($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">3</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">3</span>', $this->l->trad('SOIL') . ' 3', ''),
            ),
            "42" => array(
                "H2_TXT" => $this->l->trad('SOIL') . ' 4',
                "H2_TEXT" => $this->l->trad('SOIL_MOISTURE') . ' 4',
                "ICON" => $this->getIcon($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">4</span>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '-<i class="fas fa-tint"></i>-</span><span class="right_1 tab_mid_size_11 top_5">4</span>', $this->l->trad('SOIL') . ' 4', ''),
            ),
            "43" => array(
                "H2_TXT" => $this->l->trad('TEMP_IN'),
                "H2_TEXT" => $this->l->trad('TEMPERATURE_IN'),
                "ICON" => $this->getIcon($switch, '[<i class="wi wi-thermometer"></i>]'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '[<i class="wi wi-thermometer"></i>]', $this->l->trad('TEMP_IN'), ''),
            ),
            "44" => array(
                "H2_TXT" => $this->l->trad('RH_IN'),
                "H2_TEXT" => $this->l->trad('HUMIDITY_IN'),
                "ICON" => $this->getIcon($switch, '[<i class="wi wi-humidity"></i>]'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '[<i class="wi wi-humidity"></i>]', $this->l->trad('RH_IN'), ''),
            ),
            "45" => array(
                "H2_TXT" => $this->l->trad('DAY_RAIN'),
                "H2_TEXT" => $this->l->trad('PRECIPITATION_TODAY'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-raindrop"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="wi wi-raindrop"></i>', $this->l->trad('DAY_RAIN'), ' ' . $this->l->trad('D')),
            ),
            "46" => array(
                "H2_TXT" => $this->l->trad('WIND_DIR'),
                "H2_TEXT" => $this->l->trad('WIND_DIRECTION'),
                "ICON" => $this->getIcon($switch, '<i class="far fa-compass"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="far fa-compass"></i>', $this->l->trad('WIND_DIR'), ''),
            ),
            "47" => array(
                "H2_TXT" => $this->l->trad('TEMP_L') . ' 3',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_LEAF') . ' 3',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i></i><span class="right_5 tab_mid_size_11 top_5">3</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i></i><span class="right_5 tab_mid_size_11 top_5">3</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>', $this->l->trad('TEMP_L') . ' 3', ''),
            ),
            "48" => array(
                "H2_TXT" => $this->l->trad('TEMP_L') . ' 4',
                "H2_TEXT" => $this->l->trad('TEMPERATURE_LEAF') . ' 4',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i></i><span class="right_5 tab_mid_size_11 top_5">4</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i></i><span class="right_5 tab_mid_size_11 top_5">4</span><i class="right_4 bottom_3 wi wi-thermometer-internal"></i>', $this->l->trad('TEMP_L') . ' 4', ''),
            ),
            "49" => array(
                "H2_TXT" => $this->l->trad('LWET') . ' 3',
                "H2_TEXT" => $this->l->trad('LEAF_WETNESS') . ' 3',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">3</span><i class="right_6 wi wi-raindrops"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">3</span><i class="right_6 wi wi-raindrops"></i>', $this->l->trad('LWET') . ' 3', ''),
            ),
            "50" => array(
                "H2_TXT" => $this->l->trad('LWET') . ' 4',
                "H2_TEXT" => $this->l->trad('LEAF_WETNESS') . ' 4',
                "ICON" => $this->getIcon($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">4</span><i class="right_6 wi wi-raindrops"></i>'),
                "ICON_TOOLTIP" => $this->getIconTooltip($switch, '<i class="fas fa-seedling"></i><span class="right_5 tab_mid_size_11 top_5">4</span><i class="right_6 wi wi-raindrops"></i>', $this->l->trad('LWET') . ' 4', ''),
            )
        );

        return $inc;
    }

    /**
     * MILIEU CASE POUR HOME version 1
     */
    public function incMid1($datas, $switch, $config, $info, $livestation, $livenbr, $livetab)
    {
        $zero = '&#8709;';
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);

        $inc = array(
            "0" => array(
                "_VALUE_MAIN" => '',
                "_UNIT" => '',
                "_CLASS_UNIT_SMALL" => '',
                "_CLASS_UNIT_LARGE" => '',
                "color" => ''
            ),
            "1" => array(
                "_VALUE_MAIN" => $this->getWind($switch, $apiDatas['wind_ten_min_avg_mph']),
                "_UNIT" => $this->getUnit($switch, 'wind'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colWind($switch, $apiDatas['wind_ten_min_avg_mph'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "2" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_f']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_f'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "3" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $apiDatas['rain_rate_in_per_hr']),
                "_UNIT" => $this->getUnit($switch, 'rain') . '/h',
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $apiDatas['rain_rate_in_per_hr'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "4" => array(
                "_VALUE_MAIN" => $this->getWind($switch, $apiDatas['wind_ten_min_gust_mph']),
                "_UNIT" => $this->getUnit($switch, 'wind'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colWind($switch, $apiDatas['wind_ten_min_gust_mph'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "5" => array(
                "_VALUE_MAIN" => ($this->is_Temp('59', $apiDatas['temp_f']) == true) ? $this->getTemp($switch, $apiDatas['windchill_f']) : $this->getTemp($switch, $apiDatas['heat_index_f']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => ($this->is_Temp('59', $apiDatas['temp_f']) == true) ? $this->col->colWindchill($switch, $apiDatas['windchill_f'], $datas, $info, $livestation, $livenbr, $livetab) : $this->col->colHeat($switch, $apiDatas['heat_index_f'], $datas, $info, $livestation, $livenbr)
            ),
            "6" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $apiDatas['rain_day_in']),
                "_UNIT" => $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $apiDatas['rain_day_in'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "7" => array(
                "_VALUE_MAIN" => $this->getWind($switch, $apiDatas['wind_day_high_mph']),
                "_UNIT" => $this->getUnit($switch, 'wind'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colWind($switch, $apiDatas['wind_day_high_mph'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "9" => array(
                "_VALUE_MAIN" => (($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? ($apiDatas['et_day'] != $zero ? $this->getRain($switch, $apiDatas['et_day']) :  $this->getRain($switch, $apiDatas['et_last'])) : $this->getRain($switch, $this->ETR_in($apiDatas['temp_day_high_f'], $apiDatas['temp_day_low_f'], $apiDatas['rain_day_in']))),
                "_UNIT" =>  $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->col->colRain($switch, $apiDatas['et_day'], $datas, $info, $livestation, $livenbr, $livetab) : $this->col->colRain($switch, $this->ETR_in($apiDatas['temp_day_high_f'], $apiDatas['temp_day_low_f'], $apiDatas['rain_day_in']), $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "11" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['dewpoint_f']),
                "_UNIT" =>  $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['dewpoint_f'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "12" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "13" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $apiDatas['rain_month_in']),
                "_UNIT" => $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $apiDatas['rain_month_in'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "14" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $apiDatas['rain_year_in']),
                "_UNIT" => $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $apiDatas['rain_year_in'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "15" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_extra_1']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_extra_1'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "16" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_extra_2']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_extra_2'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "17" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_extra_3']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_extra_3'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "18" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_extra_4']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_extra_4'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "19" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_extra_5']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_extra_5'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "20" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_extra_6']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_extra_6'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "21" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_extra_7']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_extra_7'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "24" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_leaf_1']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_leaf_1'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "25" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_leaf_2']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_leaf_2'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "26" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_soil_1']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_soil_1'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "27" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_soil_2']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_soil_2'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "28" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_soil_3']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_soil_3'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "29" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_soil_4']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_soil_4'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "30" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_1'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_1'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "31" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_2'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_2'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "32" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_3'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_3'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "33" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_4'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_4'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "34" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_5'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_5'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "35" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_6'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_6'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "36" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_7'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_7'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "37" => array(
                "_VALUE_MAIN" => $apiDatas['leaf_wetness_1'],
                "_UNIT" =>  '',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colLeaf($switch, $apiDatas['leaf_wetness_1'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "38" => array(
                "_VALUE_MAIN" => $apiDatas['leaf_wetness_2'],
                "_UNIT" =>  '',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colLeaf($switch, $apiDatas['leaf_wetness_2'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "39" => array(
                "_VALUE_MAIN" => $apiDatas['soil_moisture_1'],
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $apiDatas['soil_moisture_1'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "40" => array(
                "_VALUE_MAIN" => $apiDatas['soil_moisture_2'],
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $apiDatas['soil_moisture_2'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "41" => array(
                "_VALUE_MAIN" => $apiDatas['soil_moisture_3'],
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $apiDatas['soil_moisture_3'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "42" => array(
                "_VALUE_MAIN" => $apiDatas['soil_moisture_4'],
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $apiDatas['soil_moisture_4'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "43" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_in_f']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_in_f'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "44" => array(
                "_VALUE_MAIN" => $apiDatas['relative_humidity_in'],
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $apiDatas['relative_humidity_in'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "46" => array(
                "_VALUE_MAIN" => '<a data-toggle="tooltip" title="' . $this->l->degToCompass($apiDatas['wind_degrees'], $this->l->getLg()) . '"><i class="boussole wi wi-wind from-' . $apiDatas['wind_degrees'] . '-deg"></i></a>',
                "_UNIT" =>  '',
                "_CLASS_UNIT_SMALL" => '',
                "_CLASS_UNIT_LARGE" => '',
                "color" => $this->col->colWindDir($switch, $apiDatas['wind_degrees'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "47" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_leaf_3']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_leaf_3'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "48" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $apiDatas['temp_leaf_4']),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $apiDatas['temp_leaf_4'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "49" => array(
                "_VALUE_MAIN" => $apiDatas['leaf_wetness_3'],
                "_UNIT" =>  '',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colLeaf($switch, $apiDatas['leaf_wetness_3'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "50" => array(
                "_VALUE_MAIN" => $apiDatas['leaf_wetness_4'],
                "_UNIT" =>  '',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colLeaf($switch, $apiDatas['leaf_wetness_4'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
        );

        return $inc;
    }

    /**
     * MILIEU CASE POUR HOME version 2
     */
    public function incMid2($datas, $switch, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);
        $apiDatasUP = $this->getAPIDatasUp($datas, $info, $livestation, $livenbr, $livetab);

        $inc = array(
            "10" => array(
                "_VALUE_MAIN" => $this->getPress($switch, $apiDatas['pressure_in']),
                "TEXT_TOOLTIP_S" => $this->l->trad('PRESSURE'),
                "TEXT_TOOLTIP_M" => $this->l->trad('PRESSURE'),
                "TEXT_TOOLTIP_L" => $this->l->pressTrad($apiDatasUP['pressure_tendency'], $this->l->getLg()),
                "_UNIT_S" => '',
                "_UNIT_M" =>  $this->getUnit($switch, 'press'),
                "_UNIT_L" =>  $this->getUnit($switch, 'press'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_MIDDLE" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "TXT_ALTERN" => '&nbsp;<img class="arrowpress2" alt="arrow" src="' . $this->pressImg($apiDatasUP['pressure_tendency'])  . '" />',
                "color" => $this->col->colPress($switch, $apiDatasUP['mb_pressure'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
        );

        return $inc;
    }

    /**
     * MILIEU CASE POUR HOME version 3
     */
    public function incMid3($datas, $switch, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);

        $inc = array(
            "22" => array(
                "_VALUE_MAIN" => $apiDatas['solar_radiation'],
                "TEXT_TOOLTIP_S" => $this->l->trad('MAX') . ' : ' . $apiDatas['solar_radiation_day_high'] . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['solar_radiation_day_high_time'], $this->l->getLg()),
                "TEXT_TOOLTIP_L" => $this->l->trad('MAX') . ' : ' . $apiDatas['solar_radiation_day_high'] . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['solar_radiation_day_high_time'], $this->l->getLg()),
                "_UNIT_S" => 'W/m²',
                "_UNIT_L" =>  '&nbsp;W/m²',
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colSun($switch, $apiDatas['solar_radiation'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
            "23" => array(
                "_VALUE_MAIN" => $apiDatas['uv_index'],
                "TEXT_TOOLTIP_S" => $this->l->trad('MAX') . ' : ' . $apiDatas['uv_index_day_high'] . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['uv_index_day_high_time'], $this->l->getLg()),
                "TEXT_TOOLTIP_L" => $this->l->trad('MAX') . ' : ' . $apiDatas['uv_index_day_high'] . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['uv_index_day_high_time'], $this->l->getLg()),
                "_UNIT_S" => '',
                "_UNIT_L" =>  '&nbsp;/16',
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colUV($switch, $apiDatas['uv_index'], $datas, $info, $livestation, $livenbr, $livetab)
            ),
        );

        return $inc;
    }

    /**
     * BAS CASE POUR HOME version 1
     */
    public function incDown1($datas, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);

        $inc = array(
            "0" => array(
                "CSS_DOWN" => '',
                "_VALUE_DOWN_S" =>  '',
                "_VALUE_DOWN_L" => ''
            ),
            "1" => array(
                "CSS_DOWN" => '500',
                "_VALUE_DOWN_S" =>  $this->l->degToCompassSmall($apiDatas['wind_degrees'], $this->l->getLg()),
                "_VALUE_DOWN_L" => $this->l->degToCompass($apiDatas['wind_degrees'], $this->l->getLg())
            ),
            "4" => array(
                "CSS_DOWN" => '500',
                "_VALUE_DOWN_S" =>  $this->l->trad('ON_10_MN'),
                "_VALUE_DOWN_L" => $this->l->trad('LAST_10_MN')
            ),
            "6" => array(
                "CSS_DOWN" => '800',
                "_VALUE_DOWN_S" =>  $this->l->trad('ACCRUED'),
                "_VALUE_DOWN_L" => $this->l->trad('ACCUMULATED')
            ),
            "7" => array(
                "CSS_DOWN" => '800',
                "_VALUE_DOWN_S" => $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['wind_day_high_time'], $this->l->getLg()),
                "_VALUE_DOWN_L" => $this->l->trad('TODAY') . ' ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['wind_day_high_time'], $this->l->getLg())
            ),
            "13" => array(
                "CSS_DOWN" => '800',
                "_VALUE_DOWN_S" => $this->l->trad('ACCRUED'),
                "_VALUE_DOWN_L" => $this->l->trad('ACCUMULATED')
            ),
            "14" => array(
                "CSS_DOWN" => '800',
                "_VALUE_DOWN_S" => $this->l->trad('ACCRUED'),
                "_VALUE_DOWN_L" => $this->l->trad('ACCUMULATED')
            ),
            "46" => array(
                "CSS_DOWN" => '800',
                "_VALUE_DOWN_S" => $apiDatas['wind_degrees'] . '°',
                "_VALUE_DOWN_L" => $apiDatas['wind_degrees'] . '°'
            ),
        );

        return $inc;
    }



    /**
     * BAS CASE POUR HOME version 2
     */

    public function incDown2($datas, $switch, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);


        $temp_soil_1_day_low = $apiDatas['temp_soil_1_day_low'];
        $temp_soil_1_month_low = $apiDatas['temp_soil_1_month_low'];
        $temp_soil_1_year_low = $apiDatas['temp_soil_1_year_low'];
        $temp_soil_1_day_high = $apiDatas['temp_soil_1_day_high'];
        $temp_soil_1_month_high = $apiDatas['temp_soil_1_month_high'];
        $temp_soil_1_year_high = $apiDatas['temp_soil_1_year_high'];

        $temp_soil_1_day_low_time = $apiDatas['temp_soil_1_day_low_time'];
        $temp_soil_1_day_high_time = $apiDatas['temp_soil_1_day_high_time'];

        $temp_soil_2_day_low = $apiDatas['temp_soil_2_day_low'];
        $temp_soil_2_month_low = $apiDatas['temp_soil_2_month_low'];
        $temp_soil_2_year_low = $apiDatas['temp_soil_2_year_low'];
        $temp_soil_2_day_high = $apiDatas['temp_soil_2_day_high'];
        $temp_soil_2_month_high = $apiDatas['temp_soil_2_month_high'];
        $temp_soil_2_year_high = $apiDatas['temp_soil_2_year_high'];

        $temp_soil_2_day_low_time = $apiDatas['temp_soil_2_day_low_time'];
        $temp_soil_2_day_high_time = $apiDatas['temp_soil_2_day_high_time'];

        $temp_soil_3_day_low = $apiDatas['temp_soil_3_day_low'];
        $temp_soil_3_month_low = $apiDatas['temp_soil_3_month_low'];
        $temp_soil_3_year_low = $apiDatas['temp_soil_3_year_low'];
        $temp_soil_3_day_high = $apiDatas['temp_soil_3_day_high'];
        $temp_soil_3_month_high = $apiDatas['temp_soil_3_month_high'];
        $temp_soil_3_year_high = $apiDatas['temp_soil_3_year_high'];

        $temp_soil_3_day_low_time = $apiDatas['temp_soil_3_day_low_time'];
        $temp_soil_3_day_high_time = $apiDatas['temp_soil_3_day_high_time'];

        $temp_soil_4_day_low = $apiDatas['temp_soil_4_day_low'];
        $temp_soil_4_month_low = $apiDatas['temp_soil_4_month_low'];
        $temp_soil_4_year_low = $apiDatas['temp_soil_4_year_low'];
        $temp_soil_4_day_high = $apiDatas['temp_soil_4_day_high'];
        $temp_soil_4_month_high = $apiDatas['temp_soil_4_month_high'];
        $temp_soil_4_year_high = $apiDatas['temp_soil_4_year_high'];

        $temp_soil_4_day_low_time = $apiDatas['temp_soil_4_day_low_time'];
        $temp_soil_4_day_high_time = $apiDatas['temp_soil_4_day_high_time'];


        $relative_humidity_1_day_low = $apiDatas['relative_humidity_1_day_low'];
        $relative_humidity_1_month_low = $apiDatas['relative_humidity_1_month_low'];
        $relative_humidity_1_year_low = $apiDatas['relative_humidity_1_year_low'];
        $relative_humidity_1_day_high = $apiDatas['relative_humidity_1_day_high'];
        $relative_humidity_1_month_high = $apiDatas['relative_humidity_1_month_high'];
        $relative_humidity_1_year_high = $apiDatas['relative_humidity_1_year_high'];

        $relative_humidity_1_day_low_time = $apiDatas['relative_humidity_1_day_low_time'];
        $relative_humidity_1_day_high_time = $apiDatas['relative_humidity_1_day_high_time'];

        $relative_humidity_2_day_low = $apiDatas['relative_humidity_2_day_low'];
        $relative_humidity_2_month_low = $apiDatas['relative_humidity_2_month_low'];
        $relative_humidity_2_year_low = $apiDatas['relative_humidity_2_year_low'];
        $relative_humidity_2_day_high = $apiDatas['relative_humidity_2_day_high'];
        $relative_humidity_2_month_high = $apiDatas['relative_humidity_2_month_high'];
        $relative_humidity_2_year_high = $apiDatas['relative_humidity_2_year_high'];

        $relative_humidity_2_day_low_time = $apiDatas['relative_humidity_2_day_low_time'];
        $relative_humidity_2_day_high_time = $apiDatas['relative_humidity_2_day_high_time'];

        $relative_humidity_3_day_low = $apiDatas['relative_humidity_3_day_low'];
        $relative_humidity_3_month_low = $apiDatas['relative_humidity_3_month_low'];
        $relative_humidity_3_year_low = $apiDatas['relative_humidity_3_year_low'];
        $relative_humidity_3_day_high = $apiDatas['relative_humidity_3_day_high'];
        $relative_humidity_3_month_high = $apiDatas['relative_humidity_3_month_high'];
        $relative_humidity_3_year_high = $apiDatas['relative_humidity_3_year_high'];

        $relative_humidity_3_day_low_time = $apiDatas['relative_humidity_3_day_low_time'];
        $relative_humidity_3_day_high_time = $apiDatas['relative_humidity_3_day_high_time'];

        $relative_humidity_4_day_low = $apiDatas['relative_humidity_4_day_low'];
        $relative_humidity_4_month_low = $apiDatas['relative_humidity_4_month_low'];
        $relative_humidity_4_year_low = $apiDatas['relative_humidity_4_year_low'];
        $relative_humidity_4_day_high = $apiDatas['relative_humidity_4_day_high'];
        $relative_humidity_4_month_high = $apiDatas['relative_humidity_4_month_high'];
        $relative_humidity_4_year_high = $apiDatas['relative_humidity_4_year_high'];

        $relative_humidity_4_day_low_time = $apiDatas['relative_humidity_4_day_low_time'];
        $relative_humidity_4_day_high_time = $apiDatas['relative_humidity_4_day_high_time'];

        $relative_humidity_5_day_low = $apiDatas['relative_humidity_5_day_low'];
        $relative_humidity_5_month_low = $apiDatas['relative_humidity_5_month_low'];
        $relative_humidity_5_year_low = $apiDatas['relative_humidity_5_year_low'];
        $relative_humidity_5_day_high = $apiDatas['relative_humidity_5_day_high'];
        $relative_humidity_5_month_high = $apiDatas['relative_humidity_5_month_high'];
        $relative_humidity_5_year_high = $apiDatas['relative_humidity_5_year_high'];

        $relative_humidity_5_day_low_time = $apiDatas['relative_humidity_5_day_low_time'];
        $relative_humidity_5_day_high_time = $apiDatas['relative_humidity_5_day_high_time'];

        $relative_humidity_6_day_low = $apiDatas['relative_humidity_6_day_low'];
        $relative_humidity_6_month_low = $apiDatas['relative_humidity_6_month_low'];
        $relative_humidity_6_year_low = $apiDatas['relative_humidity_6_year_low'];
        $relative_humidity_6_day_high = $apiDatas['relative_humidity_6_day_high'];
        $relative_humidity_6_month_high = $apiDatas['relative_humidity_6_month_high'];
        $relative_humidity_6_year_high = $apiDatas['relative_humidity_6_year_high'];

        $relative_humidity_6_day_low_time = $apiDatas['relative_humidity_6_day_low_time'];
        $relative_humidity_6_day_high_time = $apiDatas['relative_humidity_6_day_high_time'];

        $relative_humidity_7_day_low = $apiDatas['relative_humidity_7_day_low'];
        $relative_humidity_7_month_low = $apiDatas['relative_humidity_7_month_low'];
        $relative_humidity_7_year_low = $apiDatas['relative_humidity_7_year_low'];
        $relative_humidity_7_day_high = $apiDatas['relative_humidity_7_day_high'];
        $relative_humidity_7_month_high = $apiDatas['relative_humidity_7_month_high'];
        $relative_humidity_7_year_high = $apiDatas['relative_humidity_7_year_high'];

        $relative_humidity_7_day_low_time = $apiDatas['relative_humidity_7_day_low_time'];
        $relative_humidity_7_day_high_time = $apiDatas['relative_humidity_7_day_high_time'];

        $soil_moisture_1_day_low = $apiDatas['soil_moisture_1_day_low'];
        $soil_moisture_1_month_low = $apiDatas['soil_moisture_1_month_low'];
        $soil_moisture_1_year_low = $apiDatas['soil_moisture_1_year_low'];
        $soil_moisture_1_day_high = $apiDatas['soil_moisture_1_day_high'];
        $soil_moisture_1_month_high = $apiDatas['soil_moisture_1_month_high'];
        $soil_moisture_1_year_high = $apiDatas['soil_moisture_1_year_high'];

        $soil_moisture_1_day_low_time = $apiDatas['soil_moisture_1_day_low_time'];
        $soil_moisture_1_day_high_time = $apiDatas['soil_moisture_1_day_high_time'];

        $soil_moisture_2_day_low = $apiDatas['soil_moisture_2_day_low'];
        $soil_moisture_2_month_low = $apiDatas['soil_moisture_2_month_low'];
        $soil_moisture_2_year_low = $apiDatas['soil_moisture_2_year_low'];
        $soil_moisture_2_day_high = $apiDatas['soil_moisture_2_day_high'];
        $soil_moisture_2_month_high = $apiDatas['soil_moisture_2_month_high'];
        $soil_moisture_2_year_high = $apiDatas['soil_moisture_2_year_high'];

        $soil_moisture_2_day_low_time = $apiDatas['soil_moisture_2_day_low_time'];
        $soil_moisture_2_day_high_time = $apiDatas['soil_moisture_2_day_high_time'];

        $soil_moisture_3_day_low = $apiDatas['soil_moisture_3_day_low'];
        $soil_moisture_3_month_low = $apiDatas['soil_moisture_3_month_low'];
        $soil_moisture_3_year_low = $apiDatas['soil_moisture_3_year_low'];
        $soil_moisture_3_day_high = $apiDatas['soil_moisture_3_day_high'];
        $soil_moisture_3_month_high = $apiDatas['soil_moisture_3_month_high'];
        $soil_moisture_3_year_high = $apiDatas['soil_moisture_3_year_high'];

        $soil_moisture_3_day_low_time = $apiDatas['soil_moisture_3_day_low_time'];
        $soil_moisture_3_day_high_time = $apiDatas['soil_moisture_3_day_high_time'];

        $soil_moisture_4_day_low = $apiDatas['soil_moisture_4_day_low'];
        $soil_moisture_4_month_low = $apiDatas['soil_moisture_4_month_low'];
        $soil_moisture_4_year_low = $apiDatas['soil_moisture_4_year_low'];
        $soil_moisture_4_day_high = $apiDatas['soil_moisture_4_day_high'];
        $soil_moisture_4_month_high = $apiDatas['soil_moisture_4_month_high'];
        $soil_moisture_4_year_high = $apiDatas['soil_moisture_4_year_high'];

        $soil_moisture_4_day_low_time = $apiDatas['soil_moisture_4_day_low_time'];
        $soil_moisture_4_day_high_time = $apiDatas['soil_moisture_4_day_high_time'];

        $temp_in_day_low_f = $apiDatas['temp_in_day_low_f'];
        $temp_in_month_low_f = $apiDatas['temp_in_month_low_f'];
        $temp_in_year_low_f = $apiDatas['temp_in_year_low_f'];
        $temp_in_day_high_f = $apiDatas['temp_in_day_high_f'];
        $temp_in_month_high_f = $apiDatas['temp_in_month_high_f'];
        $temp_in_year_high_f = $apiDatas['temp_in_year_high_f'];

        $temp_in_day_low_time = $apiDatas['temp_in_day_low_time'];
        $temp_in_day_high_time = $apiDatas['temp_in_day_high_time'];

        $relative_humidity_in_day_low = $apiDatas['relative_humidity_in_day_low'];
        $relative_humidity_in_month_low = $apiDatas['relative_humidity_in_month_low'];
        $relative_humidity_in_year_low = $apiDatas['relative_humidity_in_year_low'];
        $relative_humidity_in_day_high = $apiDatas['relative_humidity_in_day_high'];
        $relative_humidity_in_month_high = $apiDatas['relative_humidity_in_month_high'];
        $relative_humidity_in_year_high = $apiDatas['relative_humidity_in_year_high'];

        $relative_humidity_in_day_low_time = $apiDatas['relative_humidity_in_day_low_time'];
        $relative_humidity_in_day_high_time = $apiDatas['relative_humidity_in_day_high_time'];

        $inc = array(
            "2" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_day_low_f']), $this->getTemp($switch, $apiDatas['temp_month_low_f']), $this->getTemp($switch, $apiDatas['temp_year_low_f'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_day_high_f']), $this->getTemp($switch, $apiDatas['temp_month_high_f']), $this->getTemp($switch, $apiDatas['temp_year_high_f'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "10" => array(
                "CSS_DOWN" => '800',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_PRESSURE'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_PRESSURE'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'press'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getPress($switch, $apiDatas['pressure_day_low_in']), $this->getPress($switch, $apiDatas['pressure_month_low_in']), $this->getPress($switch,  $apiDatas['pressure_year_low_in'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getPress($switch, $apiDatas['pressure_day_high_in']), $this->getPress($switch, $apiDatas['pressure_month_high_in']), $this->getPress($switch, $apiDatas['pressure_year_high_in'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['pressure_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['pressure_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "11" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TDN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_DEW_POINT'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TDX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_DEW_POINT'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['dewpoint_day_low_f']), $this->getTemp($switch, $apiDatas['dewpoint_month_low_f']), $this->getTemp($switch, $apiDatas['dewpoint_year_low_f'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['dewpoint_day_high_f']), $this->getTemp($switch, $apiDatas['dewpoint_month_high_f']), $this->getTemp($switch, $apiDatas['dewpoint_year_high_f'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['dewpoint_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['dewpoint_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "12" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $apiDatas['relative_humidity_day_low'], $apiDatas['relative_humidity_month_low'], $apiDatas['relative_humidity_year_low']),
                "_DMY_VALUE_x" => $this->getDMY($switch, $apiDatas['relative_humidity_day_high'], $apiDatas['relative_humidity_month_high'], $apiDatas['relative_humidity_year_high']),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['relative_humidity_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['relative_humidity_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "15" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_1_day_low']), $this->getTemp($switch, $apiDatas['temp_extra_1_month_low']), $this->getTemp($switch, $apiDatas['temp_extra_1_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_1_day_high']), $this->getTemp($switch, $apiDatas['temp_extra_1_month_high']), $this->getTemp($switch, $apiDatas['temp_extra_1_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_1_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_1_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "16" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_2_day_low']), $this->getTemp($switch, $apiDatas['temp_extra_2_month_low']), $this->getTemp($switch, $apiDatas['temp_extra_2_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_2_day_high']), $this->getTemp($switch,  $apiDatas['temp_extra_2_month_high']), $this->getTemp($switch, $apiDatas['temp_extra_2_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_2_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_2_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "17" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_3_day_low']), $this->getTemp($switch, $apiDatas['temp_extra_3_month_low']), $this->getTemp($switch, $apiDatas['temp_extra_3_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_3_day_high']), $this->getTemp($switch, $apiDatas['temp_extra_3_month_high']), $this->getTemp($switch, $apiDatas['temp_extra_3_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_3_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_3_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "18" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_4_day_low']), $this->getTemp($switch, $apiDatas['temp_extra_4_month_low']), $this->getTemp($switch, $apiDatas['temp_extra_4_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_4_day_high']), $this->getTemp($switch,  $apiDatas['temp_extra_4_month_high']), $this->getTemp($switch, $apiDatas['temp_extra_4_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_4_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_4_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "19" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_5_day_low']), $this->getTemp($switch, $apiDatas['temp_extra_5_month_low']), $this->getTemp($switch, $apiDatas['temp_extra_5_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch,  $apiDatas['temp_extra_5_day_high']), $this->getTemp($switch, $apiDatas['temp_extra_5_month_high']), $this->getTemp($switch, $apiDatas['temp_extra_5_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_5_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_5_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "20" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_6_day_low']), $this->getTemp($switch, $apiDatas['temp_extra_6_month_low']), $this->getTemp($switch, $apiDatas['temp_extra_6_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_6_day_high']), $this->getTemp($switch, $apiDatas['temp_extra_6_month_high']), $this->getTemp($switch, $apiDatas['temp_extra_6_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_6_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_6_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "21" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_7_day_low']), $this->getTemp($switch, $apiDatas['temp_extra_7_month_low']), $this->getTemp($switch, $apiDatas['temp_extra_7_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_extra_7_day_high']), $this->getTemp($switch, $apiDatas['temp_extra_7_month_high']), $this->getTemp($switch, $apiDatas['temp_extra_7_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_7_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_extra_7_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "24" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_1_day_low']), $this->getTemp($switch, $apiDatas['temp_leaf_1_month_low']), $this->getTemp($switch, $apiDatas['temp_leaf_1_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_1_day_high']), $this->getTemp($switch,  $apiDatas['temp_leaf_1_month_high']), $this->getTemp($switch, $apiDatas['temp_leaf_1_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_1_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_1_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "25" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_2_day_low']), $this->getTemp($switch, $apiDatas['temp_leaf_2_month_low']), $this->getTemp($switch, $apiDatas['temp_leaf_2_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_2_day_high']), $this->getTemp($switch,  $apiDatas['temp_leaf_2_month_high']), $this->getTemp($switch, $apiDatas['temp_leaf_2_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_2_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_2_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "26" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_1_day_low), $this->getTemp($switch, $temp_soil_1_month_low), $this->getTemp($switch, $temp_soil_1_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_1_day_high), $this->getTemp($switch, $temp_soil_1_month_high), $this->getTemp($switch, $temp_soil_1_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_1_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_1_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "27" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_2_day_low), $this->getTemp($switch, $temp_soil_2_month_low), $this->getTemp($switch, $temp_soil_2_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_2_day_high), $this->getTemp($switch, $temp_soil_2_month_high), $this->getTemp($switch, $temp_soil_2_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_2_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_2_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "28" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_3_day_low), $this->getTemp($switch, $temp_soil_3_month_low), $this->getTemp($switch, $temp_soil_3_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_3_day_high), $this->getTemp($switch, $temp_soil_3_month_high), $this->getTemp($switch, $temp_soil_3_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_3_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_3_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "29" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_4_day_low), $this->getTemp($switch, $temp_soil_4_month_low), $this->getTemp($switch, $temp_soil_4_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_soil_4_day_high), $this->getTemp($switch, $temp_soil_4_month_high), $this->getTemp($switch, $temp_soil_4_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_4_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_soil_4_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "30" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_1_day_low, $relative_humidity_1_month_low, $relative_humidity_1_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_1_day_high, $relative_humidity_1_month_high, $relative_humidity_1_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_1_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_1_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "31" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_2_day_low, $relative_humidity_2_month_low, $relative_humidity_2_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_2_day_high, $relative_humidity_2_month_high, $relative_humidity_2_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_2_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_2_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "32" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_3_day_low, $relative_humidity_3_month_low, $relative_humidity_3_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_3_day_high, $relative_humidity_3_month_high, $relative_humidity_3_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_3_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_3_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "33" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_4_day_low, $relative_humidity_4_month_low, $relative_humidity_4_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_4_day_high, $relative_humidity_4_month_high, $relative_humidity_4_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_4_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_4_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "34" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_5_day_low, $relative_humidity_5_month_low, $relative_humidity_5_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_5_day_high, $relative_humidity_5_month_high, $relative_humidity_5_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_5_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_5_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "35" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_6_day_low, $relative_humidity_6_month_low, $relative_humidity_6_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_6_day_high, $relative_humidity_6_month_high, $relative_humidity_6_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_6_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_6_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "36" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_7_day_low, $relative_humidity_7_month_low, $relative_humidity_7_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_7_day_high, $relative_humidity_7_month_high, $relative_humidity_7_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_7_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_7_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "37" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MIN_INDEX'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAX_INDEX'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '',
                "_UNIT_DOWN_LARGE" => '',
                "_DMY_VALUE_n" => $this->getDMY($switch, $apiDatas['leaf_wetness_1_day_low'], $apiDatas['leaf_wetness_1_month_low'], $apiDatas['leaf_wetness_1_year_low']),
                "_DMY_VALUE_x" => $this->getDMY($switch, $apiDatas['leaf_wetness_1_day_high'], $apiDatas['leaf_wetness_1_month_high'], $apiDatas['leaf_wetness_1_year_high']),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_1_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_1_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "38" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MIN_INDEX'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAX_INDEX'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '',
                "_UNIT_DOWN_LARGE" => '',
                "_DMY_VALUE_n" => $this->getDMY($switch, $apiDatas['leaf_wetness_2_day_low'], $apiDatas['leaf_wetness_2_month_low'], $apiDatas['leaf_wetness_2_year_low']),
                "_DMY_VALUE_x" => $this->getDMY($switch, $apiDatas['leaf_wetness_2_day_high'], $apiDatas['leaf_wetness_2_month_high'], $apiDatas['leaf_wetness_2_year_high']),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_2_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_2_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "39" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => 'cB',
                "_UNIT_DOWN_LARGE" => 'cB',
                "_DMY_VALUE_n" => $this->getDMY($switch, $soil_moisture_1_day_low, $soil_moisture_1_month_low, $soil_moisture_1_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $soil_moisture_1_day_high, $soil_moisture_1_month_high, $soil_moisture_1_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_1_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_1_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "40" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => 'cB',
                "_UNIT_DOWN_LARGE" => 'cB',
                "_DMY_VALUE_n" => $this->getDMY($switch, $soil_moisture_2_day_low, $soil_moisture_2_month_low, $soil_moisture_2_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $soil_moisture_2_day_high, $soil_moisture_2_month_high, $soil_moisture_2_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_2_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_2_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "41" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => 'cB',
                "_UNIT_DOWN_LARGE" => 'cB',
                "_DMY_VALUE_n" => $this->getDMY($switch, $soil_moisture_3_day_low, $soil_moisture_3_month_low, $soil_moisture_3_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $soil_moisture_3_day_high, $soil_moisture_3_month_high, $soil_moisture_3_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_3_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_3_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "42" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => 'cB',
                "_UNIT_DOWN_LARGE" => 'cB',
                "_DMY_VALUE_n" => $this->getDMY($switch, $soil_moisture_4_day_low, $soil_moisture_4_month_low, $soil_moisture_4_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $soil_moisture_4_day_high, $soil_moisture_4_month_high, $soil_moisture_4_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_4_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($soil_moisture_4_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "43" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_in_day_low_f), $this->getTemp($switch, $temp_in_month_low_f), $this->getTemp($switch, $temp_in_year_low_f)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_in_day_high_f), $this->getTemp($switch, $temp_in_month_high_f), $this->getTemp($switch, $temp_in_year_high_f)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_in_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_in_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "44" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_HUMIDITY'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_HUMIDITY'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '%',
                "_UNIT_DOWN_LARGE" => '%',
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_in_day_low, $relative_humidity_in_month_low, $relative_humidity_in_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_in_day_high, $relative_humidity_in_month_high, $relative_humidity_in_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_in_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_in_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "47" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_3_day_low']), $this->getTemp($switch, $apiDatas['temp_leaf_3_month_low']), $this->getTemp($switch, $apiDatas['temp_leaf_3_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_3_day_high']), $this->getTemp($switch,  $apiDatas['temp_leaf_3_month_high']), $this->getTemp($switch, $apiDatas['temp_leaf_3_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_3_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_3_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "48" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('TN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MINIMUM_TEMP'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('TX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAXIMUM_TEMP'),
                "CLASS_UNIT_DOWN_SMALL" => '09',
                "CLASS_UNIT_DOWN_LARGE" => '09',
                "_UNIT_DOWN_SMALL" => '°',
                "_UNIT_DOWN_LARGE" => $this->getUnit($switch, 'temp'),
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_4_day_low']), $this->getTemp($switch, $apiDatas['temp_leaf_4_month_low']), $this->getTemp($switch, $apiDatas['temp_leaf_4_year_low'])),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $apiDatas['temp_leaf_4_day_high']), $this->getTemp($switch,  $apiDatas['temp_leaf_4_month_high']), $this->getTemp($switch, $apiDatas['temp_leaf_4_year_high'])),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_4_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['temp_leaf_4_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "49" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MIN_INDEX'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAX_INDEX'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '',
                "_UNIT_DOWN_LARGE" => '',
                "_DMY_VALUE_n" => $this->getDMY($switch, $apiDatas['leaf_wetness_3_day_low'], $apiDatas['leaf_wetness_3_month_low'], $apiDatas['leaf_wetness_3_year_low']),
                "_DMY_VALUE_x" => $this->getDMY($switch, $apiDatas['leaf_wetness_3_day_high'], $apiDatas['leaf_wetness_3_month_high'], $apiDatas['leaf_wetness_3_year_high']),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_3_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_3_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
            "50" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MIN'),
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MIN_INDEX'),
                "TEXT_DOWN_SMALL_x" => $this->l->trad('MAX'),
                "TEXT_DOWN_LARGE_x" => $this->l->trad('MAX_INDEX'),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => '',
                "_UNIT_DOWN_LARGE" => '',
                "_DMY_VALUE_n" => $this->getDMY($switch, $apiDatas['leaf_wetness_4_day_low'], $apiDatas['leaf_wetness_4_month_low'], $apiDatas['leaf_wetness_4_year_low']),
                "_DMY_VALUE_x" => $this->getDMY($switch, $apiDatas['leaf_wetness_4_day_high'], $apiDatas['leaf_wetness_4_month_high'], $apiDatas['leaf_wetness_4_year_high']),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_4_day_low_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['leaf_wetness_4_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_TXT_TOOLTIP" => $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY'))
            ),
        );

        return $inc;
    }

    /**
     * BAS CASE POUR HOME version 3
     */
    public function incDown3($datas, $switch, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);

        $wind_month_high_mph = $apiDatas['wind_month_high_mph'];
        $wind_year_high_mph = $apiDatas['wind_year_high_mph'];

        $inc = array(
            "4" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL" => $this->getDMY($switch, $this->l->trad('TODAY'), $this->l->trad('MAX_GUST'), $this->l->trad('MAX_GUST')),
                "TEXT_DOWN_LARGE" => $this->getDMY($switch, $this->l->trad('TODAY'), $this->l->trad('MAX_GUST'), $this->l->trad('MAX_GUST')),
                "DMY_OF_DOWN" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($apiDatas['wind_day_high_time'], $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "ALTERN_TXT_S_1" => $this->l->trad('MAX') . ' : ',
                "ALTERN_TXT_S_2" => '',
                "ALTERN_TXT_S_3" => '',
                "ALTERN_TXT_L_1" => $this->l->trad('MAX_GUST') . ' : ',
                "ALTERN_TXT_L_2" => '',
                "ALTERN_TXT_L_3" => '',
                "_DMY_VALUE" => $this->getDMY($switch, $this->getWind($switch, $apiDatas['wind_day_high_mph']), $this->getWind($switch, $wind_month_high_mph), $this->getWind($switch, $wind_year_high_mph)),
                "CLASS_UNIT_DOWN_SMALL" => '08',
                "CLASS_UNIT_DOWN_LARGE" => '08',
                "_UNIT_DOWN_SMALL" => $this->getUnit($switch, 'wind'),
                "_UNIT_DOWN_LARGE" => ' ' . $this->getUnit($switch, 'wind')
            )
        );

        return $inc;
    }

    /**
     * BAS CASE POUR HOME version 5
     */
    public function incDown5($datas, $switch, $config, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);

        $et_month = $apiDatas['et_month'];

        $et_year = $apiDatas['et_year'];

        $solar_radiation_month_high = $apiDatas['solar_radiation_month_high'];
        $solar_radiation_year_high = $apiDatas['solar_radiation_year_high'];

        $uv_index_month_high = $apiDatas['uv_index_month_high'];
        $uv_index_year_high = $apiDatas['uv_index_year_high'];

        $inc = array(
            "6" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MONTH_PRECIP'),
                "ALTERN_TXT_S_1n" => '',
                "_VALUE_n" =>  $this->getRain($switch, $apiDatas['rain_month_in']),
                "ALTERN_TXT_S_2n" => '',
                "CLASS_UNIT_DOWN_SMALLn" => '',
                "_UNIT_DOWN_SMALLn" => '',
                "ALTERN_TXT_S_3n" => '',
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MONTHLY_PRECIPITATION'),
                "ALTERN_TXT_L_1n" => '',
                "ALTERN_TXT_L_2n" => '',
                "CLASS_UNIT_DOWN_LARGEn" => '08',
                "_UNIT_DOWN_LARGEn" =>  $this->getUnit($switch, 'rain'),
                "ALTERN_TXT_L_3n" => '',
                "TEXT_DOWN_SMALL_x" => $this->l->trad('YEAR_PRECIP'),
                "ALTERN_TXT_S_1x" => '',
                "_VALUE_x" =>  $this->getRain($switch, $apiDatas['rain_year_in']),
                "ALTERN_TXT_S_2x" => '',
                "CLASS_UNIT_DOWN_SMALLx" => '',
                "_UNIT_DOWN_SMALLx" => '',
                "ALTERN_TXT_S_3x" => '',
                "TEXT_DOWN_LARGE_x" => $this->l->trad('ANNUAL_PRECIPITATION'),
                "ALTERN_TXT_L_1x" => '',
                "ALTERN_TXT_L_2x" => '',
                "CLASS_UNIT_DOWN_LARGEx" => '08',
                "_UNIT_DOWN_LARGEx" => $this->getUnit($switch, 'rain'),
                "ALTERN_TXT_L_3x" => '',
            ),
            "9" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MONTH_EVAPO'),
                "ALTERN_TXT_S_1n" => '',
                "_VALUE_n" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->getRain($switch, $et_month) : $this->getRain($switch, $this->ETR_in($apiDatas['temp_month_high_f'], $apiDatas['temp_month_low_f'], $apiDatas['rain_month_in'])),
                "ALTERN_TXT_S_2n" => '',
                "CLASS_UNIT_DOWN_SMALLn" => '',
                "_UNIT_DOWN_SMALLn" => '',
                "ALTERN_TXT_S_3n" => '',
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MONTHLY_EVAPO'),
                "ALTERN_TXT_L_1n" => '',
                "ALTERN_TXT_L_2n" => '',
                "CLASS_UNIT_DOWN_LARGEn" => '08',
                "_UNIT_DOWN_LARGEn" =>  $this->getUnit($switch, 'rain'),
                "ALTERN_TXT_L_3n" => '',
                "TEXT_DOWN_SMALL_x" => $this->l->trad('YEAR_EVAPO'),
                "ALTERN_TXT_S_1x" => '',
                "_VALUE_x" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->getRain($switch, $et_year) : $this->getRain($switch, $this->ETR_in($apiDatas['temp_year_high_f'], $apiDatas['temp_year_low_f'], $apiDatas['rain_year_in'])),
                "ALTERN_TXT_S_2x" => '',
                "CLASS_UNIT_DOWN_SMALLx" => '',
                "_UNIT_DOWN_SMALLx" => '',
                "ALTERN_TXT_S_3x" => '',
                "TEXT_DOWN_LARGE_x" => $this->l->trad('YEARLY_EVAPO'),
                "ALTERN_TXT_L_1x" => '',
                "ALTERN_TXT_L_2x" => '',
                "CLASS_UNIT_DOWN_LARGEx" => '08',
                "_UNIT_DOWN_LARGEx" => $this->getUnit($switch, 'rain'),
                "ALTERN_TXT_L_3x" => '',
            ),
            "22" => array(
                "CSS_DOWN" => '800',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MONTH_SUN'),
                "ALTERN_TXT_S_1n" => '',
                "_VALUE_n" => $solar_radiation_month_high,
                "ALTERN_TXT_S_2n" => '',
                "CLASS_UNIT_DOWN_SMALLn" => '',
                "_UNIT_DOWN_SMALLn" => '',
                "ALTERN_TXT_S_3n" => '',
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MONTH_MAX_SOLAR_RADIATIONS'),
                "ALTERN_TXT_L_1n" => '',
                "ALTERN_TXT_L_2n" => '',
                "CLASS_UNIT_DOWN_LARGEn" => '08',
                "_UNIT_DOWN_LARGEn" =>  '&nbsp;W/m²',
                "ALTERN_TXT_L_3n" => '',
                "TEXT_DOWN_SMALL_x" => $this->l->trad('YEAR_SUN'),
                "ALTERN_TXT_S_1x" => '',
                "_VALUE_x" => $solar_radiation_year_high,
                "ALTERN_TXT_S_2x" => '',
                "CLASS_UNIT_DOWN_SMALLx" => '',
                "_UNIT_DOWN_SMALLx" => '',
                "ALTERN_TXT_S_3x" => '',
                "TEXT_DOWN_LARGE_x" => $this->l->trad('YEAR_MAX_SOLAR_RADIATIONS'),
                "ALTERN_TXT_L_1x" => '',
                "ALTERN_TXT_L_2x" => '',
                "CLASS_UNIT_DOWN_LARGEx" => '08',
                "_UNIT_DOWN_LARGEx" => '&nbsp;W/m²',
                "ALTERN_TXT_L_3x" => '',
            ),
            "23" => array(
                "CSS_DOWN" => '800',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MONTH_UV'),
                "ALTERN_TXT_S_1n" => '',
                "_VALUE_n" => $uv_index_month_high,
                "ALTERN_TXT_S_2n" => '',
                "CLASS_UNIT_DOWN_SMALLn" => '',
                "_UNIT_DOWN_SMALLn" => '',
                "ALTERN_TXT_S_3n" => '',
                "TEXT_DOWN_LARGE_n" => $this->l->trad('MONTH_MAX_UV_INDEX'),
                "ALTERN_TXT_L_1n" => '',
                "ALTERN_TXT_L_2n" => '',
                "CLASS_UNIT_DOWN_LARGEn" => '08',
                "_UNIT_DOWN_LARGEn" =>  '&nbsp;/16',
                "ALTERN_TXT_L_3n" => '',
                "TEXT_DOWN_SMALL_x" => $this->l->trad('YEAR_UV'),
                "ALTERN_TXT_S_1x" => '',
                "_VALUE_x" => $uv_index_year_high,
                "ALTERN_TXT_S_2x" => '',
                "CLASS_UNIT_DOWN_SMALLx" => '',
                "_UNIT_DOWN_SMALLx" => '',
                "ALTERN_TXT_S_3x" => '',
                "TEXT_DOWN_LARGE_x" => $this->l->trad('YEAR_MAX_UV_INDEX'),
                "ALTERN_TXT_L_1x" => '',
                "ALTERN_TXT_L_2x" => '',
                "CLASS_UNIT_DOWN_LARGEx" => '08',
                "_UNIT_DOWN_LARGEx" => '&nbsp;/16',
                "ALTERN_TXT_L_3x" => '',
            )
        );

        return $inc;
    }

    /**
     * TOOLTIP
     */
    public function getIconTooltip($switch, $icon, $txt, $altern)
    {
        if ($switch['s_icon'] == 'yes') {
            $page = '<a data-toggle="tooltip" title="' . $txt . '">' . $icon . $altern . '</a>';
        } else {
            if ($altern != '') {
                $page = '<a data-toggle="tooltip" title="' . $txt . '">' . $altern . '</a>';
            } else {
                $page = $txt;
            }
        }
        return $page;
    }

    /**
     * ICON
     */
    public function getIcon($switch, $icon)
    {
        if ($switch['s_icon'] == 'yes') {
            $page = $icon . ' ';
        } else {
            $page = '';
        }
        return $page;
    }

    /**
     * Donne les TITRES de CASE dans PREF pour le choix OPTIONS-SELECT
     */
    public function optionValue($config, $tab)
    {
        $tabText = $this->tabTxt($config, $tab);

        $optionValue = array(
            "2" => $tabText['2'],
            "5" => $tabText['5'],
            "8" => $tabText['8'],
            "11" => $tabText['11'],
            "10" => $tabText['10'],
            "12" => $tabText['12'],
            "1" => $tabText['1'],
            "4" => $tabText['4'],
            "7" => $tabText['7'],
            "3" => $tabText['3'],
            "6" => $tabText['6'],
            "9" => $tabText['9'],
            "46" => $tabText['46'],
            "13" => $tabText['13'],
            "14" => $tabText['14'],
            "22" => $tabText['22'],
            "23" => $tabText['23'],
            "15" => $tabText['15'],
            "16" => $tabText['16'],
            "17" => $tabText['17'],
            "18" => $tabText['18'],
            "19" => $tabText['19'],
            "20" => $tabText['20'],
            "21" => $tabText['21'],
            "24" => $tabText['24'],
            "25" => $tabText['25'],
            "47" => $tabText['47'],
            "48" => $tabText['48'],
            "26" => $tabText['26'],
            "27" => $tabText['27'],
            "28" => $tabText['28'],
            "29" => $tabText['29'],
            "30" => $tabText['30'],
            "31" => $tabText['31'],
            "32" => $tabText['32'],
            "33" => $tabText['33'],
            "34" => $tabText['34'],
            "35" => $tabText['35'],
            "36" => $tabText['36'],
            "37" => $tabText['37'],
            "38" => $tabText['38'],
            "49" => $tabText['49'],
            "50" => $tabText['50'],
            "39" => $tabText['39'],
            "40" => $tabText['40'],
            "41" => $tabText['41'],
            "42" => $tabText['42'],
            "43" => $tabText['43'],
            "44" => $tabText['44'],
        );
        return $optionValue;
    }

    /**
     * Calcul Case Bas Heat-Wind
     */
    public function downHeatWind($switch, $datas, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);

        $windchill_day_low_f = $apiDatas['windchill_day_low_f'];
        $windchill_day_low_time = $apiDatas['windchill_day_low_time'];
        $windchill_month_low_f = $apiDatas['windchill_month_low_f'];
        $windchill_year_low_f = $apiDatas['windchill_year_low_f'];

        $heat_index_day_high_f = $apiDatas['heat_index_day_high_f'];
        $heat_index_day_high_time = $apiDatas['heat_index_day_high_time'];
        $heat_index_month_high_f = $apiDatas['heat_index_month_high_f'];
        $heat_index_year_high_f = $apiDatas['heat_index_year_high_f'];

        $page = '';
        if ($this->is_Temp('59', $this->getDMY($switch, $apiDatas['temp_day_high_f'], $apiDatas['temp_month_high_f'], $apiDatas['temp_year_high_f'])) == true) {
            $page .= '<a data-toggle="tooltip" title="' . $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY')) . '">' . $this->l->trad('MIN') . ' :</a> ';
        }
        if ($this->is_Temp('59', $this->getDMY($switch, $apiDatas['temp_day_low_f'], $apiDatas['temp_month_low_f'], $apiDatas['temp_year_low_f'])) == false) {
            $page .= '<a data-toggle="tooltip" title="' . $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY')) . '">' . $this->l->trad('MAX') . ' :</a> ';
        }
        if ($this->is_Temp('59', $this->getDMY($switch, $apiDatas['temp_day_low_f'], $apiDatas['temp_month_low_f'], $apiDatas['temp_year_low_f'])) == true) {
            $page .= '<div class="small500">';
            $page .= '<a data-toggle="tooltip" title="' . $this->l->trad('WINDCHILL_SMALL') . ' ' . $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($windchill_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')) . '">';
            $page .= $this->getDMY($switch, $this->getTemp($switch, $windchill_day_low_f), $this->getTemp($switch, $windchill_month_low_f), $this->getTemp($switch, $windchill_year_low_f));
            $page .= '<span class="unit09">°</span>';
            $page .= '</a>';
            $page .= '</div>';
            $page .= '<div class="large500">';
            $page .= '<a data-toggle="tooltip" title="' . $this->l->trad('WINDCHILL_LARGE') . ' ' . $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($windchill_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')) . '">';
            $page .= $this->getDMY($switch, $this->getTemp($switch, $windchill_day_low_f), $this->getTemp($switch, $windchill_month_low_f), $this->getTemp($switch, $windchill_year_low_f));
            $page .= '<span class="unit09">' . $this->getUnit($switch, 'temp') . '</span>';
            $page .= '</a>';
            $page .= '</div>';
        }
        if ($this->is_Temp('59', $this->getDMY($switch, $apiDatas['temp_day_low_f'], $apiDatas['temp_month_low_f'], $apiDatas['temp_year_low_f'])) == true && $this->is_Temp('59', $this->getDMY($switch, $apiDatas['temp_day_high_f'], $apiDatas['temp_month_high_f'], $apiDatas['temp_year_high_f'])) == false) {
            $page  .= '<div class="small500">';
            $page .= '<div class="dmy_display_on">';
            $page .= '<a data-toggle="tooltip" title="' . $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY')) . '">';
            $page .= ' |' . $this->getDMY($switch, $this->l->trad('D'), $this->l->trad('M'), $this->l->trad('Y')) . '| ';
            $page .= '</a>';
            $page .= '</div>';
            $page .= '</div>';
            $page .= '<div class="dmy_display_off">';
            $page .= ' | ';
            $page .= '</div>';
            $page .= '<div class="large500">';
            $page .= '<div class="dmy_display_on">';
            $page .= '<a data-toggle="tooltip" title="' . $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY')) . '">';
            $page .= ' | -' . $this->getDMY($switch, $this->l->trad('D'), $this->l->trad('M'), $this->l->trad('Y')) . '- | ';
            $page .= '</a>';
            $page .= '</div>';
            $page .= '</div>';
        }
        if ($this->is_Temp('59', $this->getDMY($switch, $apiDatas['temp_day_high_f'], $apiDatas['temp_month_high_f'], $apiDatas['temp_year_high_f'])) == false) {
            $page .= '<div class="small500">';
            $page .= '<a data-toggle="tooltip" title="' . $this->l->trad('HEAT') . ' ' . $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($heat_index_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')) . '">';
            $page .= $this->getDMY($switch, $this->getTemp($switch, $heat_index_day_high_f), $this->getTemp($switch, $heat_index_month_high_f), $this->getTemp($switch, $heat_index_year_high_f));
            $page .= '<span class="unit09">°</span>';
            $page .= '</a>';
            $page .= '</div>';
            $page .= '<div class="large500">';
            $page .= '<a data-toggle="tooltip" title="' . $this->l->trad('HEAT_INDEX') . ' ' . $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($heat_index_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')) . '">';
            $page .= $this->getDMY($switch, $this->getTemp($switch, $heat_index_day_high_f), $this->getTemp($switch, $heat_index_month_high_f), $this->getTemp($switch, $heat_index_year_high_f));
            $page .= '<span class="unit09">' . $this->getUnit($switch, 'temp') . '</span>';
            $page .= '</a>';
            $page .= '</div>';
        }
        return $page;
    }

    /**
     * Calcul Case Bas CLOUD TEXT
     */
    public function incDownCloudy($config, $datas, $info, $livestation, $livenbr, $livetab)
    {
        $zero = '&#8709;';
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);
        $apiDatasUP = $this->getAPIDatasUp($datas, $info, $livestation, $livenbr, $livetab);

        $temp_c = $apiDatasUP['c_temp'];

        $time = $apiDatasUP['time'];
        $sunset = $apiDatasUP['time_sunset'];
        $sunrise = $apiDatasUP['time_sunrise'];

        $longitude = $apiDatas['longitude'];
        $latitude = $apiDatas['latitude'];

        $tmp_date = date_create($time);
        $jour = date_format($tmp_date, "d");
        $mois = date_format($tmp_date, "m");
        $annee = date_format($tmp_date, "Y");

        $utc_date = date_timezone_set($tmp_date, timezone_open('UTC'));
        $heure_utc = date_format($utc_date, "H");
        $minute_utc = date_format($utc_date, "i");


        if ($apiDatas['rain_rate_in_per_hr'] == '0') {
            if ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') {
                if ($this->is_Temp('32', $apiDatas['temp_f']) == true) {
                    if (($apiDatas['relative_humidity']  >= '98') && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $apiDatas['solar_radiation']) == false)) {
                        $page = '<div class="large500">' . $this->l->trad('FREEZING_FOG') . '</div><div class="small500">' . $this->l->trad('FROST_FOG') . '</div>';
                    } elseif ((($apiDatas['relative_humidity']  >= '96') && ($apiDatas['relative_humidity'] < '98')) && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $apiDatas['solar_radiation']) == false)) {
                        $page = '<div class="large500">' . $this->l->trad('FREEZING_MIST') . '</div><div class="small500">' . $this->l->trad('FROST_MIST') . '</div>';
                    } elseif ($apiDatas['relative_humidity']  < '96' && $apiDatas['rain_day_in'] > '0') {
                        $page = $this->l->trad('ICING');
                    } elseif ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                        if ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $apiDatas['solar_radiation']) == true) {
                            $page = $this->l->trad('SUNNY');
                        } else {
                            $page = $this->l->trad('CLOUDY');
                        }
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                } elseif ($this->is_Temp('32', $apiDatas['temp_f']) == false) {
                    if (($apiDatas['relative_humidity']   >= '98') && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $apiDatas['solar_radiation']) == false)) {
                        $page = $this->l->trad('FOG');
                    } elseif ((($apiDatas['relative_humidity']   >= '96') && ($apiDatas['relative_humidity']   < '98')) && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $apiDatas['solar_radiation']) == false)) {
                        $page = $this->l->trad('MIST');
                    } elseif ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                        if ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $apiDatas['solar_radiation']) == true) {
                            $page = $this->l->trad('SUNNY');
                        } else {
                            $page = $this->l->trad('CLOUDY');
                        }
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                }
            } else {
                if ($this->is_Temp('32', $apiDatas['temp_f']) == true) {
                    if ($apiDatas['relative_humidity']   >= '98') {
                        $page = '<div class="large500">' . $this->l->trad('FREEZING_FOG') . '</div><div class="small500">' . $this->l->trad('FROST_FOG') . '</div>';
                    } elseif ($apiDatas['relative_humidity']   >= '96' && $apiDatas['relative_humidity']   < '98') {
                        $page =  '<div class="large500">' . $this->l->trad('FREEZING_MIST') . '</div><div class="small500">' . $this->l->trad('FROST_MIST') . '</div>';
                    } elseif ($apiDatas['relative_humidity']   < '96' && $apiDatas['rain_day_in'] > '0') {
                        $page = $this->l->trad('ICING');
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                } elseif ($this->is_Temp('32', $apiDatas['temp_f']) == false) {
                    if ($apiDatas['relative_humidity']   >= '98') {
                        $page = $this->l->trad('FOG');
                    } elseif (($apiDatas['relative_humidity']   >= '96') && ($apiDatas['relative_humidity']   < '98')) {
                        $page = $this->l->trad('MIST');
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                }
            }
        } elseif ($apiDatas['rain_rate_in_per_hr'] > '0') {
            if ($this->is_Temp('37.4', $apiDatas['temp_f']) == false) {
                $page = $this->l->trad('PRECIPITATION');
            } elseif ($this->is_Temp('32.9', $apiDatas['temp_f']) == false && $this->is_Temp('37.4', $apiDatas['temp_f']) == true) {
                $page = $this->l->trad('RAIN_SNOW');
            } elseif ($this->is_Temp('32.9', $apiDatas['temp_f']) == true) {
                $page = $this->l->trad('SNOW');
            }
        } else {
            $page = $zero;
        }

        return $page;
    }



    /**
     * 
     * 
     * 
     * SUN - UV - MOON 
     * ARRAY Number 8
     * 
     * 
     * 
     * 
     **/



    public function SunTxt($config, $tab)
    {
        if ($config['config_sun'] == 'sun') {
            if ($this->is_tab($tab, '22') == true) {
                $page = $this->l->trad('SUN');
            } else {
                $page = $this->l->trad('SUN') . ' + ' . $this->l->trad('MOON');
            }
        } elseif ($config['config_sun'] == 'uv') {
            if ($this->is_tab($tab, '23') == true) {
                $page = $this->l->trad('UV');
            } else {
                $page = $this->l->trad('UV') . ' + ' . $this->l->trad('MOON');
            }
        } elseif ($config['config_sun'] == 'sun_uv') {
            $page = $this->l->trad('SUN') . ' + ' . $this->l->trad('UV');
        } else {
            $page = $this->l->trad('DAY') . '/' . $this->l->trad('NIGHT');
        }
        return $page;
    }

    public function SunText($config, $tab)
    {
        if ($config['config_sun'] == 'sun') {
            if ($this->is_tab($tab, '22') == true) {
                $page = $this->l->trad('SOLAR_RADIATIONS');
            } else {
                $page = $this->l->trad('SOLAR_RADIATIONS') . ' + ' . $this->l->trad('MOON');
            }
        } elseif ($config['config_sun'] == 'uv') {
            if ($this->is_tab($tab, '23') == true) {
                $page = $this->l->trad('UV_INDEX');
            } else {
                $page = $this->l->trad('UV_INDEX') . ' + ' . $this->l->trad('MOON');
            }
        } elseif ($config['config_sun'] == 'sun_uv') {
            $page = $this->l->trad('SOLAR_RADIATIONS') . ' + ' . $this->l->trad('UV_INDEX');
        } else {
            $page = $this->l->trad('DAY') . ' / ' . $this->l->trad('NIGHT');
        }
        return $page;
    }

    public function incUpSun($switch, $config, $tab, $datas, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatasUP = $this->getAPIDatasUp($datas, $info, $livestation, $livenbr, $livetab);
        $time = $apiDatasUP['time'];
        $sunset = $apiDatasUP['time_sunset'];
        $sunrise = $apiDatasUP['time_sunrise'];

        if ($config['config_sun'] == 'sun') {
            if ($this->is_tab($tab, '22') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $icon = 'wi-day-sunny';
                    $txt_small = $this->l->trad('SUN');
                    $txt_large = $this->l->trad('SOLAR_RADIATIONS');
                } else {
                    $icon = 'wi-night-clear';
                    $txt_small = $this->l->trad('NIGHT');
                    $txt_large = $this->l->trad('NIGHT');
                }
            } else {
                $icon = 'wi-night-clear';
                $txt_small = $this->l->trad('MOON');
                $txt_large = $this->l->trad('MOON');
            }

            $model = '1';
        } elseif ($config['config_sun'] == 'uv') {
            if ($this->is_tab($tab, '23') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $icon = 'wi-hot';
                    $txt_small = $this->l->trad('UV');
                    $txt_large = $this->l->trad('UV_INDEX');
                } else {
                    $icon = 'wi-night-clear';
                    $txt_small = $this->l->trad('NIGHT');
                    $txt_large = $this->l->trad('NIGHT');
                }
            } else {
                $icon = 'wi-night-clear';
                $txt_small = $this->l->trad('MOON');
                $txt_large = $this->l->trad('MOON');
            }

            $model = '1';
        } elseif ($config['config_sun'] == 'sun_uv') {
            if ($this->is_tab($tab, '23') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '2';
                } else {
                    $icon = 'wi-night-clear';
                    $txt_small = $this->l->trad('NIGHT');
                    $txt_large = $this->l->trad('NIGHT');

                    $model = '1';
                }
            } else {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $icon = 'wi-day-sunny';
                    $txt_small = $this->l->trad('DAY');
                    $txt_large = $this->l->trad('DAY');
                } else {
                    $icon = 'wi-night-clear';
                    $txt_small = $this->l->trad('NIGHT');
                    $txt_large = $this->l->trad('NIGHT');
                }
                $model = '1';
            }
        } else {
            if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                $icon = 'wi-day-sunny';
                $txt_small = $this->l->trad('DAY');
                $txt_large = $this->l->trad('DAY');
            } else {
                $icon = 'wi-night-clear';
                $txt_small = $this->l->trad('NIGHT');
                $txt_large = $this->l->trad('NIGHT');
            }
            $model = '1';
        }

        if ($model == '1') {

            $model1 = '<div class="small500">';
            if ($switch['s_icon'] == 'yes') {
                $model1 .= '<a data-toggle="tooltip" title="' . $txt_small . '"><i class="wi ' . $icon . '"></i></a>';
            } else {
                $model1 .= $txt_small;
            }
            $model1 .= '</div>';
            $model1 .= '<div class="small500800">';
            if ($switch['s_icon'] == 'yes') {
                $model1 .= '<i class="wi ' . $icon . '"></i>&nbsp;';
            }
            $model1 .= $txt_small . '</div>';
            $model1 .= '<div class="large800">';
            if ($switch['s_icon'] == 'yes') {
                $model1 .= '<i class="wi ' . $icon . '"></i>&nbsp;';
            }
            $model1 .= $txt_large;
            $model1 .= '</div>';

            return $model1;
        } elseif ($model == '2') {

            $model2 = '<div class="small800 tab_mid_size_13">';
            $model2 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNRISE') . '">';
            $model2 .= $this->l->timeTrad($sunrise, $this->l->getLg());
            $model2 .= '</a>';
            $model2 .= '&nbsp;-&nbsp;';
            $model2 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNSET') . '">';
            $model2 .= $this->l->timeTrad($sunset, $this->l->getLg());
            $model2 .= '</a>';
            $model2 .= '</div>';
            $model2 .= '<div class="large800">';
            $model2 .= $this->l->trad('DAY');
            $model2 .= '&nbsp;:&nbsp;';
            $model2 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNRISE') . '">';
            $model2 .= $this->l->timeTrad($sunrise, $this->l->getLg());
            $model2 .= '</a>';
            $model2 .= '&nbsp;-&nbsp;';
            $model2 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNSET') . '">';
            $model2 .= $this->l->timeTrad($sunset, $this->l->getLg());
            $model2 .= '</a>';
            $model2 .= '</div>';

            return $model2;
        }
    }



    public function incMidSun($switch, $config, $tab, $datas, $info, $livestation, $livenbr, $livetab)
    {
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);
        $apiDatasUP = $this->getAPIDatasUp($datas, $info, $livestation, $livenbr, $livetab);

        $time = $apiDatasUP['time'];
        $sunset = $apiDatasUP['time_sunset'];
        $sunrise = $apiDatasUP['time_sunrise'];

        //MOON PREPARATION
        $tmp_date = strtotime($time);
        $time_moon = mktime(date("H", $tmp_date), date("i", $tmp_date), 0, date("n", $tmp_date), date("j", $tmp_date), date("Y", $tmp_date));
        $this->moon = new Solaris\MoonPhase($time_moon);
        if ($this->l->getLg() == 'en') {
            $phase_n = $this->moon->phase_name_EN();
        } elseif ($this->l->getLg() == 'fr') {
            $phase_n = $this->moon->phase_name_FR();
        }
        $phase = round($this->moon->illumination() * 100, 0);
        $phase_img = $this->moon->img_phase();



        if ($config['config_sun'] == 'sun') {
            if ($this->is_tab($tab, '22') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '1';
                } else {
                    $model = '2';
                }
            } else {
                $model = '2';
            }
        } elseif ($config['config_sun'] == 'uv') {
            if ($this->is_tab($tab, '23') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '3';
                } else {
                    $model = '2';
                }
            } else {
                $model = '2';
            }
        } elseif ($config['config_sun'] == 'sun_uv') {
            if ($this->is_tab($tab, '22') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '1';
                } else {
                    $model = '2';
                }
            } else {
                $model = '2';
            }
        } else {
            $model = '2';
        }


        //SUN
        if ($model == '1') {
            $model1 = '<div class="small500 tab_mid_size_20">';
            $model1 .= '<a data-toggle="tooltip" title="' . $this->l->trad('MAX') . '&nbsp;:&nbsp;' . $apiDatas['solar_radiation_day_high'] . '&nbsp;W/m² &nbsp;' . $this->l->trad('AT') . '&nbsp;' . $this->l->timeTrad($apiDatas['solar_radiation_day_high_time'], $this->l->getLg()) . '">';
            $model1 .= '<span ' . $this->col->colSun($switch, $apiDatas['solar_radiation'], $datas, $info, $livestation, $livenbr, $livetab) . ' >';
            $model1 .=  $apiDatas['solar_radiation'];
            $model1 .= '<span class="unit05">W/m²</span></span>';
            $model1 .= '</a>';
            $model1 .= '</div>';
            $model1 .= '<div class="large500">';
            $model1 .= '<a data-toggle="tooltip" title="' . $this->l->trad('MAX') . '&nbsp;:&nbsp;' . $apiDatas['solar_radiation_day_high'] . '&nbsp;W/m² &nbsp;' . $this->l->trad('AT') . '&nbsp;' . $this->l->timeTrad($apiDatas['solar_radiation_day_high_time'], $this->l->getLg()) . '">';
            $model1 .= '<span ' . $this->col->colSun($switch, $apiDatas['solar_radiation'], $datas, $info, $livestation, $livenbr, $livetab) . ' >';
            $model1 .=  $apiDatas['solar_radiation'];
            $model1 .= '<span class="unit06">&nbsp;W/m²</span></span>';
            $model1 .= '</a>';
            $model1 .= '</div>';
            return $model1;
        }

        //MOON
        elseif ($model == '2') {
            $model2 = '<a data-toggle="tooltip" title="' . $phase_n . ' - ' . $phase . '%">';
            $model2 .= '<img class="moon" alt="moon"  src="images/moon/' . $phase_img . '.png" />';
            $model2 .= '</a>';
            return $model2;
        }

        //UV
        elseif ($model == '3') {
            $model3 = '<div class="small500 tab_mid_size_20">';
            $model3 .= '<span ' . $this->col->colUV($switch, $apiDatas['uv_index'], $datas, $info, $livestation, $livenbr, $livetab) . '>';
            $model3 .= $apiDatas['uv_index'];
            $model3 .= '</span></div>';
            $model3 .= '<div class="large500">';
            $model3 .= '<span ' . $this->col->colUV($switch, $apiDatas['uv_index'], $datas, $info, $livestation, $livenbr, $livetab) . '>';
            $model3 .= $apiDatas['uv_index'];
            $model3 .= '</span></div>';
            return $model3;
        }
    }


    public function incDownSun($config, $tab, $datas, $info, $livestation, $livenbr, $livetab)
    {

        $apiDatasUP = $this->getAPIDatasUp($datas, $info, $livestation, $livenbr, $livetab);
        $apiDatas = $this->getAPIDatas($datas, $info, $livestation, $livenbr, $livetab);

        $time = $apiDatasUP['time'];
        $sunset = $apiDatasUP['time_sunset'];
        $sunrise = $apiDatasUP['time_sunrise'];

        if ($config['config_sun'] == 'sun') {
            if ($this->is_tab($tab, '22') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '1';
                } else {
                    $model = '2';
                }
            } else {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '3';
                } else {
                    $model = '4';
                }
            }
        } elseif ($config['config_sun'] == 'uv') {
            if ($this->is_tab($tab, '23') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '1';
                } else {
                    $model = '2';
                }
            } else {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '3';
                } else {
                    $model = '4';
                }
            }
        } elseif ($config['config_sun'] == 'sun_uv') {
            if ($this->is_tab($tab, '23') == false) {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '5';
                } else {
                    $model = '2';
                }
            } else {
                if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                    $model = '1';
                } else {
                    $model = '2';
                }
            }
        } else {
            if ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                $model = '1';
            } else {
                $model = '2';
            }
        }

        //DAY
        if ($model == '1') {
            $model1 = '<a data-toggle="tooltip" title="' . $this->l->trad('SUNRISE') . '">';
            $model1 .= $this->l->timeTrad($sunrise, $this->l->getLg());
            $model1 .= '</a>';
            $model1 .= '&nbsp;-&nbsp;';
            $model1 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNSET') . '">';
            $model1 .= $this->l->timeTrad($sunset, $this->l->getLg());
            $model1 .= '</a>';
            return $model1;
        }
        //NIGHT
        elseif ($model == '2') {
            $model2 = '<a data-toggle="tooltip" title="' . $this->l->trad('SUNSET') . '">';
            $model2 .= $this->l->timeTrad($sunset, $this->l->getLg());
            $model2 .= '</a>';
            $model2 .= '&nbsp;-&nbsp;';
            $model2 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNRISE') . '">';
            $model2 .= $this->l->timeTrad($sunrise, $this->l->getLg());
            $model2 .= '</a>';
            return $model2;
        }
        //DAY with text
        elseif ($model == '3') {
            $model3 = '<div class="large800">' . $this->l->trad('DAY') . '&nbsp;:&nbsp;</div>';
            $model3 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNRISE') . '">';
            $model3 .= $this->l->timeTrad($sunrise, $this->l->getLg());
            $model3 .= '</a>';
            $model3 .= '&nbsp;-&nbsp;';
            $model3 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNSET') . '">';
            $model3 .= $this->l->timeTrad($sunset, $this->l->getLg());
            $model3 .= '</a>';
            return $model3;
        }
        //Night with text
        elseif ($model == '4') {
            $model4 = '<div class="large800">' . $this->l->trad('NIGHT') . '&nbsp;:&nbsp;</div>';
            $model4 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNSET') . '">';
            $model4 .= $this->l->timeTrad($sunset, $this->l->getLg());
            $model4 .= '</a>';
            $model4 .= '&nbsp;-&nbsp;';
            $model4 .= '<a data-toggle="tooltip" title="' . $this->l->trad('SUNRISE') . '">';
            $model4 .= $this->l->timeTrad($sunrise, $this->l->getLg());
            $model4 .= '</a>';
            return $model4;
        }
        //UV
        elseif ($model == '5') {
            $model5 = 'UV = ' . $apiDatas['uv_index'];
            return $model5;
        }
    }













    /**
     * Vérification que la case $number 
     * (Ex : 22 pour sun / 23 pour uv)
     * existe et a été sélectionné dans tab
     * on vérifie surtout qu'elle est visible en fonction de tab_lines
     * car elle peut être sélectionné, mais non visible
     * (Ex : si number existe en ligne 5, mais que tab_lines = 4, alors cela retourne false)
     * (Ex : si number existe en ligne 2, et que tab_lines = 3, alors cela retourne true)
     * 
     * @return type(bool)
     */
    public function is_tab($tab, $number)
    {

        $result = 0;
        for ($i = 1; $i <= $tab['tab_lines']; $i++) {
            $case1 = $tab['tab_' . $i . 'a'];
            $case2 = $tab['tab_' . $i . 'b'];
            $case3 = $tab['tab_' . $i . 'c'];

            if ($case1 == $number || $case2 == $number || $case3 == $number) {
                $result .= 1;
            } else {
                $result .= 0;
            }
        }
        if ($result >= 1) {
            $response = true;
        } else {
            $response = false;
        }
        return $response;
    }


    public function getWind($switch, $data)
    {
        if ($data != '&#8709;') {
            $kph = round(((floatval($data)) * 1.6093), 1);
            if ($switch['s_wind'] == 'kph') {
                $data = $kph;
            }
        }
        return $data;
    }
    public function getWindMphToKph($wind)
    {
        if ($wind != '&#8709;') {
            $kph = round(((floatval($wind)) * 1.6093), 1);
            $wind = $kph;
        }
        return $wind;
    }


    public function getRain($switch, $rain)
    {
        if ($rain != '&#8709;') {
            $in = round((floatval($rain)), 3);
            $mm = round(((floatval($rain)) * 25.4), 1);

            if ($switch['s_rain'] == 'in') {
                $page = $in;
            } elseif ($switch['s_rain'] == 'mm') {
                $page = $mm;
            }
        } else {
            $page = $rain;
        }
        return $page;
    }
    public function getRainInToMm($rain)
    {
        if ($rain != '&#8709;') {
            $rainM = round(((floatval($rain)) * 25.4), 1);
            $rain = $rainM;
        }
        return $rain;
    }

    public function getPress($switch, $press)
    {
        if ($press != '&#8709;') {
            $in = round((floatval($press)), 2);
            $mb = round(((floatval($press)) * 33.8639), 1);

            if ($switch['s_press'] == 'inhg') {
                $press = $in;
            } elseif ($switch['s_press'] == 'hpa') {
                $press = $mb;
            }
        }
        return $press;
    }


    public function getTemp($switch, $tempF)
    {
        if ($tempF != '&#8709;') {
            if ($switch['s_temp'] == 'C') {
                $tempC = round((5 / 9) * (floatval($tempF) - 32), 1);
                $tempF = $tempC;
            }
        }
        return $tempF;
    }

    public function getTempFtoC($tempF)
    {
        if ($tempF != '&#8709;') {
            $tempC = round((5 / 9) * (floatval($tempF) - 32), 1);
            $tempF = $tempC;
        }
        return $tempF;
    }

    //utilisé uniquement avec weewx 
    public function getTempCtoF($tempC)
    {
        if ($tempC != '&#8709;') {
            $tempF = round((9 / 5) * (floatval($tempC)) + 32, 2);
            $tempC = $tempF;
        }
        return $tempC;
    }
    //utilisé uniquement avec weewx 
    public function getPressMbtoIn($press)
    {
        if ($press != '&#8709;') {
            $mb = round(((floatval($press)) / 33.8639), 2);
            $press = $mb;
        }
        return $press;
    }
    //utilisé uniquement avec weewx 
    // en fait c'est Cm vers In, donc x10
    public function getRainMmToIn($rain)
    {
        if ($rain != '&#8709;') {
            $rainI = round(((floatval($rain) * 10) / 25.4), 3);
            $rain = $rainI;
        }
        return $rain;
    }


    public function getUnit($switch, $unit)
    {
        if ($unit == 'rain') {
            if ($switch['s_rain'] == 'in') {
                $page = 'in';
            } elseif ($switch['s_rain'] == 'mm') {
                $page = 'mm';
            }
        }
        if ($unit == 'wind') {
            if ($switch['s_wind'] == 'mph') {
                $page = 'mph';
            } elseif ($switch['s_wind'] == 'kph') {
                $page = $this->l->trad('KPH_2');
            }
        }
        if ($unit == 'press') {
            if ($switch['s_press'] == 'inhg') {
                $page = 'inHg';
            } elseif ($switch['s_press'] == 'hpa') {
                $page = 'hPa';
            }
        }
        if ($unit == 'temp') {
            if ($switch['s_temp'] == 'F') {
                $page = '°F';
            } elseif ($switch['s_temp'] == 'C') {
                $page = '°C';
            }
        }
        return $page;
    }


    public function getDMY($switch, $d, $m, $y)
    {
        if ($switch['s_dmy'] == 'D') {
            $page = $d;
        } elseif ($switch['s_dmy'] == 'M') {
            $page = $m;
        } elseif ($switch['s_dmy'] == 'Y') {
            $page = $y;
        }
        return $page;
    }




    /**
     * Ex : $value = 59°F (=15°C) :
     * Vérifie si on bascule en windchill (true)
     * sinon en heat (false)
     * 
     * Ex : $value = 32°F (=0°C) :
     * Vérifie s'il gèle (true)
     * sinon Temp positive (false)
     *
     * @param [float] $tempF
     * @return boolean
     */
    public function is_Temp($value, $tempF)
    {
        if ($tempF <= $value) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Formule d'Evapotranspiration Réelle 
     * Calculé par Mbell sans capteur solaire
     *
     */
    public function ETR_in($temp_high_f, $temp_low_f, $rain_in)
    {
        $zero = '&#8709;';
        $rain_in = ($rain_in == $zero) ? '0' : $rain_in;
        $temp_high_c = round((5 / 9) * (floatval($temp_high_f) - 32), 1);
        $temp_low_c = round((5 / 9) * (floatval($temp_low_f) - 32), 1);
        $temp = ($temp_high_c + $temp_low_c) / 2;
        $a = bcpow($temp, '3');
        $b = 0.05 * $a;
        $c = 25 * $temp;
        $d = $b + $c + 300;
        $e = bcpow($rain_in, '2');
        $f = bcpow($d, '2');
        $g = 0.9 + ($e / $f);
        $h = sqrt($g);
        $i = $rain_in / $h;

        $x = 1 / (0.8 + 0.14 * $temp);
        $z = $rain_in / 100;
        $y = $z - ($x * ($z * $z));
        $w = $y * 100;

        $u = 1 / (8 * $x);
        $v = 1 / (2 * $x);

        if (($z > $u) && ($z < $v)) {
            $ETR = $w;
        } else {
            $ETR = $i;
        }

        $ETR = round($i, 3);
        if ($ETR < 0) {
            $ETR = 0;
        }

        return $ETR;
    }





    /**
     * FUNCTION SUN
     * Made by Infoclimat
     * adapted for Mbell by Damien Belliard
     * 
     */
    public function is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $solar_radiation)
    {

        if ($latitude >= '-10' && $latitude < '12') {
            $b = '0';
        } elseif (($latitude >= '12' && $latitude < '29') || ($latitude >= '-29' && $latitude < '-10')) {
            $b = '0';
        } elseif (($latitude >= '29' && $latitude < '38') || ($latitude >= '-38' && $latitude < '-29')) {
            $b = '0.05';
        } elseif (($latitude >= '38' && $latitude < '42') || ($latitude >= '-42' && $latitude < '-38')) {
            $b = '0.06';
        } elseif (($latitude >= '42' && $latitude < '45.5') || ($latitude >= '-45.5' && $latitude < '-42')) {
            $b = '0.05';
        } elseif (($latitude >= '45.5' && $latitude < '47.5') || ($latitude >= '-47.5' && $latitude < '-45.5')) {
            $b = '0.06';
        } elseif (($latitude >= '47.5' && $latitude < '50') || ($latitude >= '-50' && $latitude < '-47.5')) {
            $b = '0.04';
        } elseif (($latitude >= '50' && $latitude < '55') || ($latitude >= '-55' && $latitude < '-50')) {
            $b = '0.06';
        } elseif (($latitude >= '55') || ($latitude < '-55')) {
            $b = '0.06';
        }

        if ($latitude >= '-10' && $latitude < '12') {
            $a = '0.67';
        } elseif (($latitude >= '12' && $latitude < '29') || ($latitude >= '-29' && $latitude < '-10')) {
            $a = '0.77';
        } elseif (($latitude >= '29' && $latitude < '38') || ($latitude >= '-38' && $latitude < '-29')) {
            $a = '0.73';
        } elseif (($latitude >= '38' && $latitude < '42') || ($latitude >= '-42' && $latitude < '-38')) {
            $a = '0.67';
        } elseif (($latitude >= '42' && $latitude < '45.5') || ($latitude >= '-45.5' && $latitude < '-42')) {
            $a = '0.71';
        } elseif (($latitude >= '45.5' && $latitude < '47.5') || ($latitude >= '-47.5' && $latitude < '-45.5')) {
            $a = '0.75';
        } elseif (($latitude >= '47.5' && $latitude < '50') || ($latitude >= '-50' && $latitude < '-47.5')) {
            $a = '0.75';
        } elseif (($latitude >= '50' && $latitude < '55') || ($latitude >= '-55' && $latitude < '-50')) {
            $a = '0.77';
        } elseif (($latitude >= '55') || ($latitude < '-55')) {
            $a = '0.74';
        }

        $timestamp = mktime($heure_utc, $minute_utc, 0, $mois, $jour, $annee);
        $dayofyear = date("z", $timestamp);
        $theta = 360 * $dayofyear / 365;
        $equatemps = 0.0172 + 0.4281 * cos((pi() / 180) * ($theta)) - 7.3515 * sin((pi() / 180) * ($theta)) - 3.3495 * cos(2 * (pi() / 180) * ($theta)) - 9.3619 * sin(2 * (pi() / 180) * ($theta));
        $corrtemps = $longitude * 4;
        $declinaison = asin(0.006918 - 0.399912 * cos((pi() / 180) * ($theta)) + 0.070257 * sin((pi() / 180) * ($theta)) - 0.006758 * cos(2 * (pi() / 180) * ($theta)) + 0.000908 * sin(2 * (pi() / 180) * ($theta))) * (180 / pi());
        $minutesjour = $heure_utc * 60 + $minute_utc;
        $tempsolaire = ($minutesjour + $corrtemps + $equatemps) / 60;
        $angle_horaire = ($tempsolaire - 12) * 15;
        $hauteur_soleil = asin(sin((pi() / 180) * ($latitude)) * sin((pi() / 180) * ($declinaison)) + cos((pi() / 180) * ($latitude)) * cos((pi() / 180) * ($declinaison)) * cos((pi() / 180) * ($angle_horaire))) * (180 / pi());

        $zero = '&#8709;';
        $solar_radiation = ($solar_radiation == $zero) ? 0 : $solar_radiation; // test pour resoudre bug php 7 si solar_rad = null

        if ($hauteur_soleil > 3) {
            $seuil = (floatval($a) + floatval($b) * cos((pi() / 180) * 360 * floatval($dayofyear) / 365)) * 1080 * pow((sin(pi() / 180) * $hauteur_soleil), 1.25) * 0.85;
            $mesure = (((floatval($temp_c) * 1 - 25) * (-0.0012) * $solar_radiation) + $solar_radiation);
            if ($mesure > $seuil) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    public function liveiTab($datas)
    {
        if (is_object($datas)) {
            $inc = '0';
        } else {
            $inc = array(
                "0" => '0',
                "1" => $datas['wind_speed_avg_last_10_min'] ?? ($datas['wind_speed_10_min_avg'] ?? '0'),
                "2" => $datas['temp'] ?? ($datas['temp_out'] ?? '0'),
                "3" => $datas['rain_rate_last_in'] ?? ($datas['rain_rate_in'] ?? '0'),
                "4" => $datas['wind_speed_hi_last_10_min'] ?? ($datas['wind_speed_10_min_avg'] ?? '0'),
                "5" => $datas['wind_chill'] ?? ($datas['heat_index'] ?? '0'),
                "6" => $datas['rainfall_last_24_hr_in'] ?? ($datas['rain_day_in'] ?? '0'),
                "7" => '0',
                "8" => $datas['solar_rad'] ?? ($datas['uv_index'] ?? ($datas['uv'] ?? '0')),
                "9" => $datas['et_day'] ?? ($datas['et_last'] ?? ($datas['rain_day_in'] ?? '0')),
                "10" => $datas['bar_absolute'] ?? ($datas['bar'] ?? '0'),
                "11" => $datas['dew_point'] ?? '0',
                "12" => $datas['hum'] ?? ($datas['hum_out'] ?? '0'),
                "13" => $datas['rainfall_monthly_in'] ?? ($datas['rain_month_in'] ?? '0'),
                "14" => $datas['rainfall_year_in'] ?? ($datas['rain_year_in'] ?? '0'),
                "15" => $datas['temp_1'] ?? ($datas['temp_extra_1'] ?? '0'),
                "16" => $datas['temp_2'] ?? ($datas['temp_extra_2'] ?? '0'),
                "17" => $datas['temp_3'] ?? ($datas['temp_extra_3'] ?? '0'),
                "18" => $datas['temp_4'] ?? ($datas['temp_extra_4'] ?? '0'),
                "19" => $datas['temp_5'] ?? ($datas['temp_extra_5'] ?? '0'),
                "20" => $datas['temp_6'] ?? ($datas['temp_extra_6'] ?? '0'),
                "21" => $datas['temp_7'] ?? ($datas['temp_extra_7'] ?? '0'),
                "22" => $datas['solar_rad'] ?? '0',
                "23" => $datas['uv_index'] ?? ($datas['uv'] ?? '0'),
                "24" => $datas['temp_leaf_1'] ?? '0',
                "25" => $datas['temp_leaf_2'] ?? '0',
                "26" => $datas['temp_soil_1'] ?? '0',
                "27" => $datas['temp_soil_2'] ?? '0',
                "28" => $datas['temp_soil_3'] ?? '0',
                "29" => $datas['temp_soil_4'] ?? '0',
                "30" => $datas['hum_extra_1'] ?? '0',
                "31" => $datas['hum_extra_2'] ?? '0',
                "32" => $datas['hum_extra_3'] ?? '0',
                "33" => $datas['hum_extra_4'] ?? '0',
                "34" => $datas['hum_extra_5'] ?? '0',
                "35" => $datas['hum_extra_6'] ?? '0',
                "36" => $datas['hum_extra_7'] ?? '0',
                "37" => $datas['wet_leaf_1'] ?? '0',
                "38" => $datas['wet_leaf_2'] ?? '0',
                "39" => $datas['moist_soil_1'] ?? '0',
                "40" => $datas['moist_soil_2'] ?? '0',
                "41" => $datas['moist_soil_3'] ?? '0',
                "42" => $datas['moist_soil_4'] ?? '0',
                "43" => $datas['temp_in'] ?? '0',
                "44" => $datas['hum_in'] ?? '0',
                "45" => '0',
                "46" => $datas['wind_dir_last'] ?? ($datas['wind_dir'] ?? '0'),
                "47" => $datas['temp_leaf_3'] ?? '0',
                "48" => $datas['temp_leaf_4'] ?? '0',
                "49" => $datas['wet_leaf_3'] ?? '0',
                "50" => $datas['wet_leaf_4'] ?? '0',
            );
        }
        return $inc;
    }
}
