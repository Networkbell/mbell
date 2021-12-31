<?php

class StationView extends View
{

    public function __construct()
    {
        parent::__construct();
    }



    public function livePress($press)
    {
        //$in = round((floatval($press)), 2);
        $mb = round(((floatval($press)) * 33.8639), 1);
        return $mb;
    }

    /**
     * Pour Weatherlink Live
     * calcul sunset-sunrise à partir de timestamp
     */
    public function liveDateSun($time, $latitude, $longitude, $fuseau, $type)
    {
        date_default_timezone_set($fuseau);
        $date = date("h:i a", date_sun_info($time, $latitude, $longitude)[$type]);
        return $date;
    }


    /**
     * Pour API v1
     * calcul time format RCF822 à partir de timestamp
     */
    public function liveDateRFC822($time, $fuseau)
    {
        date_default_timezone_set($fuseau);
        $date = date(DATE_RFC822, $time);
        return $date;
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
    public function getAPIDatas($datas, $station, $livestation)
    {

        $zero = '&#8709;';
        $type = (isset($station['stat_type'])) ? $station['stat_type'] : $zero;
        
        if ($type == 'live') {
            $dat = $zero;
        } elseif ($type == 'v1' || $type == 'v2') {
            $dat = isset($datas->davis_current_observation) ? $datas->davis_current_observation : $zero;
        } else {
            $dat = $zero;
        }


        $response = array(


            //V1
            "temp_c" => ($type == 'live') ?  $zero : (isset($datas->temp_c) ? $datas->temp_c : $zero),
            "pressure_tendency_string" => ($type == 'live') ? $zero : (isset($dat->pressure_tendency_string) ? $dat->pressure_tendency_string : $zero),
            "pressure_mb" => ($type == 'live') ? $zero : (isset($datas->pressure_mb) ? $datas->pressure_mb : $zero),
            "time_RFC822" => ($type == 'live') ? $zero : (isset($datas->observation_time_rfc822) ? $datas->observation_time_rfc822 : $zero),
            "sunset" => ($type == 'live') ? $zero : (isset($dat->sunset) ? $dat->sunset : $zero),
            "sunrise" => ($type == 'live') ? $zero : (isset($dat->sunrise) ? $dat->sunrise : $zero),
            "wind_day_high_mph" => ($type == 'live') ?  $zero : (isset($dat->wind_day_high_mph) ? $dat->wind_day_high_mph : $zero),
            "wind_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->wind_day_high_time) ? $dat->wind_day_high_time : $zero),
            "wind_month_high_mph" => ($type == 'live') ?  $zero : (isset($dat->wind_month_high_mph) ? $dat->wind_month_high_mph : $zero),
            "wind_year_high_mph" => ($type == 'live') ?  $zero : (isset($dat->wind_year_high_mph) ? $dat->wind_year_high_mph : $zero),
            "et_day" => ($type == 'live') ?  $zero : (isset($dat->et_day) ? $dat->et_day : $zero),
            "temp_day_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_day_high_f) ? $dat->temp_day_high_f : $zero),
            "temp_day_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_day_low_f) ? $dat->temp_day_low_f : $zero),
            "temp_extra_1" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1) ? $dat->temp_extra_1 : $zero),
            "temp_extra_2" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2) ? $dat->temp_extra_2 : $zero),
            "temp_extra_3" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3) ? $dat->temp_extra_3 : $zero),
            "temp_extra_4" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4) ? $dat->temp_extra_4 : $zero),
            "temp_extra_5" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5) ? $dat->temp_extra_5 : $zero),
            "temp_extra_6" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6) ? $dat->temp_extra_6 : $zero),
            "temp_extra_7" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7) ? $dat->temp_extra_7 : $zero),
            "relative_humidity_1" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1) ? $dat->relative_humidity_1 : $zero),
            "relative_humidity_2" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2) ? $dat->relative_humidity_2 : $zero),
            "relative_humidity_3" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3) ? $dat->relative_humidity_3 : $zero),
            "relative_humidity_4" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4) ? $dat->relative_humidity_4 : $zero),
            "relative_humidity_5" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5) ? $dat->relative_humidity_5 : $zero),
            "relative_humidity_6" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6) ? $dat->relative_humidity_6 : $zero),
            "relative_humidity_7" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7) ? $dat->relative_humidity_7 : $zero),
            "temp_leaf_1" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1) ? $dat->temp_leaf_1 : $zero),
            "temp_leaf_2" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2) ? $dat->temp_leaf_2 : $zero),
            "temp_soil_1" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1) ? $dat->temp_soil_1 : $zero),
            "temp_soil_2" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2) ? $dat->temp_soil_2 : $zero),
            "temp_soil_3" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3) ? $dat->temp_soil_3 : $zero),
            "temp_soil_4" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4) ? $dat->temp_soil_4 : $zero),
            "leaf_wetness_1" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1) ? $dat->leaf_wetness_1 : $zero),
            "leaf_wetness_2" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2) ? $dat->leaf_wetness_2 : $zero),
            "soil_moisture_1" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1) ? $dat->soil_moisture_1 : $zero),
            "soil_moisture_2" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2) ? $dat->soil_moisture_2 : $zero),
            "soil_moisture_3" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3) ? $dat->soil_moisture_3 : $zero),
            "soil_moisture_4" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4) ? $dat->soil_moisture_4 : $zero),
            "temp_day_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_day_low_f) ? $dat->temp_day_low_f : $zero),
            "temp_month_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_month_low_f) ? $dat->temp_month_low_f : $zero),
            "temp_year_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_year_low_f) ? $dat->temp_year_low_f : $zero),
            "temp_day_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_day_high_f) ? $dat->temp_day_high_f : $zero),
            "temp_month_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_month_high_f) ? $dat->temp_month_high_f : $zero),
            "temp_year_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_year_high_f) ? $dat->temp_year_high_f : $zero),
            "temp_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_day_low_time) ? $dat->temp_day_low_time : $zero),
            "temp_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_day_high_time) ? $dat->temp_day_high_time : $zero),
            "pressure_day_low_in" => ($type == 'live') ?  $zero : (isset($dat->pressure_day_low_in) ? $dat->pressure_day_low_in : $zero),
            "pressure_month_low_in" => ($type == 'live') ?  $zero : (isset($dat->pressure_month_low_in) ? $dat->pressure_month_low_in : $zero),
            "pressure_year_low_in" => ($type == 'live') ?  $zero : (isset($dat->pressure_year_low_in) ? $dat->pressure_year_low_in : $zero),
            "pressure_day_high_in" => ($type == 'live') ?  $zero : (isset($dat->pressure_day_high_in) ? $dat->pressure_day_high_in : $zero),
            "pressure_month_high_in" => ($type == 'live') ?  $zero : (isset($dat->pressure_month_high_in) ? $dat->pressure_month_high_in : $zero),
            "pressure_year_high_in" => ($type == 'live') ?  $zero : (isset($dat->pressure_year_high_in) ? $dat->pressure_year_high_in : $zero),
            "pressure_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->pressure_day_low_time) ? $dat->pressure_day_low_time : $zero),
            "pressure_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->pressure_day_high_time) ? $dat->pressure_day_high_time : $zero),
            "dewpoint_day_low_f" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_day_low_f) ? $dat->dewpoint_day_low_f : $zero),
            "dewpoint_month_low_f" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_month_low_f) ? $dat->dewpoint_month_low_f : $zero),
            "dewpoint_year_low_f" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_year_low_f) ? $dat->dewpoint_year_low_f : $zero),
            "dewpoint_day_high_f" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_day_high_f) ? $dat->dewpoint_day_high_f : $zero),
            "dewpoint_month_high_f" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_month_high_f) ? $dat->dewpoint_month_high_f : $zero),
            "dewpoint_year_high_f" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_year_high_f) ? $dat->dewpoint_year_high_f : $zero),
            "dewpoint_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_day_low_time) ? $dat->dewpoint_day_low_time : $zero),
            "dewpoint_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->dewpoint_day_high_time) ? $dat->dewpoint_day_high_time : $zero),
            "relative_humidity_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_day_low) ? $dat->relative_humidity_day_low : $zero),
            "relative_humidity_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_month_low) ? $dat->relative_humidity_month_low : $zero),
            "relative_humidity_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_year_low) ? $dat->relative_humidity_year_low : $zero),
            "relative_humidity_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_day_high) ? $dat->relative_humidity_day_high : $zero),
            "relative_humidity_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_month_high) ? $dat->relative_humidity_month_high : $zero),
            "relative_humidity_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_year_high) ? $dat->relative_humidity_year_high : $zero),
            "relative_humidity_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_day_low_time) ? $dat->relative_humidity_day_low_time : $zero),
            "relative_humidity_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_day_high_time) ? $dat->relative_humidity_day_high_time : $zero),
            "temp_extra_1_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_day_low) ? $dat->temp_extra_1_day_low : $zero),
            "temp_extra_1_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_month_low) ? $dat->temp_extra_1_month_low : $zero),
            "temp_extra_1_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_year_low) ? $dat->temp_extra_1_year_low : $zero),
            "temp_extra_1_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_day_high) ? $dat->temp_extra_1_day_high : $zero),
            "temp_extra_1_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_month_high) ? $dat->temp_extra_1_month_high : $zero),
            "temp_extra_1_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_year_high) ? $dat->temp_extra_1_year_high : $zero),
            "temp_extra_1_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_day_low_time) ? $dat->temp_extra_1_day_low_time : $zero),
            "temp_extra_1_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_1_day_high_time) ? $dat->temp_extra_1_day_high_time : $zero),
            "temp_extra_2_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_day_low) ? $dat->temp_extra_2_day_low : $zero),
            "temp_extra_2_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_month_low) ? $dat->temp_extra_2_month_low : $zero),
            "temp_extra_2_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_year_low) ? $dat->temp_extra_2_year_low : $zero),
            "temp_extra_2_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_day_high) ? $dat->temp_extra_2_day_high : $zero),
            "temp_extra_2_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_month_high) ? $dat->temp_extra_2_month_high : $zero),
            "temp_extra_2_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_year_high) ? $dat->temp_extra_2_year_high : $zero),
            "temp_extra_2_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_day_low_time) ? $dat->temp_extra_2_day_low_time : $zero),
            "temp_extra_2_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_2_day_high_time) ? $dat->temp_extra_2_day_high_time : $zero),
            "temp_extra_3_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_day_low) ? $dat->temp_extra_3_day_low : $zero),
            "temp_extra_3_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_month_low) ? $dat->temp_extra_3_month_low : $zero),
            "temp_extra_3_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_year_low) ? $dat->temp_extra_3_year_low : $zero),
            "temp_extra_3_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_day_high) ? $dat->temp_extra_3_day_high : $zero),
            "temp_extra_3_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_month_high) ? $dat->temp_extra_3_month_high : $zero),
            "temp_extra_3_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_year_high) ? $dat->temp_extra_3_year_high : $zero),
            "temp_extra_3_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_day_low_time) ? $dat->temp_extra_3_day_low_time : $zero),
            "temp_extra_3_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_3_day_high_time) ? $dat->temp_extra_3_day_high_time : $zero),
            "temp_extra_4_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_day_low) ? $dat->temp_extra_4_day_low : $zero),
            "temp_extra_4_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_month_low) ? $dat->temp_extra_4_month_low : $zero),
            "temp_extra_4_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_year_low) ? $dat->temp_extra_4_year_low : $zero),
            "temp_extra_4_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_day_high) ? $dat->temp_extra_4_day_high : $zero),
            "temp_extra_4_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_month_high) ? $dat->temp_extra_4_month_high : $zero),
            "temp_extra_4_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_year_high) ? $dat->temp_extra_4_year_high : $zero),
            "temp_extra_4_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_day_low_time) ? $dat->temp_extra_4_day_low_time : $zero),
            "temp_extra_4_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_4_day_high_time) ? $dat->temp_extra_4_day_high_time : $zero),
            "temp_extra_5_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_day_low) ? $dat->temp_extra_5_day_low : $zero),
            "temp_extra_5_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_month_low) ? $dat->temp_extra_5_month_low : $zero),
            "temp_extra_5_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_year_low) ? $dat->temp_extra_5_year_low : $zero),
            "temp_extra_5_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_day_high) ? $dat->temp_extra_5_day_high : $zero),
            "temp_extra_5_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_month_high) ? $dat->temp_extra_5_month_high : $zero),
            "temp_extra_5_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_year_high) ? $dat->temp_extra_5_year_high : $zero),
            "temp_extra_5_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_day_low_time) ? $dat->temp_extra_5_day_low_time : $zero),
            "temp_extra_5_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_5_day_high_time) ? $dat->temp_extra_5_day_high_time : $zero),
            "temp_extra_6_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_day_low) ? $dat->temp_extra_6_day_low : $zero),
            "temp_extra_6_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_month_low) ? $dat->temp_extra_6_month_low : $zero),
            "temp_extra_6_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_year_low) ? $dat->temp_extra_6_year_low : $zero),
            "temp_extra_6_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_day_high) ? $dat->temp_extra_6_day_high : $zero),
            "temp_extra_6_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_month_high) ? $dat->temp_extra_6_month_high : $zero),
            "temp_extra_6_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_year_high) ? $dat->temp_extra_6_year_high : $zero),
            "temp_extra_6_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_day_low_time) ? $dat->temp_extra_6_day_low_time : $zero),
            "temp_extra_6_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_6_day_high_time) ? $dat->temp_extra_6_day_high_time : $zero),
            "temp_extra_7_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_day_low) ? $dat->temp_extra_7_day_low : $zero),
            "temp_extra_7_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_month_low) ? $dat->temp_extra_7_month_low : $zero),
            "temp_extra_7_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_year_low) ? $dat->temp_extra_7_year_low : $zero),
            "temp_extra_7_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_day_high) ? $dat->temp_extra_7_day_high : $zero),
            "temp_extra_7_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_month_high) ? $dat->temp_extra_7_month_high : $zero),
            "temp_extra_7_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_year_high) ? $dat->temp_extra_7_year_high : $zero),
            "temp_extra_7_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_day_low_time) ? $dat->temp_extra_7_day_low_time : $zero),
            "temp_extra_7_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_extra_7_day_high_time) ? $dat->temp_extra_7_day_high_time : $zero),
            "temp_leaf_1_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_day_low) ? $dat->temp_leaf_1_day_low : $zero),
            "temp_leaf_1_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_month_low) ? $dat->temp_leaf_1_month_low : $zero),
            "temp_leaf_1_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_year_low) ? $dat->temp_leaf_1_year_low : $zero),
            "temp_leaf_1_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_day_high) ? $dat->temp_leaf_1_day_high : $zero),
            "temp_leaf_1_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_month_high) ? $dat->temp_leaf_1_month_high : $zero),
            "temp_leaf_1_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_year_high) ? $dat->temp_leaf_1_year_high : $zero),
            "temp_leaf_1_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_day_low_time) ? $dat->temp_leaf_1_day_low_time : $zero),
            "temp_leaf_1_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_1_day_high_time) ? $dat->temp_leaf_1_day_high_time : $zero),
            "temp_leaf_2_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_day_low) ? $dat->temp_leaf_2_day_low : $zero),
            "temp_leaf_2_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_month_low) ? $dat->temp_leaf_2_month_low : $zero),
            "temp_leaf_2_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_year_low) ? $dat->temp_leaf_2_year_low : $zero),
            "temp_leaf_2_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_day_high) ? $dat->temp_leaf_2_day_high : $zero),
            "temp_leaf_2_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_month_high) ? $dat->temp_leaf_2_month_high : $zero),
            "temp_leaf_2_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_year_high) ? $dat->temp_leaf_2_year_high : $zero),
            "temp_leaf_2_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_day_low_time) ? $dat->temp_leaf_2_day_low_time : $zero),
            "temp_leaf_2_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_leaf_2_day_high_time) ? $dat->temp_leaf_2_day_high_time : $zero),
            "temp_soil_1_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_day_low) ? $dat->temp_soil_1_day_low : $zero),
            "temp_soil_1_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_month_low) ? $dat->temp_soil_1_month_low : $zero),
            "temp_soil_1_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_year_low) ? $dat->temp_soil_1_year_low : $zero),
            "temp_soil_1_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_day_high) ? $dat->temp_soil_1_day_high : $zero),
            "temp_soil_1_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_month_high) ? $dat->temp_soil_1_month_high : $zero),
            "temp_soil_1_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_year_high) ? $dat->temp_soil_1_year_high : $zero),
            "temp_soil_1_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_day_low_time) ? $dat->temp_soil_1_day_low_time : $zero),
            "temp_soil_1_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_1_day_high_time) ? $dat->temp_soil_1_day_high_time : $zero),
            "temp_soil_2_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_day_low) ? $dat->temp_soil_2_day_low : $zero),
            "temp_soil_2_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_month_low) ? $dat->temp_soil_2_month_low : $zero),
            "temp_soil_2_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_year_low) ? $dat->temp_soil_2_year_low : $zero),
            "temp_soil_2_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_day_high) ? $dat->temp_soil_2_day_high : $zero),
            "temp_soil_2_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_month_high) ? $dat->temp_soil_2_month_high : $zero),
            "temp_soil_2_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_year_high) ? $dat->temp_soil_2_year_high : $zero),
            "temp_soil_2_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_day_low_time) ? $dat->temp_soil_2_day_low_time : $zero),
            "temp_soil_2_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_2_day_high_time) ? $dat->temp_soil_2_day_high_time : $zero),
            "temp_soil_3_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_day_low) ? $dat->temp_soil_3_day_low : $zero),
            "temp_soil_3_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_month_low) ? $dat->temp_soil_3_month_low : $zero),
            "temp_soil_3_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_year_low) ? $dat->temp_soil_3_year_low : $zero),
            "temp_soil_3_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_day_high) ? $dat->temp_soil_3_day_high : $zero),
            "temp_soil_3_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_month_high) ? $dat->temp_soil_3_month_high : $zero),
            "temp_soil_3_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_year_high) ? $dat->temp_soil_3_year_high : $zero),
            "temp_soil_3_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_day_low_time) ? $dat->temp_soil_3_day_low_time : $zero),
            "temp_soil_3_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_3_day_high_time) ? $dat->temp_soil_3_day_high_time : $zero),
            "temp_soil_4_day_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_day_low) ? $dat->temp_soil_4_day_low : $zero),
            "temp_soil_4_month_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_month_low) ? $dat->temp_soil_4_month_low : $zero),
            "temp_soil_4_year_low" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_year_low) ? $dat->temp_soil_4_year_low : $zero),
            "temp_soil_4_day_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_day_high) ? $dat->temp_soil_4_day_high : $zero),
            "temp_soil_4_month_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_month_high) ? $dat->temp_soil_4_month_high : $zero),
            "temp_soil_4_year_high" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_year_high) ? $dat->temp_soil_4_year_high : $zero),
            "temp_soil_4_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_day_low_time) ? $dat->temp_soil_4_day_low_time : $zero),
            "temp_soil_4_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_soil_4_day_high_time) ? $dat->temp_soil_4_day_high_time : $zero),
            "relative_humidity_1_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_day_low) ? $dat->relative_humidity_1_day_low : $zero),
            "relative_humidity_1_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_month_low) ? $dat->relative_humidity_1_month_low : $zero),
            "relative_humidity_1_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_year_low) ? $dat->relative_humidity_1_year_low : $zero),
            "relative_humidity_1_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_day_high) ? $dat->relative_humidity_1_day_high : $zero),
            "relative_humidity_1_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_month_high) ? $dat->relative_humidity_1_month_high : $zero),
            "relative_humidity_1_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_year_high) ? $dat->relative_humidity_1_year_high : $zero),
            "relative_humidity_1_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_day_low_time) ? $dat->relative_humidity_1_day_low_time : $zero),
            "relative_humidity_1_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_1_day_high_time) ? $dat->relative_humidity_1_day_high_time : $zero),
            "relative_humidity_2_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_day_low) ? $dat->relative_humidity_2_day_low : $zero),
            "relative_humidity_2_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_month_low) ? $dat->relative_humidity_2_month_low : $zero),
            "relative_humidity_2_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_year_low) ? $dat->relative_humidity_2_year_low : $zero),
            "relative_humidity_2_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_day_high) ? $dat->relative_humidity_2_day_high : $zero),
            "relative_humidity_2_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_month_high) ? $dat->relative_humidity_2_month_high : $zero),
            "relative_humidity_2_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_year_high) ? $dat->relative_humidity_2_year_high : $zero),
            "relative_humidity_2_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_day_low_time) ? $dat->relative_humidity_2_day_low_time : $zero),
            "relative_humidity_2_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_2_day_high_time) ? $dat->relative_humidity_2_day_high_time : $zero),
            "relative_humidity_3_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_day_low) ? $dat->relative_humidity_3_day_low : $zero),
            "relative_humidity_3_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_month_low) ? $dat->relative_humidity_3_month_low : $zero),
            "relative_humidity_3_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_year_low) ? $dat->relative_humidity_3_year_low : $zero),
            "relative_humidity_3_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_day_high) ? $dat->relative_humidity_3_day_high : $zero),
            "relative_humidity_3_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_month_high) ? $dat->relative_humidity_3_month_high : $zero),
            "relative_humidity_3_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_year_high) ? $dat->relative_humidity_3_year_high : $zero),
            "relative_humidity_3_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_day_low_time) ? $dat->relative_humidity_3_day_low_time : $zero),
            "relative_humidity_3_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_3_day_high_time) ? $dat->relative_humidity_3_day_high_time : $zero),
            "relative_humidity_4_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_day_low) ? $dat->relative_humidity_4_day_low : $zero),
            "relative_humidity_4_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_month_low) ? $dat->relative_humidity_4_month_low : $zero),
            "relative_humidity_4_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_year_low) ? $dat->relative_humidity_4_year_low : $zero),
            "relative_humidity_4_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_day_high) ? $dat->relative_humidity_4_day_high : $zero),
            "relative_humidity_4_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_month_high) ? $dat->relative_humidity_4_month_high : $zero),
            "relative_humidity_4_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_year_high) ? $dat->relative_humidity_4_year_high : $zero),
            "relative_humidity_4_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_day_low_time) ? $dat->relative_humidity_4_day_low_time : $zero),
            "relative_humidity_4_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_4_day_high_time) ? $dat->relative_humidity_4_day_high_time : $zero),
            "relative_humidity_5_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_day_low) ? $dat->relative_humidity_5_day_low : $zero),
            "relative_humidity_5_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_month_low) ? $dat->relative_humidity_5_month_low : $zero),
            "relative_humidity_5_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_year_low) ? $dat->relative_humidity_5_year_low : $zero),
            "relative_humidity_5_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_day_high) ? $dat->relative_humidity_5_day_high : $zero),
            "relative_humidity_5_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_month_high) ? $dat->relative_humidity_5_month_high : $zero),
            "relative_humidity_5_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_year_high) ? $dat->relative_humidity_5_year_high : $zero),
            "relative_humidity_5_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_day_low_time) ? $dat->relative_humidity_5_day_low_time : $zero),
            "relative_humidity_5_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_5_day_high_time) ? $dat->relative_humidity_5_day_high_time : $zero),
            "relative_humidity_6_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_day_low) ? $dat->relative_humidity_6_day_low : $zero),
            "relative_humidity_6_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_month_low) ? $dat->relative_humidity_6_month_low : $zero),
            "relative_humidity_6_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_year_low) ? $dat->relative_humidity_6_year_low : $zero),
            "relative_humidity_6_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_day_high) ? $dat->relative_humidity_6_day_high : $zero),
            "relative_humidity_6_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_month_high) ? $dat->relative_humidity_6_month_high : $zero),
            "relative_humidity_6_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_year_high) ? $dat->relative_humidity_6_year_high : $zero),
            "relative_humidity_6_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_day_low_time) ? $dat->relative_humidity_6_day_low_time : $zero),
            "relative_humidity_6_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_6_day_high_time) ? $dat->relative_humidity_6_day_high_time : $zero),
            "relative_humidity_7_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_day_low) ? $dat->relative_humidity_7_day_low : $zero),
            "relative_humidity_7_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_month_low) ? $dat->relative_humidity_7_month_low : $zero),
            "relative_humidity_7_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_year_low) ? $dat->relative_humidity_7_year_low : $zero),
            "relative_humidity_7_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_day_high) ? $dat->relative_humidity_7_day_high : $zero),
            "relative_humidity_7_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_month_high) ? $dat->relative_humidity_7_month_high : $zero),
            "relative_humidity_7_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_year_high) ? $dat->relative_humidity_7_year_high : $zero),
            "relative_humidity_7_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_day_low_time) ? $dat->relative_humidity_7_day_low_time : $zero),
            "relative_humidity_7_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_7_day_high_time) ? $dat->relative_humidity_7_day_high_time : $zero),
            "leaf_wetness_1_day_low" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_day_low) ? $dat->leaf_wetness_1_day_low : $zero),
            "leaf_wetness_1_month_low" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_month_low) ? $dat->leaf_wetness_1_month_low : $zero),
            "leaf_wetness_1_year_low" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_year_low) ? $dat->leaf_wetness_1_year_low : $zero),
            "leaf_wetness_1_day_high" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_day_high) ? $dat->leaf_wetness_1_day_high : $zero),
            "leaf_wetness_1_month_high" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_month_high) ? $dat->leaf_wetness_1_month_high : $zero),
            "leaf_wetness_1_year_high" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_year_high) ? $dat->leaf_wetness_1_year_high : $zero),
            "leaf_wetness_1_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_day_low_time) ? $dat->leaf_wetness_1_day_low_time : $zero),
            "leaf_wetness_1_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_1_day_high_time) ? $dat->leaf_wetness_1_day_high_time : $zero),
            "leaf_wetness_2_day_low" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_day_low) ? $dat->leaf_wetness_2_day_low : $zero),
            "leaf_wetness_2_month_low" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_month_low) ? $dat->leaf_wetness_2_month_low : $zero),
            "leaf_wetness_2_year_low" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_year_low) ? $dat->leaf_wetness_2_year_low : $zero),
            "leaf_wetness_2_day_high" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_day_high) ? $dat->leaf_wetness_2_day_high : $zero),
            "leaf_wetness_2_month_high" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_month_high) ? $dat->leaf_wetness_2_month_high : $zero),
            "leaf_wetness_2_year_high" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_year_high) ? $dat->leaf_wetness_2_year_high : $zero),
            "leaf_wetness_2_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_day_low_time) ? $dat->leaf_wetness_2_day_low_time : $zero),
            "leaf_wetness_2_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->leaf_wetness_2_day_high_time) ? $dat->leaf_wetness_2_day_high_time : $zero),
            "soil_moisture_1_day_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_day_low) ? $dat->soil_moisture_1_day_low : $zero),
            "soil_moisture_1_month_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_month_low) ? $dat->soil_moisture_1_month_low : $zero),
            "soil_moisture_1_year_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_year_low) ? $dat->soil_moisture_1_year_low : $zero),
            "soil_moisture_1_day_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_day_high) ? $dat->soil_moisture_1_day_high : $zero),
            "soil_moisture_1_month_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_month_high) ? $dat->soil_moisture_1_month_high : $zero),
            "soil_moisture_1_year_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_year_high) ? $dat->soil_moisture_1_year_high : $zero),
            "soil_moisture_1_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_day_low_time) ? $dat->soil_moisture_1_day_low_time : $zero),
            "soil_moisture_1_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_1_day_high_time) ? $dat->soil_moisture_1_day_high_time : $zero),
            "soil_moisture_2_day_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_day_low) ? $dat->soil_moisture_2_day_low : $zero),
            "soil_moisture_2_month_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_month_low) ? $dat->soil_moisture_2_month_low : $zero),
            "soil_moisture_2_year_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_year_low) ? $dat->soil_moisture_2_year_low : $zero),
            "soil_moisture_2_day_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_day_high) ? $dat->soil_moisture_2_day_high : $zero),
            "soil_moisture_2_month_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_month_high) ? $dat->soil_moisture_2_month_high : $zero),
            "soil_moisture_2_year_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_year_high) ? $dat->soil_moisture_2_year_high : $zero),
            "soil_moisture_2_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_day_low_time) ? $dat->soil_moisture_2_day_low_time : $zero),
            "soil_moisture_2_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_2_day_high_time) ? $dat->soil_moisture_2_day_high_time : $zero),
            "soil_moisture_3_day_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_day_low) ? $dat->soil_moisture_3_day_low : $zero),
            "soil_moisture_3_month_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_month_low) ? $dat->soil_moisture_3_month_low : $zero),
            "soil_moisture_3_year_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_year_low) ? $dat->soil_moisture_3_year_low : $zero),
            "soil_moisture_3_day_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_day_high) ? $dat->soil_moisture_3_day_high : $zero),
            "soil_moisture_3_month_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_month_high) ? $dat->soil_moisture_3_month_high : $zero),
            "soil_moisture_3_year_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_year_high) ? $dat->soil_moisture_3_year_high : $zero),
            "soil_moisture_3_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_day_low_time) ? $dat->soil_moisture_3_day_low_time : $zero),
            "soil_moisture_3_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_3_day_high_time) ? $dat->soil_moisture_3_day_high_time : $zero),
            "soil_moisture_4_day_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_day_low) ? $dat->soil_moisture_4_day_low : $zero),
            "soil_moisture_4_month_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_month_low) ? $dat->soil_moisture_4_month_low : $zero),
            "soil_moisture_4_year_low" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_year_low) ? $dat->soil_moisture_4_year_low : $zero),
            "soil_moisture_4_day_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_day_high) ? $dat->soil_moisture_4_day_high : $zero),
            "soil_moisture_4_month_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_month_high) ? $dat->soil_moisture_4_month_high : $zero),
            "soil_moisture_4_year_high" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_year_high) ? $dat->soil_moisture_4_year_high : $zero),
            "soil_moisture_4_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_day_low_time) ? $dat->soil_moisture_4_day_low_time : $zero),
            "soil_moisture_4_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->soil_moisture_4_day_high_time) ? $dat->soil_moisture_4_day_high_time : $zero),
            "temp_in_day_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_in_day_low_f) ? $dat->temp_in_day_low_f : $zero),
            "temp_in_month_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_in_month_low_f) ? $dat->temp_in_month_low_f : $zero),
            "temp_in_year_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_in_year_low_f) ? $dat->temp_in_year_low_f : $zero),
            "temp_in_day_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_in_day_high_f) ? $dat->temp_in_day_high_f : $zero),
            "temp_in_month_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_in_month_high_f) ? $dat->temp_in_month_high_f : $zero),
            "temp_in_year_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_in_year_high_f) ? $dat->temp_in_year_high_f : $zero),
            "temp_in_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->temp_in_day_low_time) ? $dat->temp_in_day_low_time : $zero),
            "temp_in_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->temp_in_day_high_time) ? $dat->temp_in_day_high_time : $zero),
            "relative_humidity_in_day_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_day_low) ? $dat->relative_humidity_in_day_low : $zero),
            "relative_humidity_in_month_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_month_low) ? $dat->relative_humidity_in_month_low : $zero),
            "relative_humidity_in_year_low" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_year_low) ? $dat->relative_humidity_in_year_low : $zero),
            "relative_humidity_in_day_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_day_high) ? $dat->relative_humidity_in_day_high : $zero),
            "relative_humidity_in_month_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_month_high) ? $dat->relative_humidity_in_month_high : $zero),
            "relative_humidity_in_year_high" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_year_high) ? $dat->relative_humidity_in_year_high : $zero),
            "relative_humidity_in_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_day_low_time) ? $dat->relative_humidity_in_day_low_time : $zero),
            "relative_humidity_in_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->relative_humidity_in_day_high_time) ? $dat->relative_humidity_in_day_high_time : $zero),
            "temp_day_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_day_high_f) ? $dat->temp_day_high_f : $zero),
            "temp_month_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_month_high_f) ? $dat->temp_month_high_f : $zero),
            "temp_year_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_year_high_f) ? $dat->temp_year_high_f : $zero),
            "temp_day_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_day_low_f) ? $dat->temp_day_low_f : $zero),
            "temp_month_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_month_low_f) ? $dat->temp_month_low_f : $zero),
            "temp_year_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_year_low_f) ? $dat->temp_year_low_f : $zero),
            "windchill_day_low_f" => ($type == 'live') ?  $zero : (isset($dat->windchill_day_low_f) ? $dat->windchill_day_low_f : $zero),
            "windchill_day_low_time" => ($type == 'live') ?  $zero : (isset($dat->windchill_day_low_time) ? $dat->windchill_day_low_time : $zero),
            "windchill_month_low_f" => ($type == 'live') ?  $zero : (isset($dat->windchill_month_low_f) ? $dat->windchill_month_low_f : $zero),
            "windchill_year_low_f" => ($type == 'live') ?  $zero : (isset($dat->windchill_year_low_f) ? $dat->windchill_year_low_f : $zero),
            "heat_index_day_high_f" => ($type == 'live') ?  $zero : (isset($dat->heat_index_day_high_f) ? $dat->heat_index_day_high_f : $zero),
            "heat_index_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->heat_index_day_high_time) ? $dat->heat_index_day_high_time : $zero),
            "heat_index_month_high_f" => ($type == 'live') ?  $zero : (isset($dat->heat_index_month_high_f) ? $dat->heat_index_month_high_f : $zero),
            "heat_index_year_high_f" => ($type == 'live') ?  $zero : (isset($dat->heat_index_year_high_f) ? $dat->heat_index_year_high_f : $zero),
            "solar_radiation_day_high" => ($type == 'live') ?  $zero : (isset($dat->solar_radiation_day_high) ? $dat->solar_radiation_day_high : $zero),
            "solar_radiation_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->solar_radiation_day_high_time) ? $dat->solar_radiation_day_high_time : $zero),
            "uv_index_day_high" => ($type == 'live') ?  $zero : (isset($dat->uv_index_day_high) ? $dat->uv_index_day_high : $zero),
            "uv_index_day_high_time" => ($type == 'live') ?  $zero : (isset($dat->uv_index_day_high_time) ? $dat->uv_index_day_high_time : $zero),
            "et_month" => ($type == 'live') ?  $zero : (isset($dat->et_month) ? $dat->et_month : $zero),
            "temp_month_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_month_high_f) ? $dat->temp_month_high_f : $zero),
            "temp_month_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_month_low_f) ? $dat->temp_month_low_f : $zero),
            "et_year" => ($type == 'live') ?  $zero : (isset($dat->et_year) ? $dat->et_year : $zero),
            "temp_year_high_f" => ($type == 'live') ?  $zero : (isset($dat->temp_year_high_f) ? $dat->temp_year_high_f : $zero),
            "temp_year_low_f" => ($type == 'live') ?  $zero : (isset($dat->temp_year_low_f) ? $dat->temp_year_low_f : $zero),
            "solar_radiation_month_high" => ($type == 'live') ?  $zero : (isset($dat->solar_radiation_month_high) ? $dat->solar_radiation_month_high : $zero),
            "solar_radiation_year_high" => ($type == 'live') ?  $zero : (isset($dat->solar_radiation_year_high) ? $dat->solar_radiation_year_high : $zero),
            "uv_index_month_high" => ($type == 'live') ?  $zero : (isset($dat->uv_index_month_high) ? $dat->uv_index_month_high : $zero),
            "uv_index_year_high" => ($type == 'live') ?  $zero : (isset($dat->uv_index_year_high) ? $dat->uv_index_year_high : $zero),
            "rain_rate_hour_high_in_per_hr" => ($type == 'live') ? $zero : (isset($dat->rain_rate_hour_high_in_per_hr) ? $dat->rain_rate_hour_high_in_per_hr : $zero),

            //V2
            "time_unix" => ($type == 'live') ? (isset($datas['ts'][0]) ? $datas['ts'][0] : $zero) : $zero,
            "time_zone" => ($type == 'live') ? (isset($livestation['stations'][0]['time_zone']) ? $livestation['stations'][0]['time_zone'] : $zero) : $zero,
            "bar_trend" => ($type == 'live') ? (isset($datas['bar_trend'][0]) ? $datas['bar_trend'][0] : $zero) : $zero,
            "rainfall_last_15_min_mm" => ($type == 'live') ? (isset($datas['rainfall_last_15_min_mm'][0]) ? $datas['rainfall_last_15_min_mm'][0] : $zero) : $zero,
            "rain_rate_hi_last_15_min_mm" => ($type == 'live') ? (isset($datas['rain_rate_hi_last_15_min_mm'][0]) ? $datas['rain_rate_hi_last_15_min_mm'][0] : $zero) : $zero,

            //V1 + V2
            "latitude" => ($type == 'live') ? (isset($livestation['stations'][0]['latitude']) ? $livestation['stations'][0]['latitude'] : $zero) : (isset($datas->latitude) ? $datas->latitude : $zero),
            "longitude" => ($type == 'live') ? (isset($livestation['stations'][0]['longitude']) ? $livestation['stations'][0]['longitude'] : $zero) : (isset($datas->longitude) ? $datas->longitude : $zero),
            "station_name" => ($type == 'live') ? (isset($livestation['stations'][0]['station_name']) ? $livestation['stations'][0]['station_name'] : $zero) : (isset($dat->station_name) ? $dat->station_name : $zero),
            "location" => ($type == 'live') ? (isset($livestation['stations'][0]['city']) ? $livestation['stations'][0]['city'] : $zero) : (isset($datas->location) ? $datas->location : $zero),
            "station_id" => ($type == 'live') ? (isset($livestation['stations'][0]['station_id']) ? $livestation['stations'][0]['station_id'] : $zero) : (isset($datas->station_id) ? $datas->station_id : $zero),

            "pressure_in" => ($type == 'live') ? (isset($datas['bar_absolute'][0]) ? $datas['bar_absolute'][0] : $zero) : (isset($datas->pressure_in) ? $datas->pressure_in : $zero),
            "temp_f" => ($type == 'live') ? (isset($datas['temp'][0]) ? $datas['temp'][0] : $zero) : (isset($datas->temp_f) ? $datas->temp_f : $zero),

            "windchill_f" => ($type == 'live') ? (isset($datas['wind_chill'][0]) ? $datas['wind_chill'][0] : $zero) : (isset($datas->windchill_f) ? $datas->windchill_f : $zero),
            "heat_index_f" => ($type == 'live') ? (isset($datas['heat_index'][0]) ? $datas['heat_index'][0] : $zero) : (isset($datas->heat_index_f) ? $datas->heat_index_f : $zero),
            "dewpoint_f" => ($type == 'live') ? (isset($datas['dew_point'][0]) ? $datas['dew_point'][0] : $zero) : (isset($datas->dewpoint_f) ? $datas->dewpoint_f : $zero),
            "relative_humidity" => ($type == 'live') ? (isset($datas['hum'][0]) ? $datas['hum'][0] : $zero) : (isset($datas->relative_humidity) ? $datas->relative_humidity : $zero),
            "wind_ten_min_avg_mph" => ($type == 'live') ? (isset($datas['wind_speed_avg_last_10_min'][0]) ? $datas['wind_speed_avg_last_10_min'][0] : $zero) : (isset($dat->wind_ten_min_avg_mph) ? $dat->wind_ten_min_avg_mph : $zero),
            "wind_ten_min_gust_mph" => ($type == 'live') ? (isset($datas['wind_speed_hi_last_10_min'][0]) ? $datas['wind_speed_hi_last_10_min'][0] : $zero) : (isset($dat->wind_ten_min_gust_mph) ? $dat->wind_ten_min_gust_mph : $zero),
            "rain_rate_in_per_hr" => ($type == 'live') ? (isset($datas['rain_rate_last_in'][0]) ? $datas['rain_rate_last_in'][0] : $zero) : (isset($dat->rain_rate_in_per_hr) ? $dat->rain_rate_in_per_hr : $zero),
            "rain_day_in" => ($type == 'live') ? (isset($datas['rainfall_last_24_hr_in'][0]) ? $datas['rainfall_last_24_hr_in'][0] : $zero) : (isset($dat->rain_day_in) ? $dat->rain_day_in : $zero),
            "rain_month_in" => ($type == 'live') ? (isset($datas['rainfall_monthly_in'][0]) ? $datas['rainfall_monthly_in'][0] : $zero) : (isset($dat->rain_month_in) ? $dat->rain_month_in : $zero),
            "rain_year_in" => ($type == 'live') ? (isset($datas['rainfall_year_in'][0]) ? $datas['rainfall_year_in'][0] : $zero) : (isset($dat->rain_year_in) ? $dat->rain_year_in : $zero),
            "solar_radiation" => ($type == 'live') ? (isset($datas['solar_rad'][0]) ? $datas['solar_rad'][0] : $zero) : (isset($dat->solar_radiation) ? $dat->solar_radiation : $zero),
            "uv_index" => ($type == 'live') ? (isset($datas['uv_index'][0]) ? $datas['uv_index'][0] : $zero) : (isset($dat->uv_index) ? $dat->uv_index : $zero),
            "temp_in_f" => ($type == 'live') ? (isset($datas['temp_in'][0]) ? $datas['temp_in'][0] : $zero) : (isset($dat->temp_in_f) ? $dat->temp_in_f : $zero),
            "relative_humidity_in" => ($type == 'live') ? (isset($datas['hum_in'][0]) ? $datas['hum_in'][0] : $zero) : (isset($dat->relative_humidity_in) ? $dat->relative_humidity_in : $zero),
            "wind_degrees" => ($type == 'live') ? (isset($datas['wind_dir_last'][0]) ? $datas['wind_dir_last'][0] : $zero) : (isset($datas->wind_degrees) ? $datas->wind_degrees : $zero),
        );

        return $response;
    }

    /**
     * Test datas Json amélioré pour uniformisé Apiv1 et Weatherlink Live
     */
    public function getAPIDatasUp($datas, $station, $livestation)
    {
        $zero = '&#8709;';
        $type = (isset($station['stat_type'])) ? $station['stat_type'] : $zero;

        $timeunix = $this->getAPIDatas($datas, $station, $livestation)['time_unix'];
        $timeRFC822 = $this->getAPIDatas($datas, $station, $livestation)['time_RFC822'];
        $timezone = $this->getAPIDatas($datas, $station, $livestation)['time_zone'];
        $sunset = $this->getAPIDatas($datas, $station, $livestation)['sunset'];
        $sunrise = $this->getAPIDatas($datas, $station, $livestation)['sunrise'];
        $latitude = $this->getAPIDatas($datas, $station, $livestation)['latitude'];
        $longitude = $this->getAPIDatas($datas, $station, $livestation)['longitude'];

        $bartrend = $this->getAPIDatas($datas, $station, $livestation)['bar_trend'];
        $pressurestring = $this->getAPIDatas($datas, $station, $livestation)['pressure_tendency_string'];
        $pressure_mb = $this->getAPIDatas($datas, $station, $livestation)['pressure_mb'];
        $temp_f = $this->getAPIDatas($datas, $station, $livestation)['temp_f'];
        $temp_c = $this->getAPIDatas($datas, $station, $livestation)['temp_c'];



        $data = array(
            "time" => ($type == 'live') ? $this->liveDateRFC822($timeunix, $timezone) : $timeRFC822,
            "pressure_tendency" => ($type == 'live') ? $this->livePressTrend($bartrend) : $pressurestring,
            "fuseau" => ($type == 'live') ? $timezone : $this->timeZone($timeRFC822),
            "time_sunset" => ($type == 'live') ? $this->liveDateSun($timeunix, $latitude, $longitude, $timezone, 'sunset') : $sunset,
            "time_sunrise" => ($type == 'live') ? $this->liveDateSun($timeunix, $latitude, $longitude, $timezone, 'sunrise') : $sunrise,
            "mb_pressure" => ($type == 'live') ? $this->livePress($pressure_mb) : $pressure_mb,
            "c_temp" => ($type == 'live') ?  $this->getTempFtoC($temp_f)  : $temp_c,

        );
        return $data;
    }

    /**
     * TITRES CASES POUR SELECT PREF
     */

    public function tabTxt($config)
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
                "txt" => $this->SunTxt($config),
                "text" => $this->SunText($config)
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
            )
        );

        return $tab_txt;
    }

    /**
     * TITRES CASE POUR HOME version 1
     */
    public function incUp1($datas, $switch, $config, $info, $livestation)
    {

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
                "ICON_TOOLTIP" => ($this->is_Temp('59', $this->getAPIDatas($datas, $info, $livestation)['temp_f']) == true) ? $this->getIconTooltip($switch, '\'<i class="wi wi-thermometer-exterior"></i>\'', $this->l->trad('WINDCHILL_SMALL'), '') : $this->getIconTooltip($switch, '\'<i class="wi wi-thermometer-exterior"></i>\'', $this->l->trad('HEAT'), ''),
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
                "H2_TXT" => '<img class="arrowpress" alt="arrow"  src="' . $this->pressImg($this->getAPIDatasUp($datas, $info, $livestation)['pressure_tendency'])  . '" />',
                "H2_TEXT" => $this->l->trad('PRESSURE'),
                "ICON" => $this->getIcon($switch, '<i class="wi wi-barometer"></i>'),
                "ICON_TOOLTIP" =>  $this->getIconTooltip($switch, '<i class="wi wi-barometer"></i>', $this->l->pressTrad($this->getAPIDatasUp($datas, $info, $livestation)['pressure_tendency'], $this->l->getLg()), ' <img class="arrowpress" alt="arrow"  src="' . $this->pressImg($this->getAPIDatasUp($datas, $info, $livestation)['pressure_tendency'])  . '" />'),
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
        );

        return $inc;
    }

    /**
     * MILIEU CASE POUR HOME version 1
     */
    public function incMid1($datas, $switch, $config, $info, $livestation)
    {
        $temp_f = $this->getAPIDatas($datas, $info, $livestation)['temp_f'];
        $windchill_f = $this->getAPIDatas($datas, $info, $livestation)['windchill_f'];

        $heat_index_f  = $this->getAPIDatas($datas, $info, $livestation)['heat_index_f'];
        $dewpoint_f  = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_f'];
        $relative_humidity  = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity'];

        $wind_ten_min_avg_mph = $this->getAPIDatas($datas, $info, $livestation)['wind_ten_min_avg_mph'];
        $wind_ten_min_gust_mph = $this->getAPIDatas($datas, $info, $livestation)['wind_ten_min_gust_mph'];
        $wind_day_high_mph = $this->getAPIDatas($datas, $info, $livestation)['wind_day_high_mph'];

        $rain_rate_in_per_hr = $this->getAPIDatas($datas, $info, $livestation)['rain_rate_in_per_hr'];
        $rain_day_in = $this->getAPIDatas($datas, $info, $livestation)['rain_day_in'];
        $rain_month_in = $this->getAPIDatas($datas, $info, $livestation)['rain_month_in'];
        $rain_year_in = $this->getAPIDatas($datas, $info, $livestation)['rain_year_in'];

        $et_day = $this->getAPIDatas($datas, $info, $livestation)['et_day'];

        $temp_day_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_day_high_f'];
        $temp_day_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_day_low_f'];

        $temp_extra_1 = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1'];
        $temp_extra_2 = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2'];
        $temp_extra_3 = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3'];
        $temp_extra_4 = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4'];
        $temp_extra_5 = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5'];
        $temp_extra_6 = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6'];
        $temp_extra_7 = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7'];

        $temp_leaf_1 = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1'];
        $temp_leaf_2 = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2'];

        $temp_soil_1 = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1'];
        $temp_soil_2 = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2'];
        $temp_soil_3 = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3'];
        $temp_soil_4 = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4'];

        $relative_humidity_1 = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1'];
        $relative_humidity_2 = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2'];
        $relative_humidity_3 = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3'];
        $relative_humidity_4 = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4'];
        $relative_humidity_5 = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5'];
        $relative_humidity_6 = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6'];
        $relative_humidity_7 = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7'];
        $leaf_wetness_1 = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1'];
        $leaf_wetness_2 = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2'];

        $soil_moisture_1 = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1'];
        $soil_moisture_2 = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2'];
        $soil_moisture_3 = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3'];
        $soil_moisture_4 = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4'];

        $temp_in_f = $this->getAPIDatas($datas, $info, $livestation)['temp_in_f'];
        $relative_humidity_in = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in'];

        $inc = array(
            "0" => array(
                "_VALUE_MAIN" => '',
                "_UNIT" => '',
                "_CLASS_UNIT_SMALL" => '',
                "_CLASS_UNIT_LARGE" => '',
                "color" => ''
            ),
            "1" => array(
                "_VALUE_MAIN" => $this->getWind($switch, $wind_ten_min_avg_mph),
                "_UNIT" => $this->getUnit($switch, 'wind'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colWind($switch, $wind_ten_min_avg_mph, $datas, $info, $livestation)
            ),
            "2" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_f),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_f, $datas, $info, $livestation)
            ),
            "3" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $rain_rate_in_per_hr),
                "_UNIT" => $this->getUnit($switch, 'rain') . '/h',
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $rain_rate_in_per_hr, $datas, $info, $livestation)
            ),
            "4" => array(
                "_VALUE_MAIN" => $this->getWind($switch, $wind_ten_min_gust_mph),
                "_UNIT" => $this->getUnit($switch, 'wind'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colWind($switch, $wind_ten_min_gust_mph, $datas, $info, $livestation)
            ),
            "5" => array(
                "_VALUE_MAIN" => ($this->is_Temp('59', $temp_f) == true) ? $this->getTemp($switch, $windchill_f) : $this->getTemp($switch, $heat_index_f),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => ($this->is_Temp('59', $temp_f) == true) ? $this->col->colWindchill($switch, $windchill_f, $datas, $info, $livestation) : $this->col->colHeat($switch, $heat_index_f, $datas, $info, $livestation)
            ),
            "6" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $rain_day_in),
                "_UNIT" => $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $rain_day_in, $datas, $info, $livestation)
            ),
            "7" => array(
                "_VALUE_MAIN" => $this->getWind($switch, $wind_day_high_mph),
                "_UNIT" => $this->getUnit($switch, 'wind'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colWind($switch, $wind_day_high_mph, $datas, $info, $livestation)
            ),
            "9" => array(
                "_VALUE_MAIN" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->getRain($switch, $et_day) : $this->getRain($switch, $this->ETR_in($temp_day_high_f, $temp_day_low_f, $rain_day_in)),
                "_UNIT" =>  $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->col->colRain($switch, $et_day, $datas, $info, $livestation) : $this->col->colRain($switch, $this->ETR_in($temp_day_high_f, $temp_day_low_f, $rain_day_in), $datas, $info, $livestation)
            ),
            "11" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $dewpoint_f),
                "_UNIT" =>  $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $dewpoint_f, $datas, $info, $livestation)
            ),
            "12" => array(
                "_VALUE_MAIN" => $relative_humidity,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity, $datas, $info, $livestation)
            ),
            "13" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $rain_month_in),
                "_UNIT" => $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $rain_month_in, $datas, $info, $livestation)
            ),
            "14" => array(
                "_VALUE_MAIN" => $this->getRain($switch, $rain_year_in),
                "_UNIT" => $this->getUnit($switch, 'rain'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colRain($switch, $rain_year_in, $datas, $info, $livestation)
            ),
            "15" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_extra_1),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_extra_1, $datas, $info, $livestation)
            ),
            "16" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_extra_2),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_extra_2, $datas, $info, $livestation)
            ),
            "17" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_extra_3),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_extra_3, $datas, $info, $livestation)
            ),
            "18" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_extra_4),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_extra_4, $datas, $info, $livestation)
            ),
            "19" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_extra_5),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_extra_5, $datas, $info, $livestation)
            ),
            "20" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_extra_6),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_extra_6, $datas, $info, $livestation)
            ),
            "21" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_extra_7),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_extra_7, $datas, $info, $livestation)
            ),
            "24" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_leaf_1),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_leaf_1, $datas, $info, $livestation)
            ),
            "25" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_leaf_2),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_leaf_2, $datas, $info, $livestation)
            ),
            "26" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_soil_1),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_soil_1, $datas, $info, $livestation)
            ),
            "27" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_soil_2),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_soil_2, $datas, $info, $livestation)
            ),
            "28" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_soil_3),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_soil_3, $datas, $info, $livestation)
            ),
            "29" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_soil_4),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_soil_4, $datas, $info, $livestation)
            ),
            "30" => array(
                "_VALUE_MAIN" => $relative_humidity_1,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_1, $datas, $info, $livestation)
            ),
            "31" => array(
                "_VALUE_MAIN" => $relative_humidity_2,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_2, $datas, $info, $livestation)
            ),
            "32" => array(
                "_VALUE_MAIN" => $relative_humidity_3,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_3, $datas, $info, $livestation)
            ),
            "33" => array(
                "_VALUE_MAIN" => $relative_humidity_4,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_4, $datas, $info, $livestation)
            ),
            "34" => array(
                "_VALUE_MAIN" => $relative_humidity_5,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_5, $datas, $info, $livestation)
            ),
            "35" => array(
                "_VALUE_MAIN" => $relative_humidity_6,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_6, $datas, $info, $livestation)
            ),
            "36" => array(
                "_VALUE_MAIN" => $relative_humidity_7,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_7, $datas, $info, $livestation)
            ),
            "37" => array(
                "_VALUE_MAIN" => $leaf_wetness_1,
                "_UNIT" =>  '',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colLeaf($switch, $leaf_wetness_1, $datas, $info, $livestation)
            ),
            "38" => array(
                "_VALUE_MAIN" => $leaf_wetness_2,
                "_UNIT" =>  '',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colLeaf($switch, $leaf_wetness_2, $datas, $info, $livestation)
            ),
            "39" => array(
                "_VALUE_MAIN" => $soil_moisture_1,
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $soil_moisture_1, $datas, $info, $livestation)
            ),
            "40" => array(
                "_VALUE_MAIN" => $soil_moisture_2,
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $soil_moisture_2, $datas, $info, $livestation)
            ),
            "41" => array(
                "_VALUE_MAIN" => $soil_moisture_3,
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $soil_moisture_3, $datas, $info, $livestation)
            ),
            "42" => array(
                "_VALUE_MAIN" => $soil_moisture_4,
                "_UNIT" =>  'cB',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colSoil($switch, $soil_moisture_4, $datas, $info, $livestation)
            ),
            "43" => array(
                "_VALUE_MAIN" => $this->getTemp($switch, $temp_in_f),
                "_UNIT" => $this->getUnit($switch, 'temp'),
                "_CLASS_UNIT_SMALL" => '08',
                "_CLASS_UNIT_LARGE" => '09',
                "color" => $this->col->colTemp($switch, $temp_in_f, $datas, $info, $livestation)
            ),
            "44" => array(
                "_VALUE_MAIN" => $relative_humidity_in,
                "_UNIT" =>  '%',
                "_CLASS_UNIT_SMALL" => '06',
                "_CLASS_UNIT_LARGE" => '08',
                "color" => $this->col->colHumidity($switch, $relative_humidity_in, $datas, $info, $livestation)
            ),
        );

        return $inc;
    }

    /**
     * MILIEU CASE POUR HOME version 2
     */
    public function incMid2($datas, $switch, $info, $livestation)
    {
        $pressure_in = $this->getAPIDatas($datas, $info, $livestation)['pressure_in'];
        $pressure_mb = $this->getAPIDatasUp($datas, $info, $livestation)['mb_pressure'];

        $inc = array(
            "10" => array(
                "_VALUE_MAIN" => $this->getPress($switch, $pressure_in),
                "TEXT_TOOLTIP_S" => $this->l->trad('PRESSURE'),
                "TEXT_TOOLTIP_M" => $this->l->trad('PRESSURE'),
                "TEXT_TOOLTIP_L" => $this->l->pressTrad($this->getAPIDatasUp($datas, $info, $livestation)['pressure_tendency'], $this->l->getLg()),
                "_UNIT_S" => '',
                "_UNIT_M" =>  $this->getUnit($switch, 'press'),
                "_UNIT_L" =>  $this->getUnit($switch, 'press'),
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_MIDDLE" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "TXT_ALTERN" => '&nbsp;<img class="arrowpress2" alt="arrow" src="' . $this->pressImg($this->getAPIDatasUp($datas, $info, $livestation)['pressure_tendency'])  . '" />',
                "color" => $this->col->colPress($switch, $pressure_mb, $datas, $info, $livestation)
            ),
        );

        return $inc;
    }

    /**
     * MILIEU CASE POUR HOME version 3
     */
    public function incMid3($datas, $switch, $info, $livestation)
    {
        $solar_radiation = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation'];
        $solar_radiation_day_high = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation_day_high'];
        $solar_radiation_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation_day_high_time'];

        $uv_index = $this->getAPIDatas($datas, $info, $livestation)['uv_index'];
        $uv_index_day_high = $this->getAPIDatas($datas, $info, $livestation)['uv_index_day_high'];
        $uv_index_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['uv_index_day_high_time'];

        $inc = array(
            "22" => array(
                "_VALUE_MAIN" => $solar_radiation,
                "TEXT_TOOLTIP_S" => $this->l->trad('MAX') . ' : ' . $solar_radiation_day_high . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($solar_radiation_day_high_time, $this->l->getLg()),
                "TEXT_TOOLTIP_L" => $this->l->trad('MAX') . ' : ' . $solar_radiation_day_high . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($solar_radiation_day_high_time, $this->l->getLg()),
                "_UNIT_S" => 'W/m²',
                "_UNIT_L" =>  '&nbsp;W/m²',
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colSun($switch, $solar_radiation, $datas, $info, $livestation)
            ),
            "23" => array(
                "_VALUE_MAIN" => $uv_index,
                "TEXT_TOOLTIP_S" => $this->l->trad('MAX') . ' : ' . $uv_index_day_high . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($uv_index_day_high_time, $this->l->getLg()),
                "TEXT_TOOLTIP_L" => $this->l->trad('MAX') . ' : ' . $uv_index_day_high . ' W/m² ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($uv_index_day_high_time, $this->l->getLg()),
                "_UNIT_S" => '',
                "_UNIT_L" =>  '&nbsp;/16',
                "_CLASS_UNIT_SMALL" => '05',
                "_CLASS_UNIT_LARGE" => '06',
                "color" => $this->col->colUV($switch, $uv_index, $datas)
            ),
        );

        return $inc;
    }

    /**
     * BAS CASE POUR HOME version 1
     */
    public function incDown1($datas, $info, $livestation)
    {


        $wind_degrees = $this->getAPIDatas($datas, $info, $livestation)['wind_degrees'];
        $wind_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['wind_day_high_time'];

        $inc = array(
            "0" => array(
                "CSS_DOWN" => '',
                "_VALUE_DOWN_S" =>  '',
                "_VALUE_DOWN_L" => ''
            ),
            "1" => array(
                "CSS_DOWN" => '500',
                "_VALUE_DOWN_S" =>  $this->l->degToCompassSmall($wind_degrees, $this->l->getLg()),
                "_VALUE_DOWN_L" => $this->l->degToCompass($wind_degrees, $this->l->getLg())
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
                "_VALUE_DOWN_S" => $this->l->trad('AT') . ' ' . $this->l->timeTrad($wind_day_high_time, $this->l->getLg()),
                "_VALUE_DOWN_L" => $this->l->trad('TODAY') . ' ' . $this->l->trad('AT') . ' ' . $this->l->timeTrad($wind_day_high_time, $this->l->getLg())
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
        );

        return $inc;
    }



    /**
     * BAS CASE POUR HOME version 2
     */

    public function incDown2($datas, $switch, $info, $livestation)
    {
        $temp_day_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_day_low_f'];
        $temp_month_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_month_low_f'];
        $temp_year_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_year_low_f'];
        $temp_day_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_day_high_f'];
        $temp_month_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_month_high_f'];
        $temp_year_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_year_high_f'];

        $temp_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_day_low_time'];
        $temp_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_day_high_time'];

        $pressure_day_low_in = $this->getAPIDatas($datas, $info, $livestation)['pressure_day_low_in'];
        $pressure_month_low_in = $this->getAPIDatas($datas, $info, $livestation)['pressure_month_low_in'];
        $pressure_year_low_in = $this->getAPIDatas($datas, $info, $livestation)['pressure_year_low_in'];
        $pressure_day_high_in = $this->getAPIDatas($datas, $info, $livestation)['pressure_day_high_in'];
        $pressure_month_high_in = $this->getAPIDatas($datas, $info, $livestation)['pressure_month_high_in'];
        $pressure_year_high_in = $this->getAPIDatas($datas, $info, $livestation)['pressure_year_high_in'];

        $pressure_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['pressure_day_low_time'];
        $pressure_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['pressure_day_high_time'];

        $dewpoint_day_low_f = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_day_low_f'];
        $dewpoint_month_low_f = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_month_low_f'];
        $dewpoint_year_low_f = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_year_low_f'];
        $dewpoint_day_high_f = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_day_high_f'];
        $dewpoint_month_high_f = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_month_high_f'];
        $dewpoint_year_high_f = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_year_high_f'];

        $dewpoint_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_day_low_time'];
        $dewpoint_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['dewpoint_day_high_time'];

        $relative_humidity_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_day_low'];
        $relative_humidity_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_month_low'];
        $relative_humidity_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_year_low'];
        $relative_humidity_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_day_high'];
        $relative_humidity_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_month_high'];
        $relative_humidity_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_year_high'];

        $relative_humidity_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_day_low_time'];
        $relative_humidity_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_day_high_time'];

        $temp_extra_1_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_day_low'];
        $temp_extra_1_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_month_low'];
        $temp_extra_1_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_year_low'];
        $temp_extra_1_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_day_high'];
        $temp_extra_1_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_month_high'];
        $temp_extra_1_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_year_high'];

        $temp_extra_1_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_day_low_time'];
        $temp_extra_1_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_1_day_high_time'];

        $temp_extra_2_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_day_low'];
        $temp_extra_2_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_month_low'];
        $temp_extra_2_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_year_low'];
        $temp_extra_2_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_day_high'];
        $temp_extra_2_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_month_high'];
        $temp_extra_2_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_year_high'];

        $temp_extra_2_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_day_low_time'];
        $temp_extra_2_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_2_day_high_time'];

        $temp_extra_3_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_day_low'];
        $temp_extra_3_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_month_low'];
        $temp_extra_3_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_year_low'];
        $temp_extra_3_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_day_high'];
        $temp_extra_3_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_month_high'];
        $temp_extra_3_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_year_high'];

        $temp_extra_3_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_day_low_time'];
        $temp_extra_3_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_3_day_high_time'];

        $temp_extra_4_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_day_low'];
        $temp_extra_4_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_month_low'];
        $temp_extra_4_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_year_low'];
        $temp_extra_4_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_day_high'];
        $temp_extra_4_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_month_high'];
        $temp_extra_4_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_year_high'];

        $temp_extra_4_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_day_low_time'];
        $temp_extra_4_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_4_day_high_time'];

        $temp_extra_5_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_day_low'];
        $temp_extra_5_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_month_low'];
        $temp_extra_5_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_year_low'];
        $temp_extra_5_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_day_high'];
        $temp_extra_5_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_month_high'];
        $temp_extra_5_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_year_high'];

        $temp_extra_5_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_day_low_time'];
        $temp_extra_5_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_5_day_high_time'];

        $temp_extra_6_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_day_low'];
        $temp_extra_6_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_month_low'];
        $temp_extra_6_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_year_low'];
        $temp_extra_6_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_day_high'];
        $temp_extra_6_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_month_high'];
        $temp_extra_6_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_year_high'];

        $temp_extra_6_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_day_low_time'];
        $temp_extra_6_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_6_day_high_time'];

        $temp_extra_7_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_day_low'];
        $temp_extra_7_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_month_low'];
        $temp_extra_7_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_year_low'];
        $temp_extra_7_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_day_high'];
        $temp_extra_7_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_month_high'];
        $temp_extra_7_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_year_high'];

        $temp_extra_7_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_day_low_time'];
        $temp_extra_7_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_extra_7_day_high_time'];

        $temp_leaf_1_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_day_low'];
        $temp_leaf_1_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_month_low'];
        $temp_leaf_1_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_year_low'];
        $temp_leaf_1_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_day_high'];
        $temp_leaf_1_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_month_high'];
        $temp_leaf_1_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_year_high'];

        $temp_leaf_1_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_day_low_time'];
        $temp_leaf_1_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_1_day_high_time'];

        $temp_leaf_2_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_day_low'];
        $temp_leaf_2_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_month_low'];
        $temp_leaf_2_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_year_low'];
        $temp_leaf_2_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_day_high'];
        $temp_leaf_2_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_month_high'];
        $temp_leaf_2_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_year_high'];

        $temp_leaf_2_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_day_low_time'];
        $temp_leaf_2_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_leaf_2_day_high_time'];

        $temp_soil_1_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_day_low'];
        $temp_soil_1_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_month_low'];
        $temp_soil_1_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_year_low'];
        $temp_soil_1_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_day_high'];
        $temp_soil_1_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_month_high'];
        $temp_soil_1_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_year_high'];

        $temp_soil_1_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_day_low_time'];
        $temp_soil_1_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_1_day_high_time'];

        $temp_soil_2_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_day_low'];
        $temp_soil_2_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_month_low'];
        $temp_soil_2_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_year_low'];
        $temp_soil_2_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_day_high'];
        $temp_soil_2_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_month_high'];
        $temp_soil_2_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_year_high'];

        $temp_soil_2_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_day_low_time'];
        $temp_soil_2_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_2_day_high_time'];

        $temp_soil_3_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_day_low'];
        $temp_soil_3_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_month_low'];
        $temp_soil_3_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_year_low'];
        $temp_soil_3_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_day_high'];
        $temp_soil_3_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_month_high'];
        $temp_soil_3_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_year_high'];

        $temp_soil_3_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_day_low_time'];
        $temp_soil_3_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_3_day_high_time'];

        $temp_soil_4_day_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_day_low'];
        $temp_soil_4_month_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_month_low'];
        $temp_soil_4_year_low = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_year_low'];
        $temp_soil_4_day_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_day_high'];
        $temp_soil_4_month_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_month_high'];
        $temp_soil_4_year_high = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_year_high'];

        $temp_soil_4_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_day_low_time'];
        $temp_soil_4_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_soil_4_day_high_time'];


        $relative_humidity_1_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_day_low'];
        $relative_humidity_1_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_month_low'];
        $relative_humidity_1_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_year_low'];
        $relative_humidity_1_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_day_high'];
        $relative_humidity_1_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_month_high'];
        $relative_humidity_1_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_year_high'];

        $relative_humidity_1_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_day_low_time'];
        $relative_humidity_1_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_1_day_high_time'];

        $relative_humidity_2_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_day_low'];
        $relative_humidity_2_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_month_low'];
        $relative_humidity_2_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_year_low'];
        $relative_humidity_2_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_day_high'];
        $relative_humidity_2_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_month_high'];
        $relative_humidity_2_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_year_high'];

        $relative_humidity_2_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_day_low_time'];
        $relative_humidity_2_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_2_day_high_time'];

        $relative_humidity_3_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_day_low'];
        $relative_humidity_3_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_month_low'];
        $relative_humidity_3_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_year_low'];
        $relative_humidity_3_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_day_high'];
        $relative_humidity_3_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_month_high'];
        $relative_humidity_3_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_year_high'];

        $relative_humidity_3_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_day_low_time'];
        $relative_humidity_3_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_3_day_high_time'];

        $relative_humidity_4_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_day_low'];
        $relative_humidity_4_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_month_low'];
        $relative_humidity_4_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_year_low'];
        $relative_humidity_4_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_day_high'];
        $relative_humidity_4_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_month_high'];
        $relative_humidity_4_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_year_high'];

        $relative_humidity_4_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_day_low_time'];
        $relative_humidity_4_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_4_day_high_time'];

        $relative_humidity_5_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_day_low'];
        $relative_humidity_5_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_month_low'];
        $relative_humidity_5_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_year_low'];
        $relative_humidity_5_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_day_high'];
        $relative_humidity_5_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_month_high'];
        $relative_humidity_5_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_year_high'];

        $relative_humidity_5_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_day_low_time'];
        $relative_humidity_5_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_5_day_high_time'];

        $relative_humidity_6_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_day_low'];
        $relative_humidity_6_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_month_low'];
        $relative_humidity_6_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_year_low'];
        $relative_humidity_6_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_day_high'];
        $relative_humidity_6_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_month_high'];
        $relative_humidity_6_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_year_high'];

        $relative_humidity_6_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_day_low_time'];
        $relative_humidity_6_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_6_day_high_time'];

        $relative_humidity_7_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_day_low'];
        $relative_humidity_7_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_month_low'];
        $relative_humidity_7_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_year_low'];
        $relative_humidity_7_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_day_high'];
        $relative_humidity_7_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_month_high'];
        $relative_humidity_7_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_year_high'];

        $relative_humidity_7_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_day_low_time'];
        $relative_humidity_7_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_7_day_high_time'];

        $leaf_wetness_1_day_low = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_day_low'];
        $leaf_wetness_1_month_low = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_month_low'];
        $leaf_wetness_1_year_low = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_year_low'];
        $leaf_wetness_1_day_high = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_day_high'];
        $leaf_wetness_1_month_high = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_month_high'];
        $leaf_wetness_1_year_high = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_year_high'];

        $leaf_wetness_1_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_day_low_time'];
        $leaf_wetness_1_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_1_day_high_time'];

        $leaf_wetness_2_day_low = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_day_low'];
        $leaf_wetness_2_month_low = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_month_low'];
        $leaf_wetness_2_year_low = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_year_low'];
        $leaf_wetness_2_day_high = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_day_high'];
        $leaf_wetness_2_month_high = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_month_high'];
        $leaf_wetness_2_year_high = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_year_high'];

        $leaf_wetness_2_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_day_low_time'];
        $leaf_wetness_2_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['leaf_wetness_2_day_high_time'];

        $soil_moisture_1_day_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_day_low'];
        $soil_moisture_1_month_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_month_low'];
        $soil_moisture_1_year_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_year_low'];
        $soil_moisture_1_day_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_day_high'];
        $soil_moisture_1_month_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_month_high'];
        $soil_moisture_1_year_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_year_high'];

        $soil_moisture_1_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_day_low_time'];
        $soil_moisture_1_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_1_day_high_time'];

        $soil_moisture_2_day_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_day_low'];
        $soil_moisture_2_month_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_month_low'];
        $soil_moisture_2_year_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_year_low'];
        $soil_moisture_2_day_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_day_high'];
        $soil_moisture_2_month_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_month_high'];
        $soil_moisture_2_year_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_year_high'];

        $soil_moisture_2_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_day_low_time'];
        $soil_moisture_2_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_2_day_high_time'];

        $soil_moisture_3_day_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_day_low'];
        $soil_moisture_3_month_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_month_low'];
        $soil_moisture_3_year_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_year_low'];
        $soil_moisture_3_day_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_day_high'];
        $soil_moisture_3_month_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_month_high'];
        $soil_moisture_3_year_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_year_high'];

        $soil_moisture_3_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_day_low_time'];
        $soil_moisture_3_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_3_day_high_time'];

        $soil_moisture_4_day_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_day_low'];
        $soil_moisture_4_month_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_month_low'];
        $soil_moisture_4_year_low = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_year_low'];
        $soil_moisture_4_day_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_day_high'];
        $soil_moisture_4_month_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_month_high'];
        $soil_moisture_4_year_high = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_year_high'];

        $soil_moisture_4_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_day_low_time'];
        $soil_moisture_4_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['soil_moisture_4_day_high_time'];

        $temp_in_day_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_in_day_low_f'];
        $temp_in_month_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_in_month_low_f'];
        $temp_in_year_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_in_year_low_f'];
        $temp_in_day_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_in_day_high_f'];
        $temp_in_month_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_in_month_high_f'];
        $temp_in_year_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_in_year_high_f'];

        $temp_in_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['temp_in_day_low_time'];
        $temp_in_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['temp_in_day_high_time'];

        $relative_humidity_in_day_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_day_low'];
        $relative_humidity_in_month_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_month_low'];
        $relative_humidity_in_year_low = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_year_low'];
        $relative_humidity_in_day_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_day_high'];
        $relative_humidity_in_month_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_month_high'];
        $relative_humidity_in_year_high = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_year_high'];

        $relative_humidity_in_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_day_low_time'];
        $relative_humidity_in_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity_in_day_high_time'];

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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_day_low_f), $this->getTemp($switch, $temp_month_low_f), $this->getTemp($switch, $temp_year_low_f)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_day_high_f), $this->getTemp($switch, $temp_month_high_f), $this->getTemp($switch, $temp_year_high_f)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getPress($switch, $pressure_day_low_in), $this->getPress($switch, $pressure_month_low_in), $this->getPress($switch, $pressure_year_low_in)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getPress($switch, $pressure_day_high_in), $this->getPress($switch, $pressure_month_high_in), $this->getPress($switch, $pressure_year_high_in)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($pressure_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($pressure_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $dewpoint_day_low_f), $this->getTemp($switch, $dewpoint_month_low_f), $this->getTemp($switch, $dewpoint_year_low_f)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $dewpoint_day_high_f), $this->getTemp($switch, $dewpoint_month_high_f), $this->getTemp($switch, $dewpoint_year_high_f)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($dewpoint_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($dewpoint_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $relative_humidity_day_low, $relative_humidity_month_low, $relative_humidity_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $relative_humidity_day_high, $relative_humidity_month_high, $relative_humidity_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($relative_humidity_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_1_day_low), $this->getTemp($switch, $temp_extra_1_month_low), $this->getTemp($switch, $temp_extra_1_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_1_day_high), $this->getTemp($switch, $temp_extra_1_month_high), $this->getTemp($switch, $temp_extra_1_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_1_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_1_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_2_day_low), $this->getTemp($switch, $temp_extra_2_month_low), $this->getTemp($switch, $temp_extra_2_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_2_day_high), $this->getTemp($switch,  $temp_extra_2_month_high), $this->getTemp($switch, $temp_extra_2_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_2_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_2_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_3_day_low), $this->getTemp($switch, $temp_extra_3_month_low), $this->getTemp($switch, $temp_extra_3_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_3_day_high), $this->getTemp($switch, $temp_extra_3_month_high), $this->getTemp($switch, $temp_extra_3_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_3_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_3_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_4_day_low), $this->getTemp($switch, $temp_extra_4_month_low), $this->getTemp($switch, $temp_extra_4_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_4_day_high), $this->getTemp($switch,  $temp_extra_4_month_high), $this->getTemp($switch, $temp_extra_4_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_4_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_4_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_5_day_low), $this->getTemp($switch, $temp_extra_5_month_low), $this->getTemp($switch, $temp_extra_5_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch,  $temp_extra_5_day_high), $this->getTemp($switch, $temp_extra_5_month_high), $this->getTemp($switch, $temp_extra_5_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_5_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_5_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_6_day_low), $this->getTemp($switch, $temp_extra_6_month_low), $this->getTemp($switch, $temp_extra_6_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_6_day_high), $this->getTemp($switch, $temp_extra_6_month_high), $this->getTemp($switch, $temp_extra_6_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_6_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_6_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_7_day_low), $this->getTemp($switch, $temp_extra_7_month_low), $this->getTemp($switch, $temp_extra_7_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_extra_7_day_high), $this->getTemp($switch, $temp_extra_7_month_high), $this->getTemp($switch, $temp_extra_7_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_7_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_extra_7_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_leaf_1_day_low), $this->getTemp($switch, $temp_leaf_1_month_low), $this->getTemp($switch, $temp_leaf_1_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_leaf_1_day_high), $this->getTemp($switch, $temp_leaf_1_month_high), $this->getTemp($switch, $temp_leaf_1_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_leaf_1_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_leaf_1_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $this->getTemp($switch, $temp_leaf_2_day_low), $this->getTemp($switch, $temp_leaf_2_month_low), $this->getTemp($switch, $temp_leaf_2_year_low)),
                "_DMY_VALUE_x" => $this->getDMY($switch, $this->getTemp($switch, $temp_leaf_2_day_high), $this->getTemp($switch, $temp_leaf_2_month_high), $this->getTemp($switch, $temp_leaf_2_year_high)),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_leaf_2_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($temp_leaf_2_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $leaf_wetness_1_day_low, $leaf_wetness_1_month_low, $leaf_wetness_1_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $leaf_wetness_1_day_high, $leaf_wetness_1_month_high, $leaf_wetness_1_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($leaf_wetness_1_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($leaf_wetness_1_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
                "_DMY_VALUE_n" => $this->getDMY($switch, $leaf_wetness_2_day_low, $leaf_wetness_2_month_low, $leaf_wetness_2_year_low),
                "_DMY_VALUE_x" => $this->getDMY($switch, $leaf_wetness_2_day_high, $leaf_wetness_2_month_high, $leaf_wetness_2_year_high),
                "DMY_OF_DOWN_n" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($leaf_wetness_2_day_low_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "DMY_OF_DOWN_x" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($leaf_wetness_2_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
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
        );

        return $inc;
    }

    /**
     * BAS CASE POUR HOME version 3
     */
    public function incDown3($datas, $switch, $info, $livestation)
    {
        $wind_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['wind_day_high_time'];
        $wind_day_high_mph = $this->getAPIDatas($datas, $info, $livestation)['wind_day_high_mph'];
        $wind_month_high_mph = $this->getAPIDatas($datas, $info, $livestation)['wind_month_high_mph'];
        $wind_year_high_mph = $this->getAPIDatas($datas, $info, $livestation)['wind_year_high_mph'];

        $inc = array(
            "4" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL" => $this->getDMY($switch, $this->l->trad('TODAY'), $this->l->trad('MAX_GUST'), $this->l->trad('MAX_GUST')),
                "TEXT_DOWN_LARGE" => $this->getDMY($switch, $this->l->trad('TODAY'), $this->l->trad('MAX_GUST'), $this->l->trad('MAX_GUST')),
                "DMY_OF_DOWN" =>  $this->getDMY($switch, $this->l->trad('AT') . ' ' . $this->l->timeTrad($wind_day_high_time, $this->l->getLg()), $this->l->trad('OF_THE_MONTH'), $this->l->trad('OF_THE_YEAR')),
                "ALTERN_TXT_S_1" => $this->l->trad('MAX') . ' : ',
                "ALTERN_TXT_S_2" => '',
                "ALTERN_TXT_S_3" => '',
                "ALTERN_TXT_L_1" => $this->l->trad('MAX_GUST') . ' : ',
                "ALTERN_TXT_L_2" => '',
                "ALTERN_TXT_L_3" => '',
                "_DMY_VALUE" => $this->getDMY($switch, $this->getWind($switch, $wind_day_high_mph), $this->getWind($switch, $wind_month_high_mph), $this->getWind($switch, $wind_year_high_mph)),
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
    public function incDown5($datas, $switch, $config, $info, $livestation)
    {
        $rain_month_in = $this->getAPIDatas($datas, $info, $livestation)['rain_month_in'];
        $rain_year_in = $this->getAPIDatas($datas, $info, $livestation)['rain_year_in'];

        $et_month = $this->getAPIDatas($datas, $info, $livestation)['et_month'];
        $temp_month_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_month_high_f'];
        $temp_month_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_month_low_f'];

        $et_year = $this->getAPIDatas($datas, $info, $livestation)['et_year'];
        $temp_year_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_year_high_f'];
        $temp_year_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_year_low_f'];

        $solar_radiation_month_high = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation_month_high'];
        $solar_radiation_year_high = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation_year_high'];

        $uv_index_month_high = $this->getAPIDatas($datas, $info, $livestation)['uv_index_month_high'];
        $uv_index_year_high = $this->getAPIDatas($datas, $info, $livestation)['uv_index_year_high'];

        $inc = array(
            "6" => array(
                "CSS_DOWN" => '500',
                "TEXT_DOWN_SMALL_n" => $this->l->trad('MONTH_PRECIP'),
                "ALTERN_TXT_S_1n" => '',
                "_VALUE_n" =>  $this->getRain($switch, $rain_month_in),
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
                "_VALUE_x" =>  $this->getRain($switch, $rain_year_in),
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
                "_VALUE_n" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->getRain($switch, $et_month) : $this->getRain($switch, $this->ETR_in($temp_month_high_f, $temp_month_low_f, $rain_month_in)),
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
                "_VALUE_x" => ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') ? $this->getRain($switch, $et_year) : $this->getRain($switch, $this->ETR_in($temp_year_high_f, $temp_year_low_f, $rain_year_in)),
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
    public function optionValue($config)
    {
        $optionValue = array(
            "2" => $this->tabTxt($config)['2'],
            "5" => $this->tabTxt($config)['5'],
            "8" => $this->tabTxt($config)['8'],
            "11" => $this->tabTxt($config)['11'],
            "10" => $this->tabTxt($config)['10'],
            "12" => $this->tabTxt($config)['12'],
            "1" => $this->tabTxt($config)['1'],
            "4" => $this->tabTxt($config)['4'],
            "7" => $this->tabTxt($config)['7'],
            "3" => $this->tabTxt($config)['3'],
            "6" => $this->tabTxt($config)['6'],
            "9" => $this->tabTxt($config)['9'],
            "13" => $this->tabTxt($config)['13'],
            "14" => $this->tabTxt($config)['14'],
            "22" => $this->tabTxt($config)['22'],
            "23" => $this->tabTxt($config)['23'],
            "15" => $this->tabTxt($config)['15'],
            "16" => $this->tabTxt($config)['16'],
            "17" => $this->tabTxt($config)['17'],
            "18" => $this->tabTxt($config)['18'],
            "19" => $this->tabTxt($config)['19'],
            "20" => $this->tabTxt($config)['20'],
            "21" => $this->tabTxt($config)['21'],
            "24" => $this->tabTxt($config)['24'],
            "25" => $this->tabTxt($config)['25'],
            "26" => $this->tabTxt($config)['26'],
            "27" => $this->tabTxt($config)['27'],
            "28" => $this->tabTxt($config)['28'],
            "29" => $this->tabTxt($config)['29'],
            "30" => $this->tabTxt($config)['30'],
            "31" => $this->tabTxt($config)['31'],
            "32" => $this->tabTxt($config)['32'],
            "33" => $this->tabTxt($config)['33'],
            "34" => $this->tabTxt($config)['34'],
            "35" => $this->tabTxt($config)['35'],
            "36" => $this->tabTxt($config)['36'],
            "37" => $this->tabTxt($config)['37'],
            "38" => $this->tabTxt($config)['38'],
            "39" => $this->tabTxt($config)['39'],
            "40" => $this->tabTxt($config)['40'],
            "41" => $this->tabTxt($config)['41'],
            "42" => $this->tabTxt($config)['42'],
            "43" => $this->tabTxt($config)['43'],
            "44" => $this->tabTxt($config)['44']
        );
        return $optionValue;
    }

    /**
     * Calcul Case Bas Heat-Wind
     */
    public function downHeatWind($switch, $datas, $info, $livestation)
    {

        $temp_day_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_day_high_f'];
        $temp_month_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_month_high_f'];
        $temp_year_high_f = $this->getAPIDatas($datas, $info, $livestation)['temp_year_high_f'];

        $temp_day_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_day_low_f'];
        $temp_month_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_month_low_f'];
        $temp_year_low_f = $this->getAPIDatas($datas, $info, $livestation)['temp_year_low_f'];

        $windchill_day_low_f = $this->getAPIDatas($datas, $info, $livestation)['windchill_day_low_f'];
        $windchill_day_low_time = $this->getAPIDatas($datas, $info, $livestation)['windchill_day_low_time'];
        $windchill_month_low_f = $this->getAPIDatas($datas, $info, $livestation)['windchill_month_low_f'];
        $windchill_year_low_f = $this->getAPIDatas($datas, $info, $livestation)['windchill_year_low_f'];

        $heat_index_day_high_f = $this->getAPIDatas($datas, $info, $livestation)['heat_index_day_high_f'];
        $heat_index_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['heat_index_day_high_time'];
        $heat_index_month_high_f = $this->getAPIDatas($datas, $info, $livestation)['heat_index_month_high_f'];
        $heat_index_year_high_f = $this->getAPIDatas($datas, $info, $livestation)['heat_index_year_high_f'];

        $page = '';
        if ($this->is_Temp('59', $this->getDMY($switch, $temp_day_high_f, $temp_month_high_f, $temp_year_high_f)) == true) {
            $page .= '<a data-toggle="tooltip" title="' . $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY')) . '">' . $this->l->trad('MIN') . ' :</a> ';
        }
        if ($this->is_Temp('59', $this->getDMY($switch, $temp_day_low_f, $temp_month_low_f, $temp_year_low_f)) == false) {
            $page .= '<a data-toggle="tooltip" title="' . $this->getDMY($switch, $this->l->trad('DAILY'), $this->l->trad('MONTHLY'), $this->l->trad('YEARLY')) . '">' . $this->l->trad('MAX') . ' :</a> ';
        }
        if ($this->is_Temp('59', $this->getDMY($switch, $temp_day_low_f, $temp_month_low_f, $temp_year_low_f)) == true) {
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
        if ($this->is_Temp('59', $this->getDMY($switch, $temp_day_low_f, $temp_month_low_f, $temp_year_low_f)) == true && $this->is_Temp('59', $this->getDMY($switch, $temp_day_high_f, $temp_month_high_f, $temp_year_high_f)) == false) {
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
        if ($this->is_Temp('59', $this->getDMY($switch, $temp_day_high_f, $temp_month_high_f, $temp_year_high_f)) == false) {
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
    public function incDownCloudy($config, $datas, $info, $livestation)
    {
        $temp_f = $this->getAPIDatas($datas, $info, $livestation)['temp_f'];
        $temp_c = $this->getAPIDatasUp($datas, $info, $livestation)['c_temp'];

        $time = $this->getAPIDatasUp($datas, $info, $livestation)['time'];
        $sunset = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunset'];
        $sunrise = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunrise'];

        $longitude = $this->getAPIDatas($datas, $info, $livestation)['longitude'];
        $latitude = $this->getAPIDatas($datas, $info, $livestation)['latitude'];

        $tmp_date = date_create($time);
        $jour = date_format($tmp_date, "d");
        $mois = date_format($tmp_date, "m");
        $annee = date_format($tmp_date, "Y");

        $utc_date = date_timezone_set($tmp_date, timezone_open('UTC'));
        $heure_utc = date_format($utc_date, "H");
        $minute_utc = date_format($utc_date, "i");

        $relative_humidity = $this->getAPIDatas($datas, $info, $livestation)['relative_humidity'];
        $solar_radiation = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation'];
        $rain_rate_in_per_hr = $this->getAPIDatas($datas, $info, $livestation)['rain_rate_in_per_hr'];
        $rain_day_in = $this->getAPIDatas($datas, $info, $livestation)['rain_day_in'];

        if ($rain_rate_in_per_hr == '0') {
            if ($config['config_sun'] == 'sun' || $config['config_sun'] == 'sun_uv') {
                if ($this->is_Temp('32', $temp_f) == true) {
                    if (($relative_humidity  >= '98') && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $solar_radiation) == false)) {
                        $page = '<div class="large500">' . $this->l->trad('FREEZING_FOG') . '</div><div class="small500">' . $this->l->trad('FROST_FOG') . '</div>';
                    } elseif ((($relative_humidity  >= '96') && ($relative_humidity < '98')) && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $solar_radiation) == false)) {
                        $page = '<div class="large500">' . $this->l->trad('FREEZING_MIST') . '</div><div class="small500">' . $this->l->trad('FROST_MIST') . '</div>';
                    } elseif ($relative_humidity  < '96' && $rain_day_in > '0') {
                        $page = $this->l->trad('ICING');
                    } elseif ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                        if ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $solar_radiation) == true) {
                            $page = $this->l->trad('SUNNY');
                        } else {
                            $page = $this->l->trad('CLOUDY');
                        }
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                } elseif ($this->is_Temp('32', $temp_f) == false) {
                    if (($relative_humidity   >= '98') && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $solar_radiation) == false)) {
                        $page = $this->l->trad('FOG');
                    } elseif ((($relative_humidity   >= '96') && ($relative_humidity   < '98')) && ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $solar_radiation) == false)) {
                        $page = $this->l->trad('MIST');
                    } elseif ($this->TimeStation($time) > $this->TimeStation($sunrise) && $this->TimeStation($time) < $this->TimeStation($sunset)) {
                        if ($this->is_sun($longitude, $latitude, $jour, $mois, $annee, $heure_utc, $minute_utc, $temp_c, $solar_radiation) == true) {
                            $page = $this->l->trad('SUNNY');
                        } else {
                            $page = $this->l->trad('CLOUDY');
                        }
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                }
            } else {
                if ($this->is_Temp('32', $temp_f) == true) {
                    if ($relative_humidity   >= '98') {
                        $page = '<div class="large500">' . $this->l->trad('FREEZING_FOG') . '</div><div class="small500">' . $this->l->trad('FROST_FOG') . '</div>';
                    } elseif ($relative_humidity   >= '96' && $relative_humidity   < '98') {
                        $page =  '<div class="large500">' . $this->l->trad('FREEZING_MIST') . '</div><div class="small500">' . $this->l->trad('FROST_MIST') . '</div>';
                    } elseif ($relative_humidity   < '96' && $rain_day_in > '0') {
                        $page = $this->l->trad('ICING');
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                } elseif ($this->is_Temp('32', $temp_f) == false) {
                    if ($relative_humidity   >= '98') {
                        $page = $this->l->trad('FOG');
                    } elseif (($relative_humidity   >= '96') && ($relative_humidity   < '98')) {
                        $page = $this->l->trad('MIST');
                    } else {
                        $page = $this->l->trad('NOUGHT');
                    }
                }
            }
        } elseif ($rain_rate_in_per_hr > '0') {
            if ($this->is_Temp('37.4', $temp_f) == false) {
                $page = $this->l->trad('PRECIPITATION');
            } elseif ($this->is_Temp('32.9', $temp_f) == false && $this->is_Temp('37.4', $temp_f) == true) {
                $page = $this->l->trad('RAIN_SNOW');
            } elseif ($this->is_Temp('32.9', $temp_f) == true) {
                $page = $this->l->trad('SNOW');
            }
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



    public function SunTxt($config)
    {
        if ($config['config_sun'] == 'sun') {
            $page = $this->l->trad('SUN');
        } elseif ($config['config_sun'] == 'uv') {
            $page = $this->l->trad('UV_INDEX');
        } elseif ($config['config_sun'] == 'sun_uv') {
            $page = $this->l->trad('SUN') . ' + ' . $this->l->trad('UV');
        } else {
            $page = $this->l->trad('DAY') . '/' . $this->l->trad('NIGHT');
        }
        return $page;
    }

    public function SunText($config)
    {
        if ($config['config_sun'] == 'sun') {
            $page = $this->l->trad('SOLAR_RADIATIONS');
        } elseif ($config['config_sun'] == 'uv') {
            $page = $this->l->trad('UV_INDEX');
        } elseif ($config['config_sun'] == 'sun_uv') {
            $page = $this->l->trad('SOLAR_RADIATIONS') . ' + ' . $this->l->trad('UV_INDEX');
        } else {
            $page = $this->l->trad('DAY') . ' / ' . $this->l->trad('NIGHT');
        }
        return $page;
    }

    public function incUpSun($switch, $config, $tab, $datas, $info, $livestation)
    {
        $time = $this->getAPIDatasUp($datas, $info, $livestation)['time'];
        $sunset = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunset'];
        $sunrise = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunrise'];

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



    public function incMidSun($switch, $config, $tab, $datas, $info, $livestation)
    {
        $time = $this->getAPIDatasUp($datas, $info, $livestation)['time'];
        $sunset = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunset'];
        $sunrise = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunrise'];

        $solar_radiation = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation'];
        $solar_radiation_day_high = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation_day_high'];
        $solar_radiation_day_high_time = $this->getAPIDatas($datas, $info, $livestation)['solar_radiation_day_high_time'];

        $uv_index = $this->getAPIDatas($datas, $info, $livestation)['uv_index'];

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
            $model1 .= '<a data-toggle="tooltip" title="' . $this->l->trad('MAX') . '&nbsp;:&nbsp;' . $solar_radiation_day_high . '&nbsp;W/m² &nbsp;' . $this->l->trad('AT') . '&nbsp;' . $this->l->timeTrad($solar_radiation_day_high_time, $this->l->getLg()) . '">';
            $model1 .= '<span ' . $this->col->colSun($switch, $solar_radiation, $datas, $info, $livestation) . ' >';
            $model1 .=  $solar_radiation;
            $model1 .= '<span class="unit05">W/m²</span></span>';
            $model1 .= '</a>';
            $model1 .= '</div>';
            $model1 .= '<div class="large500">';
            $model1 .= '<a data-toggle="tooltip" title="' . $this->l->trad('MAX') . '&nbsp;:&nbsp;' . $solar_radiation_day_high . '&nbsp;W/m² &nbsp;' . $this->l->trad('AT') . '&nbsp;' . $this->l->timeTrad($solar_radiation_day_high_time, $this->l->getLg()) . '">';
            $model1 .= '<span ' . $this->col->colSun($switch, $solar_radiation, $datas, $info, $livestation) . ' >';
            $model1 .=  $solar_radiation;
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
            $model3 .= '<span ' . $this->col->colUV($switch, $uv_index, $datas) . '>';
            $model3 .= $uv_index;
            $model3 .= '</span></div>';
            $model3 .= '<div class="large500">';
            $model3 .= '<span ' . $this->col->colUV($switch, $uv_index, $datas) . '>';
            $model3 .= $uv_index;
            $model3 .= '</span></div>';
            return $model3;
        }
    }


    public function incDownSun($config, $tab, $datas, $info, $livestation)
    {

        $time = $this->getAPIDatasUp($datas, $info, $livestation)['time'];
        $sunset = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunset'];
        $sunrise = $this->getAPIDatasUp($datas, $info, $livestation)['time_sunrise'];

        $uv_index = $this->getAPIDatas($datas, $info, $livestation)['uv_index'];

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
            $model5 = 'UV = ' . $uv_index;
            return $model5;
        }
    }










    /**
     * Création de date à partir de timestamp
     */

    public function DateCreate($datas, $lg, $timeZone)
    {
        if (isset($datas)) {
            date_default_timezone_set($timeZone);
            $date = new DateTime();
            $date->setTimestamp($datas);
            if ($lg == "fr") {
                $date_time =  $date->format('d.m.Y à H:i');
            } elseif ($lg == "en") {
                $date_time = $date->format('m/d/Y @ h:ia');
            }
        } else {
            $date_time =  '&#8709;';
        }
        return $date_time;
    }





    /**
     * Création de date à partir de format RFC822
     */

    public function DateStation($datas, $lg)
    {
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

    //format de $datas doit être "h:i a"
    public function TimeStation($datas)
    {
        $tmp_date = date_create($datas);
        if (isset($tmp_date)) {
            $time = date_format($tmp_date, "Hi");
        }
        return $time;
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


    public function getWind($switch, $mph)
    {
        $kph = round(((floatval($mph)) * 1.6093), 1);

        if ($switch['s_wind'] == 'mph') {
            $page = $mph;
        } elseif ($switch['s_wind'] == 'kph') {
            $page = $kph;
        }
        return $page;
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
        $in = round((floatval($press)), 2);
        $mb = round(((floatval($press)) * 33.8639), 1);

        if ($switch['s_press'] == 'inhg') {
            $page = $in;
        } elseif ($switch['s_press'] == 'hpa') {
            $page = $mb;
        }
        return $page;
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

        if ($hauteur_soleil > 3) {
            $seuil = ($a + $b * cos((pi() / 180) * 360 * $dayofyear / 365)) * 1080 * pow((sin(pi() / 180) * $hauteur_soleil), 1.25) * 0.85;
            $mesure = ((($temp_c * 1 - 25) * (-0.0012) * $solar_radiation) + $solar_radiation);
            if ($mesure > $seuil) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
