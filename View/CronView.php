<?php
class CronView extends View
{

    public function __construct()
    {
        parent::__construct();
        $this->statview = new StationView();
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
            "_URL" => 'index.php?controller=pref&action=list&',
            "_LG" => $this->l->getLg(),
            "HOMEPAGE" => $this->l->trad('SETTINGS'),
            "HOMESCREEN" => $this->l->trad('SETTINGS'),
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
        $this->page = str_replace('{_URL}',  $param['_URL'], $this->page);
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
        $this->page = str_replace('{HOMEPAGE}',  $param['HOMEPAGE'], $this->page);
        $this->page = str_replace('{HOMESCREEN}',  $param['HOMESCREEN'], $this->page);
        $this->page = str_replace('{LOGOUT1}',  $this->l->trad('LOGOUT1'), $this->page);
        $this->page = str_replace('{LOGOUT2}',  $this->l->trad('LOGOUT2'), $this->page);
    }

    public function cronList($config, $active, $paramJson, $liveStation, $timeCron)
    {
        $location = $this->statview->getAPIDatas($paramJson, $active, $liveStation)['location'];
        $timeZone = $this->statview->getAPIDatasUp($paramJson, $active, $liveStation)['fuseau'];

        $param = array(
            "1" => $active['stat_type'],
            "2" => $location,
            "3" => ($config['config_cron'] == 1) ? $this->l->trad('ACTIVATED') : $this->l->trad('DISABLED'),
            "4" => $this->statview->DateCreate($timeCron['data_time_cron'], $this->l->getLg(), $timeZone),
        );

        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('BDD_MANAGMENT') . '</h1>';
        $this->page .= $this->getListInfoCron($param);
        $this->page .= ($config['config_cron'] == 1) ? $this->getButton($this->l->getLg(), 'cron', 'disactive', $this->l->trad('CRON_DISACTIVE')) : $this->getButton($this->l->getLg(), 'cron', 'active', $this->l->trad('CRON_ACTIVE'));
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('CRON') . '</h1>';
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_1'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_3'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_4'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_5'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_6'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_7'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_8'));
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function getListInfoCron($param)
    {
        $this->page .= $this->searchHTML('listInfoCron', 'cron');
        $this->page = str_replace('{_STATION_TYPE}',  $param['1'], $this->page);
        $this->page = str_replace('{_STATION_LOCATION}',  $param['2'], $this->page);
        $this->page = str_replace('{_CRON_STATUS}',  $param['3'], $this->page);
        $this->page = str_replace('{_CRON_TIMER}',  $param['4'], $this->page);
        $this->page = str_replace('{STATION_TYPE}',  $this->l->trad('STATION_TYPE'), $this->page);
        $this->page = str_replace('{STATION_LOCATION}',  $this->l->trad('STATION_LOCATION'), $this->page);
        $this->page = str_replace('{CRON_STATUS}',  $this->l->trad('CRON_STATUS'), $this->page);
        $this->page = str_replace('{CRON_TIMER}',  $this->l->trad('CRON_TIMER'), $this->page);
    }
}
