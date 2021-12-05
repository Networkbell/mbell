<?php
class ChangeView extends View
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
            "_LG" => $this->l->getLg()
        );
        $this->page =  $this->getHead($param);
        $this->page .=  '<body>';
        $this->page .=  '<div class="container px-0">';
        $this->page .= $this->getHeader($param);
    }

    public function getHeader($param)
    {
        $this->page .= $this->searchHTML('headerPref', 'pref');
        $this->page = str_replace('{_LOGO}',  $param['_LOGO'], $this->page);
        $this->page = str_replace('{_ROOT}',  $param['_ROOT'], $this->page);
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
        $this->page = str_replace('{HOMEPAGE}',  $this->l->trad('HOMEPAGE'), $this->page);
        $this->page = str_replace('{HOMESCREEN}',  $this->l->trad('HOMESCREEN'), $this->page);
        $this->page = str_replace('{LOGOUT1}',  $this->l->trad('LOGOUT1'), $this->page);
        $this->page = str_replace('{LOGOUT2}',  $this->l->trad('LOGOUT2'), $this->page);
    }

    public function changeList($info, $station, $datas)
    {
        $zero = '&#8709;';
        $location = isset($datas->location) ? $datas->location : $zero;
        $station_id = isset($datas->station_id) ? $datas->station_id : $zero;

        $param = array(
            "3" => $station['stat_type'],
            "4" => $location,
            "5" => $station_id,
            "6" => $station['stat_did'],
            "7" => $station['stat_key'],
            "8" => $station['stat_password'],
            "9" => $station['stat_token'],
            "CHANGE_INFO_1" => $this->l->trad('CHANGE_INFO_1'),
            "CHANGE_INFO_2" => $this->l->trad('CHANGE_INFO_2') . $station['stat_id'],
           "UPDATE_INFO" => $this->l->trad('UPDATE_INFO'),
            "DELETE_INFO" => $this->l->trad('DELETE_INFO'),
            "ACTIVE_INFO" => $this->l->trad('ACTIVE_INFO')
        );

        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('STATION_ACTIVE') . '</h1>';
        $this->page .= $this->getInfo($param['CHANGE_INFO_1']);
        $this->page .= $this->getInfo($param['CHANGE_INFO_2']);
        if ($station['stat_type'] == 'v1') {
            $this->page .= $this->getListInfov1($param);
        }
        if ($station['stat_type'] == 'v2') {
            $this->page .= $this->getListInfov2($param);
        }
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('TITLE_CHANGE') . ' ' . $_SESSION['user_login'] . '</h1>';
        $this->page .= "<table class='table table-striped table-bordered'>";
        $this->page .= "<thead>";
        $this->page .= "<tr>";
        $this->page .= "<th scope='col'>ID</th>";
        $this->page .= "<th scope='col'>TYPE</th>";
        $this->page .= "<th scope='col'>DID</th>";
        $this->page .= "<th scope='col'>KEY</th>";
        $this->page .= "<th scope='col'>USER</th>";
        $this->page .= "<th scope='col'>PASSWORD</th>";
        $this->page .= "<th scope='col'>TOKEN</th>";
        $this->page .= "<th scope='col'>ACTIONS</th>";
        $this->page .= "</tr>";
        $this->page .= "</thead>";
        $this->page .= "<tbody>";
        foreach ($info as $list) {
            $id = $list['stat_id'];
            $lg = $this->l->getLg();
            $this->page .= "<tr>";
            $this->page .= "<td>" . $id . "</td>";
            $this->page .= "<td>" . $list['stat_type'] . "</td>";
            $this->page .= "<td>" . $list['stat_did'] . "</td>";
            $this->page .= "<td>" . $list['stat_key'] . "</td>";
            $this->page .= "<td>" . $list['stat_users'] . "</td>";
            $this->page .= "<td>" . $list['stat_password'] . "</td>";
            $this->page .= "<td>" . $list['stat_token'] . "</td>";
            $this->page .= "<td class='td_button' ><a title='".$param['UPDATE_INFO']."' class='text-primary' href='index.php?controller=change&action=update&stat_id=$id'><i class='fas fa-pen-square'></i></a> ";
            $this->page .= "<a class='text-secondary' title='".$param['DELETE_INFO']."' href='index.php?controller=change&action=delete&stat_id=$id&lg=$lg'><i class='fas fa-trash-alt'></i></a>";
            $this->page .= "<a class='lock_unlock text-danger' title='".$param['ACTIVE_INFO']."' href='index.php?controller=change&action=active&stat_id=$id&lg=$lg'><i class='fas fa-toggle-off icon-unlock'></i><i class='fas fa-toggle-on icon-lock'></i></a></td>";
            $this->page .= "</tr>";
        }
        $this->page .= "</tbody>";
        $this->page .= "</table>";
        $this->page .= $this->getButton($this->l->getLg(), 'change', 'choose', $this->l->trad('ADD_STATION'));
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }



    public function addStation($station, $paramGet)
    {
        $item = $paramGet['user_id'];

        $this->constructHead();
        $param = array(
            "STATION_CHOOSE" => $this->l->trad('STATION_CHOOSE'),
            "1" => $this->stationActive($station),
            "2" => $this->l->trad('CHOOSE_SELECT'),
            "3" => $this->l->trad('STATION_SELECT_V1'),
            "4" => $this->l->trad('STATION_SELECT_V2'),
            "STATION_STEP6_P1" => $this->l->trad('STATION_STEP6_P1'),
            "STATION_STEP6_P2" => $this->l->trad('STATION_STEP6_P2'),
            "INFO_STATION" => $this->l->trad('INFO_STATION'),
            "_USER_ID" => $item,
            "_LG" => $this->l->getLg(),
            "_CONTROLLER" => 'change',
            "_ACTION" => 'choose',
            "_VAL_STAT_DID" => '',
            "_VAL_STAT_KEY" => '',
            "_VAL_STAT_USERS" => '',
            "_VAL_STAT_PASSWORD" => '',
            "_VAL_STAT_TOKEN" => '',
            "_VAL_STAT_ID" => '',
            "STATION_BUTTON" => $this->l->trad('ADD_STATION')

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
        $this->page .= '<form action="index.php?controller=change&action=add&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormStation($station, $param, $param['_CONTROLLER']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function updateStation($station, $paramGet)
    {
        $item = $paramGet['user_id'];
        $this->constructHead();
        $param = array(
            "STATION_CHOOSE" => $this->l->trad('STATION_CHOOSE'),
            "1" => $this->stationActive($station['stat_type']),
            "2" => $this->l->trad('CHOOSE_SELECT'),
            "3" => $this->l->trad('STATION_SELECT_V1'),
            "4" => $this->l->trad('STATION_SELECT_V2'),
            "STATION_STEP6_P1" => $this->l->trad('STATION_STEP6_P1'),
            "STATION_STEP6_P2" => $this->l->trad('STATION_STEP6_P2'),
            "INFO_STATION" => $this->l->trad('INFO_STATION'),
            "_USER_ID" => $item,
            "_LG" => $this->l->getLg(),
            "_CONTROLLER" => 'change',
            "_ACTION" => 'choose',
            "_VAL_STAT_DID" => $station['stat_did'],
            "_VAL_STAT_KEY" => $station['stat_key'],
            "_VAL_STAT_USERS" => $station['stat_users'],
            "_VAL_STAT_PASSWORD" => $station['stat_password'],
            "_VAL_STAT_TOKEN" => $station['stat_token'],
            "_VAL_STAT_ID" => $station['stat_id'],
            "STATION_BUTTON" => $this->l->trad('UPDATE_STATION')

        );
        $this->page .= '<main id="main_installer">';
        /*$this->page .= '<section>';
        $this->page .= '<h1>' . $param['STATION_CHOOSE'] . '</h1>';
        $this->page .= $this->getInfo($param['STATION_STEP6_P1']);
        $this->page .= $this->getInfo($param['STATION_STEP6_P2']);
        $this->page .= $this->getStationV0($param);
        $this->page .= '</section>';*/
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['INFO_STATION'] . '</h1>';
        $this->page .= '<form action="index.php?controller=change&action=updateForm&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormStation($station['stat_type'], $param, $param['_CONTROLLER']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }
}
