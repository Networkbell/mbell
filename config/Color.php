<?php

class Color
{

    public function __construct()
    {
        
    }


    public function arrColor($switch, $datas, $info, $livestation)
    {

        $css = $switch['s_css'];
        $col = $switch['s_color'];
        $daynight = $switch['s_daynight'];

        $this->statview = new StationView();

        $time = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time'];
        $sunset = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time_sunset'];
        $sunrise = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time_sunrise'];

        $Ttime = $this->statview->TimeStation($time);
        $Tsunrise = $this->statview->TimeStation($sunrise);
        $Tsunset = $this->statview->TimeStation($sunset);
        

        if ($daynight == 'on') {
            if ($css == 'bluelight' || $css == 'bluedark') {
                $css = ($Ttime > $Tsunrise && $Ttime < $Tsunset) ? 'bluelight' : 'bluedark';
            } elseif ($css == 'white' || $css == 'black') {
                $css = ($Ttime > $Tsunrise && $Ttime < $Tsunset) ? 'white' : 'black';
            }
        } elseif ($daynight == 'off') {
            $css = $css;
        }


        $neutral = array(
            "error" => (($css == 'bluedark') || ($css == 'black')) ? '#eef4fa' : '#202c3a',
            "1" => (($css == 'bluedark') || ($css == 'black')) ? '#eef4fa' :  '#202c3a'
        );

        $colored = array(
            "error" => (($css == 'bluedark') || ($css == 'black')) ? '#eef4fa' : '#202c3a',
            "1" => (($css == 'bluedark') || ($css == 'black')) ? '#eef4fa' : '#202c3a',
            "2" => (($css == 'bluedark') || ($css == 'black')) ? '#7ed832' : '#599f1e',
            "3" => (($css == 'bluedark') || ($css == 'black')) ? '#ea8e20' : '#ea8e20',
            "4" => (($css == 'bluedark') || ($css == 'black')) ? '#1bcece' : '#0f61b3',
            "5" => (($css == 'bluedark') || ($css == 'black')) ? '#0f61b3' : '#0f61b3'
        );

        $dynamic = array(
            "error" => (($css == 'bluedark') || ($css == 'black')) ? '#eef4fa' : '#202c3a',
            "temp" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#fd47f8' : '#fd47f8',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#d547fd' : '#d547fd',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#a747fd' : '#a747fd',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#8c0dfd' : '#8c0dfd',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#6c33fd' : '#6c33fd',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#500dfd' : '#500dfd',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#2720fd' : '#2720fd',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#0d42fd' : '#0d42fd',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#0d7efd' : '#0d7efd',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#0dbafd' : '#02a3e1',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#0df6fd' : '#02c7ce',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfdc8' : '#02cea1',
                "13" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfd8c' : '#02e140',
                "14" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfd50' : '#59ba02',
                "15" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfd14' : '#1f9d22',
                "16" => (($css == 'bluedark') || ($css == 'black')) ? '#42fd0d' : '#016e04',
                "17" => (($css == 'bluedark') || ($css == 'black')) ? '#7efd0d' : '#709325',
                "18" => (($css == 'bluedark') || ($css == 'black')) ? '#bafd0d' : '#9c9e2e',
                "19" => (($css == 'bluedark') || ($css == 'black')) ? '#f6fd0d' : '#c6b00e',
                "20" => (($css == 'bluedark') || ($css == 'black')) ? '#fdc80d' : '#e1b002',
                "21" => (($css == 'bluedark') || ($css == 'black')) ? '#fd8c0d' : '#fd8c0d',
                "22" => (($css == 'bluedark') || ($css == 'black')) ? '#fd500d' : '#fd500d',
                "23" => (($css == 'bluedark') || ($css == 'black')) ? '#fd140d' : '#fd140d',
                "24" => (($css == 'bluedark') || ($css == 'black')) ? '#fd0d42' : '#fd0d42',
                "25" => (($css == 'bluedark') || ($css == 'black')) ? '#fd0d7e' : '#fd0d7e',
                "26" => (($css == 'bluedark') || ($css == 'black')) ? '#fd0dba' : '#fd0dba'
            ),
            "heat" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#bafd0d' : '#9c9e2e',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#f6fd0d' : '#c6b00e',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#fdc80d' : '#e1b002',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#fd8c0d' : '#fd8c0d',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#fd500d' : '#fd500d',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#fd140d' : '#fd140d',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#fd0d42' : '#fd0d42',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#fd0d7e' : '#fd0d7e',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#fd0dba' : '#fd0dba'
            ),
            "windchill" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#fd47f8' : '#fd47f8',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#d547fd' : '#d547fd',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#a747fd' : '#a747fd',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#8c0dfd' : '#8c0dfd',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#6c33fd' : '#6c33fd',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#500dfd' : '#500dfd',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#2720fd' : '#2720fd',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#0d42fd' : '#0d42fd',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#0d7efd' : '#0d7efd',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#0dbafd' : '#02a3e1',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#0df6fd' : '#02c7ce',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfdc8' : '#02cea1',
                "13" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfd8c' : '#02e140',
                "14" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfd50' : '#59ba02',
                "15" => (($css == 'bluedark') || ($css == 'black')) ? '#0dfd14' : '#1f9d22',
                "16" => (($css == 'bluedark') || ($css == 'black')) ? '#42fd0d' : '#016e04',
                "17" => (($css == 'bluedark') || ($css == 'black')) ? '#7efd0d' : '#709325'
            ),
            "solar" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#767d89' : '#767d89',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#768986' : '#768986',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#7f8976' : '#7f8976',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#93936c' : '#93936c',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#a7a758' : '#a7a758',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#c4c43b' : '#c4c43b',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#d8d827' : '#d8d827',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#ffff00' : '#dbcd38',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#ffea00' : '#e6bc41',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#ffd500' : '#eeb139',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#ffbf00' : '#f6a331',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#ffaa00' : '#fe9329',
                "13" => (($css == 'bluedark') || ($css == 'black')) ? '#ff9500' : '#fe8129',
                "14" => (($css == 'bluedark') || ($css == 'black')) ? '#ff7f00' : '#fe7029',
                "15" => (($css == 'bluedark') || ($css == 'black')) ? '#ff6a00' : '#ff6a00',
                "16" => (($css == 'bluedark') || ($css == 'black')) ? '#ff5400' : '#ff5400',
                "17" => (($css == 'bluedark') || ($css == 'black')) ? '#ff3f00' : '#ff3f00',
                "18" => (($css == 'bluedark') || ($css == 'black')) ? '#ff2a00' : '#ff2a00',
                "19" => (($css == 'bluedark') || ($css == 'black')) ? '#ff1400' : '#ff1400',
                "20" => (($css == 'bluedark') || ($css == 'black')) ? '#ff0041' : '#ff0041',
                "21" => (($css == 'bluedark') || ($css == 'black')) ? '#ff0081' : '#ff0081',
                "22" => (($css == 'bluedark') || ($css == 'black')) ? '#ff00c1' : '#ff00c1',
                "23" => (($css == 'bluedark') || ($css == 'black')) ? '#fd00ff' : '#fd00ff',
                "24" => (($css == 'bluedark') || ($css == 'black')) ? '#fe62ff' : '#c300c4',
                "25" => (($css == 'bluedark') || ($css == 'black')) ? '#feb1ff' : '#880089',
                "26" => (($css == 'bluedark') || ($css == 'black')) ? '#ffebff' : '#4e004e',
                "night" => (($css == 'bluedark') || ($css == 'black')) ? '#1bcece' : '#0d5066'
            ),
            "uv" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#767d89' : '#767d89',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#768986' : '#768986',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#7f8976' : '#7f8976',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#93936c' : '#93936c',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#a7a758' : '#a7a758',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#c4c43b' : '#c4c43b',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#d8d827' : '#d8d827',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#ffff00' : '#dbcd38',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#ffea00' : '#e6bc41',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#ffd500' : '#eeb139',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#ffbf00' : '#f6a331',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#ff9500' : '#fe8129',
                "13" => (($css == 'bluedark') || ($css == 'black')) ? '#ff6a00' : '#ff6a00',
                "14" => (($css == 'bluedark') || ($css == 'black')) ? '#ff3f00' : '#ff3f00',
                "15" => (($css == 'bluedark') || ($css == 'black')) ? '#ff1400' : '#ff1400',
                "16" => (($css == 'bluedark') || ($css == 'black')) ? '#ff0041' : '#ff0041',
                "17" => (($css == 'bluedark') || ($css == 'black')) ? '#ff00c1' : '#ff00c1',
                "18" => (($css == 'bluedark') || ($css == 'black')) ? '#fe62ff' : '#fd00ff',
                "night" => (($css == 'bluedark') || ($css == 'black')) ? '#1bcece' : '#0d5066'
            ),
            "pressure" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#ebf5ff' : '#5b6d7d',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#d8ebff' : '#8294a4',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#c4e2ff' : '#a4b1bd',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#b1d8ff' : '#98b1c9',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#9dceff' : '#8cb1d5',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#89c4ff' : '#80b1e1',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#76bbff' : '#74b1ed',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#62b1ff' : '#62b1ff',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#4ea7ff' : '#4ea7ff',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#3b9dff' : '#3b9dff',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#2794ff' : '#2794ff',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#148aff' : '#148aff',
                "13" => (($css == 'bluedark') || ($css == 'black')) ? '#0080ff' : '#0080ff',
                "14" => (($css == 'bluedark') || ($css == 'black')) ? '#3b6cff' : '#3b6cff',
                "15" => (($css == 'bluedark') || ($css == 'black')) ? '#4f4fff' : '#4f4fff',
                "16" => (($css == 'bluedark') || ($css == 'black')) ? '#7b4fff' : '#7b4fff',
                "17" => (($css == 'bluedark') || ($css == 'black')) ? '#a74fff' : '#a74fff',
                "18" => (($css == 'bluedark') || ($css == 'black')) ? '#d34fff' : '#d34fff',
                "19" => (($css == 'bluedark') || ($css == 'black')) ? '#ff4fff' : '#ff4fff',
                "20" => (($css == 'bluedark') || ($css == 'black')) ? '#ff4fd3' : '#ff4fd3',
                "21" => (($css == 'bluedark') || ($css == 'black')) ? '#ff4fa7' : '#ff4fa7',
                "22" => (($css == 'bluedark') || ($css == 'black')) ? '#ff4f7b' : '#ff4f7b',
                "23" => (($css == 'bluedark') || ($css == 'black')) ? '#ff4f4f' : '#ff4f4f',
                "24" => (($css == 'bluedark') || ($css == 'black')) ? '#ff2828' : '#ff2828',
                "25" => (($css == 'bluedark') || ($css == 'black')) ? '#ff0101' : '#ff0101',
                "26" => (($css == 'bluedark') || ($css == 'black')) ? '#d90000' : '#d90000'
            ),
            "rain" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#ffffff' : '#06090c',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#ebf5ff' : '#3d5873',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#d8ebff' : '#517599',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#c4e2ff' : '#6489ad',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#b1d8ff' : '#7e9cbb',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#9dceff' : '#98b1c9',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#89c4ff' : '#86b1db',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#76bbff' : '#74b1ed',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#62b1ff' : '#62b1ff',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#4ea7ff' : '#4ea7ff',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#3b9dff' : '#3b9dff',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#2794ff' : '#2794ff',
                "13" => (($css == 'bluedark') || ($css == 'black')) ? '#148aff' : '#148aff',
                "14" => (($css == 'bluedark') || ($css == 'black')) ? '#0080ff' : '#0080ff',
                "15" => (($css == 'bluedark') || ($css == 'black')) ? '#0076eb' : '#0076eb',
                "16" => (($css == 'bluedark') || ($css == 'black')) ? '#006cd8' : '#006cd8',
                "17" => (($css == 'bluedark') || ($css == 'black')) ? '#0062c4' : '#0062c4',
                "18" => (($css == 'bluedark') || ($css == 'black')) ? '#0059b1' : '#0059b1'
            ),
            "wind" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#ffffff' : '#181732',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#fffffd' : '#524eab',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#feffea' : '#4e78ab',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#fdffd6' : '#4ea6ab',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#fcf8be' : '#4eab54',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#f9eca7' : '#8dab4e',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#f7dc91' : '#a4ab4e',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#f4c77b' : '#ab9b4e',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#f2af65' : '#c0943c',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#ef9350' : '#ec733b',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#ec733b' : '#ea5027',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#ea5027' : '#e50000',
                "13" => (($css == 'bluedark') || ($css == 'black')) ? '#e72913' : '#e50072',
                "14" => (($css == 'bluedark') || ($css == 'black')) ? '#e50000' : '#e500ab',
                "15" => (($css == 'bluedark') || ($css == 'black')) ? '#e50039' : '#970070',
                "16" => (($css == 'bluedark') || ($css == 'black')) ? '#e50072' : '#202c3a'
            ),
            "humidity" => array(
                "1" => (($css == 'bluedark') || ($css == 'black')) ? '#fffed1' : '#c4c43b',
                "2" => (($css == 'bluedark') || ($css == 'black')) ? '#e7f3cf' : '#a7a758',
                "3" => (($css == 'bluedark') || ($css == 'black')) ? '#d0e8ce' : '#93936c',
                "4" => (($css == 'bluedark') || ($css == 'black')) ? '#b9ddcc' : '#7f8976',
                "5" => (($css == 'bluedark') || ($css == 'black')) ? '#a2d3cb' : '#768986',
                "6" => (($css == 'bluedark') || ($css == 'black')) ? '#8bc8ca' : '#8294a4',
                "7" => (($css == 'bluedark') || ($css == 'black')) ? '#73bdc8' : '#5b8bb7',
                "8" => (($css == 'bluedark') || ($css == 'black')) ? '#5cb2c7' : '#388ddb',
                "9" => (($css == 'bluedark') || ($css == 'black')) ? '#45a8c6' : '#0080ff',
                "10" => (($css == 'bluedark') || ($css == 'black')) ? '#498ace' : '#4f4fff',
                "11" => (($css == 'bluedark') || ($css == 'black')) ? '#4d6cd6' : '#0101ff',
                "12" => (($css == 'bluedark') || ($css == 'black')) ? '#514edf' : '#0000b2'

            ),

        );


        if ($col == 'neutral') {
            $result = $neutral;
        }
        if ($col == 'colored') {
            $result = $colored;
        }
        if ($col == 'dynamic') {
            $result = $dynamic;
        }

        return $result;
    }


    /* $value = no parseJson / no Celsius */
    public function colTemp($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['temp'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = ($value == null) ? $col['3'] : (($value <= '32') ? $col['4'] : $col['3']);
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;') {
                $color =  $col['error'];
            } elseif ($value >= '104') { //40°C
                $color =  $tcol['26'];
            } elseif ($value >= '95' && $value < '104') { //35-40°C
                $color =  $tcol['25'];
            } elseif ($value >= '86' && $value < '95') { //30-35°C
                $color =  $tcol['24'];
            } elseif ($value >= '80.6' && $value < '86') { //27.5-30°C
                $color =  $tcol['23'];
            } elseif ($value >= '77' && $value < '80.6') { //25-27.5°C
                $color =  $tcol['22'];
            } elseif ($value >= '71.6' && $value < '77') { //22.5-25°C
                $color =  $tcol['21'];
            } elseif ($value >= '68' && $value < '71.6') { //20-22.5°C
                $color =  $tcol['20'];
            } elseif ($value >= '62.6' && $value < '68') { //17.5-20°C
                $color =  $tcol['19'];
            } elseif ($value >= '59' && $value < '62.6') { //15-17.5°C
                $color =  $tcol['18'];
            } elseif ($value >= '53.6' && $value < '59') { //12.5-15°C
                $color =  $tcol['17'];
            } elseif ($value >= '50' && $value < '53.6') { //10-12.5°C
                $color =  $tcol['16'];
            } elseif ($value >= '44.6' && $value < '50') { //7.5-10°C
                $color =  $tcol['15'];
            } elseif ($value >= '41' && $value < '44.6') { //5-7.5°C
                $color =  $tcol['14'];
            } elseif ($value >= '35.6' && $value < '41') { //2.5-5°C
                $color =  $tcol['13'];
            } elseif ($value >= '32' && $value < '35.6') { //0-2.5°C
                $color =  $tcol['12'];
            } elseif ($value >= '28.4' && $value < '32') { //-2.5 0°C
                $color =  $tcol['11'];
            } elseif ($value >= '23' && $value < '28.4') { //-5 -2.5°C
                $color =  $tcol['10'];
            } elseif ($value >= '19.4' && $value < '23') { //-7.5 -5°C
                $color =  $tcol['9'];
            } elseif ($value >= '14' && $value < '19.4') { //-10 -7.5°C
                $color =  $tcol['8'];
            } elseif ($value >= '5' && $value < '14') { //-15 -10°C 
                $color =  $tcol['7'];
            } elseif ($value >= '-4' && $value < '5') { //-20 -15°C 
                $color =  $tcol['6'];
            } elseif ($value >= '-13' && $value < '-4') { //-25 -20°C 
                $color =  $tcol['5'];
            } elseif ($value >= '-22' && $value < '-13') { //-30 -25°C 
                $color =  $tcol['4'];
            } elseif ($value >= '-31' && $value < '-22') { //-35 -30°C 
                $color =  $tcol['3'];
            } elseif ($value >= '-40' && $value < '-31') { //-40 -35°C 
                $color =  $tcol['2'];
            } elseif ($value < '-40') { //<-40°C
                $color =  $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }

    /* $value = no parseJson / no Celsius */
    public function colHeat($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['heat'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = ($value == null) ? $col['3'] : (($value <= '32') ? $col['4'] : $col['3']);
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;') {
                $color =  $col['error'];
            } elseif ($value >= '104') { //40°C
                $color =  $tcol['9'];
            } elseif ($value >= '95' && $value < '104') { //35-40°C
                $color = $tcol['8'];
            } elseif ($value >= '86' && $value < '95') { //30-35°C
                $color = $tcol['7'];
            } elseif ($value >= '80.6' && $value < '86') { //27.5-30°C
                $color = $tcol['6'];
            } elseif ($value >= '77' && $value < '80.6') { //25-27.5°C
                $color = $tcol['5'];
            } elseif ($value >= '71.6' && $value < '77') { //22.5-25°C
                $color = $tcol['4'];
            } elseif ($value >= '68' && $value < '71.6') { //20-22.5°C
                $color = $tcol['3'];
            } elseif ($value >= '62.6' && $value < '68') { //17.5-20°C
                $color = $tcol['2'];
            } elseif ($value < '62.6') { //15-17.5°C
                $color = $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }

    /* $value = no parseJson / no Celsius */
    public function colWindchill($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['windchill'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = ($value == null) ? $col['3'] : (($value <= '32') ? $col['4'] : $col['3']);
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;') {
                $color =  $col['error'];
            } elseif ($value >= '53.6') { //12.5-15°C
                $color =  $tcol['17'];
            } elseif ($value >= '50' && $value < '53.6') { //10-12.5°C
                $color =  $tcol['16'];
            } elseif ($value >= '44.6' && $value < '50') { //7.5-10°C
                $color =  $tcol['15'];
            } elseif ($value >= '41' && $value < '44.6') { //5-7.5°C
                $color =  $tcol['14'];
            } elseif ($value >= '35.6' && $value < '41') { //2.5-5°C
                $color =  $tcol['13'];
            } elseif ($value >= '32' && $value < '35.6') { //0-2.5°C
                $color =  $tcol['12'];
            } elseif ($value >= '28.4' && $value < '32') { //-2.5 0°C
                $color =  $tcol['11'];
            } elseif ($value >= '23' && $value < '28.4') { //-5 -2.5°C
                $color =  $tcol['10'];
            } elseif ($value >= '19.4' && $value < '23') { //-7.5 -5°C
                $color =  $tcol['9'];
            } elseif ($value >= '14' && $value < '19.4') { //-10 -7.5°C
                $color =  $tcol['8'];
            } elseif ($value >= '5' && $value < '14') { //-15 -10°C 
                $color =  $tcol['7'];
            } elseif ($value >= '-4' && $value < '5') { //-20 -15°C 
                $color =  $tcol['6'];
            } elseif ($value >= '-13' && $value < '-4') { //-25 -20°C 
                $color =  $tcol['5'];
            } elseif ($value >= '-22' && $value < '-13') { //-30 -25°C 
                $color =  $tcol['4'];
            } elseif ($value >= '-31' && $value < '-22') { //-35 -30°C 
                $color =  $tcol['3'];
            } elseif ($value >= '-40' && $value < '-31') { //-40 -35°C 
                $color =  $tcol['2'];
            } elseif ($value < '-40') { //<-40°C
                $color =  $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }


    /* $value = no parseJson  */
    public function colSun($switch, $value, $datas, $info, $livestation)
    {
        $this->statview = new StationView();
        
        $time = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time'];
        $sunset = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time_sunset'];
        $sunrise = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time_sunrise'];

        $col = $this->arrColor($switch, $datas, $info, $livestation);

        $tcol = $col['solar'] ?? '';
        $s_col = $switch['s_color'];

        $Ttime = $this->statview->TimeStation($time);
        $Tsunrise = $this->statview->TimeStation($sunrise);
        $Tsunset = $this->statview->TimeStation($sunset); 

        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = ($value == '&#8709;' || $value < '0') ? $col['3'] : (($Ttime  > $Tsunrise && $Ttime  < $Tsunset) ? $col['3'] : $col['5']);
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;' || $value < '0') {
                $color =  $col['error'];
            } elseif ($Ttime  > $Tsunrise && $Ttime  < $Tsunset) {
                if ($value >= '2000') {
                    $color =  $tcol['26'];
                } elseif ($value >= '1500' && $value < '2000') {
                    $color =  $tcol['25'];
                } elseif ($value >= '1250' && $value < '1500') {
                    $color =  $tcol['24'];
                } elseif ($value >= '1000' && $value < '1250') {
                    $color =  $tcol['23'];
                } elseif ($value >= '900' && $value < '1000') {
                    $color =  $tcol['22'];
                } elseif ($value >= '800' && $value < '900') {
                    $color =  $tcol['21'];
                } elseif ($value >= '700' && $value < '800') {
                    $color =  $tcol['20'];
                } elseif ($value >= '600' && $value < '700') {
                    $color =  $tcol['19'];
                } elseif ($value >= '500' && $value < '600') {
                    $color =  $tcol['18'];
                } elseif ($value >= '400' && $value < '500') {
                    $color =  $tcol['17'];
                } elseif ($value >= '300' && $value < '400') {
                    $color =  $tcol['16'];
                } elseif ($value >= '250' && $value < '300') {
                    $color =  $tcol['15'];
                } elseif ($value >= '200' && $value < '250') {
                    $color =  $tcol['14'];
                } elseif ($value >= '175' && $value < '200') {
                    $color =  $tcol['13'];
                } elseif ($value >= '150' && $value < '175') {
                    $color =  $tcol['12'];
                } elseif ($value >= '125' && $value < '150') {
                    $color =  $tcol['11'];
                } elseif ($value >= '100' && $value < '125') {
                    $color =  $tcol['10'];
                } elseif ($value >= '75' && $value < '100') {
                    $color =  $tcol['9'];
                } elseif ($value >= '50' && $value < '75') {
                    $color =  $tcol['8'];
                } elseif ($value >= '40' && $value < '50') {
                    $color =  $tcol['7'];
                } elseif ($value >= '30' && $value < '40') {
                    $color =  $tcol['6'];
                } elseif ($value >= '20' && $value < '30') {
                    $color =  $tcol['5'];
                } elseif ($value >= '10' && $value < '20') {
                    $color =  $tcol['4'];
                } elseif ($value >= '5' && $value < '10') {
                    $color =  $tcol['3'];
                } elseif ($value > '0' && $value < '5') {
                    $color =  $tcol['2'];
                } elseif ($value == '0') {
                    $color =  $tcol['1'];
                }
            } else {
                $color =  $tcol['night'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }


    /* $value = no parseJson  */
    public function colUV($switch, $value, $datas, $info, $livestation)
    {
        $this->statview = new StationView();
        
        $time = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time'];
        $sunset = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time_sunset'];
        $sunrise = $this->statview->getAPIDatasUp($datas, $info, $livestation)['time_sunrise'];

        $col = $this->arrColor($switch, $datas, $info, $livestation);

        $tcol = $col['uv'] ?? '';
        $s_col = $switch['s_color'];

        $Ttime = $this->statview->TimeStation($time);
        $Tsunrise = $this->statview->TimeStation($sunrise);
        $Tsunset = $this->statview->TimeStation($sunset); 

        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = ($value == '&#8709;' || $value < '0') ? $col['3'] : (($Ttime > $Tsunrise && $Ttime < $Tsunset) ? $col['3'] : $col['5']);
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;' || $value < '0') {
                $color =  $col['error'];
            } elseif ($Ttime > $Tsunrise && $Ttime < $Tsunset) {

                if ($value >= '16') {
                    $color = $tcol['18'];
                } elseif ($value >= '15' && $value < '16') {
                    $color = $tcol['17'];
                } elseif ($value >= '14' && $value < '15') {
                    $color = $tcol['16'];
                } elseif ($value >= '13' && $value < '14') {
                    $color = $tcol['15'];
                } elseif ($value >= '12' && $value < '13') {
                    $color = $tcol['14'];
                } elseif ($value >= '11' && $value < '12') {
                    $color = $tcol['13'];
                } elseif ($value >= '10' && $value < '11') {
                    $color = $tcol['12'];
                } elseif ($value >= '9' && $value < '10') {
                    $color = $tcol['11'];
                } elseif ($value >= '8' && $value < '9') {
                    $color = $tcol['10'];
                } elseif ($value >= '7' && $value < '8') {
                    $color = $tcol['9'];
                } elseif ($value >= '6' && $value < '7') {
                    $color = $tcol['8'];
                } elseif ($value >= '5' && $value < '6') {
                    $color = $tcol['7'];
                } elseif ($value >= '4' && $value < '5') {
                    $color = $tcol['6'];
                } elseif ($value >= '3' && $value < '4') {
                    $color = $tcol['5'];
                } elseif ($value >= '2' && $value < '3') {
                    $color = $tcol['4'];
                } elseif ($value >= '1' && $value < '2') {
                    $color = $tcol['3'];
                } elseif ($value > '0' && $value < '1') {
                    $color = $tcol['2'];
                } elseif ($value == '0') {
                    $color = $tcol['1'];
                }
            } else {
                $color = $tcol['night'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }


    /* $value = no parseJson / no press in */
    public function colPress($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['pressure'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = $col['1'];
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;') {
                $color =  $col['error'];
            } elseif ($value >= '1050') {
                $color = $tcol['26'];
            } elseif ($value >= '1045' && $value < '1050') {
                $color = $tcol['25'];
            } elseif ($value >= '1040' && $value < '1045') {
                $color = $tcol['24'];
            } elseif ($value >= '1035' && $value < '1040') {
                $color = $tcol['23'];
            } elseif ($value >= '1030' && $value < '1035') {
                $color = $tcol['22'];
            } elseif ($value >= '1025' && $value < '1030') {
                $color = $tcol['21'];
            } elseif ($value >= '1020' && $value < '1025') {
                $color = $tcol['20'];
            } elseif ($value >= '1015' && $value < '1020') {
                $color = $tcol['19'];
            } elseif ($value >= '1010' && $value < '1015') {
                $color = $tcol['18'];
            } elseif ($value >= '1005' && $value < '1010') {
                $color = $tcol['17'];
            } elseif ($value >= '1000' && $value < '1005') {
                $color = $tcol['16'];
            } elseif ($value >= '995' && $value < '1000') {
                $color = $tcol['15'];
            } elseif ($value >= '990' && $value < '995') {
                $color = $tcol['14'];
            } elseif ($value >= '985' && $value < '990') {
                $color = $tcol['13'];
            } elseif ($value >= '980' && $value < '985') {
                $color = $tcol['12'];
            } elseif ($value >= '975' && $value < '980') {
                $color = $tcol['11'];
            } elseif ($value >= '970' && $value < '975') {
                $color = $tcol['10'];
            } elseif ($value >= '965' && $value < '970') {
                $color = $tcol['9'];
            } elseif ($value >= '960' && $value < '965') {
                $color = $tcol['8'];
            } elseif ($value >= '950' && $value < '960') {
                $color = $tcol['7'];
            } elseif ($value >= '940' && $value < '950') {
                $color = $tcol['6'];
            } elseif ($value >= '930' && $value < '940') {
                $color = $tcol['5'];
            } elseif ($value >= '920' && $value < '930') {
                $color = $tcol['4'];
            } elseif ($value >= '910' && $value < '920') {
                $color = $tcol['3'];
            } elseif ($value >= '900' && $value < '910') {
                $color = $tcol['2'];
            } elseif ($value < '900') {
                $color = $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }

    /* $value = no parseJson / no rain mm */
    public function colRain($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['rain'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = $col['2'];
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;' || $value < '0') {
                $color =  $col['error'];
            } elseif ($value >= '39,37') {
                $color = $tcol['18'];
            } elseif ($value >= '19.69' && $value < '39.37') {
                $color = $tcol['17'];
            } elseif ($value >= '11.81' && $value < '19.69') {
                $color = $tcol['16'];
            } elseif ($value >= '7.87' && $value < '11.81') {
                $color = $tcol['15'];
            } elseif ($value >= '5.91' && $value < '7.87') {
                $color = $tcol['14'];
            } elseif ($value >= '3.94' && $value < '5.91') {
                $color = $tcol['13'];
            } elseif ($value >= '2.95' && $value < '3.94') {
                $color = $tcol['12'];
            } elseif ($value >= '1.97' && $value < '2.95') {
                $color = $tcol['11'];
            } elseif ($value >= '1.57' && $value < '1.97') {
                $color = $tcol['10'];
            } elseif ($value >= '1.18' && $value < '1.57') {
                $color = $tcol['9'];
            } elseif ($value >= '0.79' && $value < '1.18') {
                $color = $tcol['8'];
            } elseif ($value >= '0.39' && $value < '0.79') {
                $color = $tcol['7'];
            } elseif ($value >= '0.20' && $value < '0.39') {
                $color = $tcol['6'];
            } elseif ($value >= '0.08' && $value < '0.20') {
                $color = $tcol['5'];
            } elseif ($value >= '0.04' && $value < '0.08') {
                $color = $tcol['4'];
            } elseif ($value >= '0.02' && $value < '0.04') {
                $color = $tcol['3'];
            } elseif ($value > '0' && $value < '0.02') {
                $color = $tcol['2'];
            } elseif ($value == '0') {
                $color = $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }


    /* $value = no parseJson / no wind kph */
    public function colWind($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['wind'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = $col['1'];
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;' || $value < '0') {
                $color =  $col['error'];
            } elseif ($value >= '124') {
                $color = $tcol['16'];
            } elseif ($value >= '93' && $value < '124') {
                $color = $tcol['15'];
            } elseif ($value >= '75' && $value < '93') {
                $color = $tcol['14'];
            } elseif ($value >= '62' && $value < '75') {
                $color = $tcol['13'];
            } elseif ($value >= '56' && $value < '62') {
                $color = $tcol['12'];
            } elseif ($value >= '50' && $value < '56') {
                $color = $tcol['11'];
            } elseif ($value >= '43' && $value < '50') {
                $color = $tcol['10'];
            } elseif ($value >= '37' && $value < '43') {
                $color = $tcol['9'];
            } elseif ($value >= '31' && $value < '37') {
                $color = $tcol['8'];
            } elseif ($value >= '25' && $value < '31') {
                $color = $tcol['7'];
            } elseif ($value >= '19' && $value < '25') {
                $color = $tcol['6'];
            } elseif ($value >= '12' && $value < '19') {
                $color = $tcol['5'];
            } elseif ($value >= '6' && $value < '12') {
                $color = $tcol['4'];
            } elseif ($value >= '3' && $value < '6') {
                $color = $tcol['3'];
            } elseif ($value > '0' && $value < '3') {
                $color = $tcol['2'];
            } elseif ($value == '0') {
                $color = $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }

    /* $value = no parseJson  */
    public function colHumidity($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['humidity'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = $col['2'];
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;' || $value < '0' || $value > '100') {
                $color =  $col['error'];
            } elseif ($value >= '98' && $value <= '100') {
                $color = $tcol['12'];
            } elseif ($value >= '95' && $value < '98') {
                $color = $tcol['11'];
            } elseif ($value >= '90' && $value < '95') {
                $color = $tcol['10'];
            } elseif ($value >= '80' && $value < '90') {
                $color = $tcol['9'];
            } elseif ($value >= '70' && $value < '80') {
                $color = $tcol['8'];
            } elseif ($value >= '60' && $value < '70') {
                $color = $tcol['7'];
            } elseif ($value >= '50' && $value < '60') {
                $color = $tcol['6'];
            } elseif ($value >= '40' && $value < '50') {
                $color = $tcol['5'];
            } elseif ($value >= '30' && $value < '40') {
                $color = $tcol['4'];
            } elseif ($value >= '20' && $value < '30') {
                $color = $tcol['3'];
            } elseif ($value >= '10' && $value < '20') {
                $color = $tcol['2'];
            } elseif ($value >= '0' && $value < '10') {
                $color = $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }


    /* $value = no parseJson  */
    public function colLeaf($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['humidity'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = $col['2'];
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;' || $value < '0' || $value > '15') {
                $color =  $col['error'];
            } elseif ($value >= '14' && $value <= '15') {
                $color = $tcol['12'];
            } elseif ($value >= '13' && $value < '14') {
                $color = $tcol['11'];
            } elseif ($value >= '12' && $value < '13') {
                $color = $tcol['10'];
            } elseif ($value >= '11' && $value < '12') {
                $color = $tcol['9'];
            } elseif ($value >= '10' && $value < '11') {
                $color = $tcol['8'];
            } elseif ($value >= '9' && $value < '10') {
                $color = $tcol['7'];
            } elseif ($value >= '8' && $value < '9') {
                $color = $tcol['6'];
            } elseif ($value >= '7' && $value < '8') {
                $color = $tcol['5'];
            } elseif ($value >= '6' && $value < '7') {
                $color = $tcol['4'];
            } elseif ($value >= '4' && $value < '6') {
                $color = $tcol['3'];
            } elseif ($value >= '2' && $value < '4') {
                $color = $tcol['2'];
            } elseif ($value >= '0' && $value < '2') {
                $color = $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }



    /* $value = no parseJson  */
    public function colSoil($switch, $value, $datas, $info, $livestation)
    {
        $col = $this->arrColor($switch, $datas, $info, $livestation);
        $tcol = $col['humidity'] ?? '';
        $s_col = $switch['s_color'];
        if ($s_col == 'neutral') {
            $color = $col['1'];
        } elseif ($s_col == 'colored') {
            $color = $col['2'];
        } elseif ($s_col == 'dynamic') {
            if ($value == '&#8709;' || $value < '0' || $value > '200') {
                $color =  $col['error'];
            } elseif ($value <= '5' && $value >= '0') {
                $color = $tcol['12'];
            } elseif ($value <= '10' && $value > '5') {
                $color = $tcol['11'];
            } elseif ($value <= '20' && $value > '10') {
                $color = $tcol['10'];
            } elseif ($value <= '40' && $value > '20') {
                $color = $tcol['9'];
            } elseif ($value <= '60' && $value > '40') {
                $color = $tcol['8'];
            } elseif ($value <= '100' && $value > '60') {
                $color = $tcol['7'];
            } elseif ($value <= '140' && $value > '100') {
                $color = $tcol['6'];
            } elseif ($value <= '160' && $value > '140') {
                $color = $tcol['5'];
            } elseif ($value <= '180' && $value > '160') {
                $color = $tcol['4'];
            } elseif ($value <= '190' && $value > '180') {
                $color = $tcol['3'];
            } elseif ($value <= '195' && $value > '190') {
                $color = $tcol['2'];
            } elseif ($value <= '200' && $value > '195') {
                $color = $tcol['1'];
            }
        }
        $page = 'style="color:' . $color . '"';
        return $page;
    }



}
