<?php
class CronView extends View
{

    public function __construct()
    {
        parent::__construct();
        $this->statview = new StationView();
    }


    public function constructHead($url)
    {
        $param = array(
            "META_KEY" => $this->l->trad('META_KEY'),
            "META_DESCRIPTION" => $this->l->trad('META_DESCRIPTION'),
            "MBELL_TITRE" => $this->l->trad('MBELL_TITRE_PREF'),
            "_CSS" => "maincolor",
            "_LOGO" => "1",
            "_ROOT" => $this->getRoot(),
            "_URL" => $url,
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

    public function cronList($config, $active, $paramJson, $liveStation, $timeCron, $livenbr)
    {
        $zero = '&#8709;';
        $livetab = 0; // pas besoin ici
        $location = $this->statview->getAPIDatas($paramJson, $active, $liveStation, $livenbr,$livetab)['location'];
        $timeZone = $this->statview->getAPIDatasUp($paramJson, $active, $liveStation, $livenbr,$livetab)['fuseau'];
        $timeCron['data_time_cron'] = ($timeCron['data_time_cron']) ?? $zero;

        $param = array(
            "1" => $active['stat_type'],
            "2" => $location,
            "3" => ($config['config_cron'] == 1 || $config['config_cron'] == 2|| $config['config_cron'] == 3) ? $this->l->trad('ACTIVATED') : $this->l->trad('DISABLED'),
            "4" => $this->statview->DateCreate($timeCron['data_time_cron'], $this->l->getLg(), $timeZone),
            "5" => ($active['stat_type'] == 'live') ? '15mn' : '10mn',
            "6" => $this->cronType($config['config_cron']),
            "_URL" => 'index.php?controller=pref&action=list&',
        );

        $this->constructHead($param['_URL']);
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('BDD_MANAGMENT') . '</h1>';
        $this->page .= $this->getListInfoCron($param);
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('CRON') . '</h1>';
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_1'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_3'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_8'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_4'));
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_5'));
        $this->page .= '<div id="cronserver_btn">';
        $this->page .= ($config['config_cron'] == 1 || $config['config_cron'] == 2 || $config['config_cron'] == 3) ? $this->getButton($this->l->getLg(), 'cron', 'disactive', $this->l->trad('CRON_DISACTIVE')) : $this->getButton($this->l->getLg(), 'cron', 'active', $this->l->trad('CRON_ACTIVE'));
        $this->page .= '</div><br>';
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_6'));
        $this->page .= $this->getButton($this->l->getLg(), 'cron', 'server', $this->l->trad('CRON_SERVER'));
        $this->page .= '<br>';
        $this->page .= $this->getInfo($this->l->trad('CRON_INFO_7'));
        $this->page .= $this->getButton($this->l->getLg(), 'cron', 'direct', $this->l->trad('CRON_DIRECT'));
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function serverList($config, $active, $paramJson, $liveStation, $timeCron)
    {
        $zero = '&#8709;';
        $param = array(
            "1" => ($this->getLocalhost()) ? dirname(dirname(__FILE__)) . '/Model/cron/cron_server.php' : dirname(dirname(__FILE__)) . '\Model\cron\cron_server.php',
            "2" => ($config['config_crontime'] == '0') ? $zero : $config['config_crontime'] . ' mn',
            "_LG" => $this->l->getLg(),
            "SAVE" => $this->l->trad('SAVE'),
            "_URL" => 'index.php?controller=cron&action=list&',
        );
        $this->constructHead($param['_URL']);
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('CRON_SERVER') . '</h1>';
        $this->page .= $this->getListCronServer($param);
        $this->page .= '<form action="index.php?controller=cron&action=timer&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= '<div class="container-fluid conteneur_row_pref px-0">';
        $this->page .= $this->getSelectCron($config, $active);
        $this->page .= '</div>';
        $this->page .= $this->getSubmit('pref', $param['SAVE']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('CRON_SERVER_TITLE') . '</h1>';
        $this->page .= $this->getInfo($this->l->trad('CRON_SERVER_1'));
        $this->page .= $this->getInfo($this->l->trad('CRON_SERVER_2'));
        $this->page .= $this->getInfo($this->l->trad('CRON_SERVER_3'));
        $this->page .= $this->getInfo($this->l->trad('CRON_SERVER_4'));
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function directList($config, $active, $paramJson, $liveStation, $timeCron)
    {
   
        $param = array(
            "1" => '<a href="https://cron-job.org/en/">cron-job.org</a>',
            "2" => $this->url().'Model/cron/cron_direct.php',    
            "3" => ($active['stat_type'] == 'live') ? '15mn' : '10mn',        
            "_URL" => 'index.php?controller=cron&action=list&',
        );
        $this->constructHead($param['_URL']);
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('CRON_DIRECT') . '</h1>';
        $this->page .= $this->getListCronDirect($param);
        $this->page .= '</section>';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('CRON_DIRECT_TITLE') . '</h1>';
        $this->page .= $this->getInfo($this->l->trad('CRON_DIRECT_1'));
        $this->page .= $this->getInfo($this->l->trad('CRON_DIRECT_2'));
        $this->page .= $this->getInfo($this->l->trad('CRON_DIRECT_3'));
        $this->page .= $this->getInfo($this->l->trad('CRON_DIRECT_4'));
        $this->page .= $this->getInfo($this->l->trad('CRON_DIRECT_5'));
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function getSelectCron($config, $active)
    {
        $page = $this->searchHTML('selectCron', 'cron');
        $page = str_replace('{CHOOSE_SELECT}',  $this->l->trad('CHOOSE_SELECT'), $page);
        $page = str_replace('{DELETE}', $this->l->trad('DELETE_INFO'), $page);
        $page = str_replace('{CRON_OPTION}', $this->getOptionCron($active), $page);
        $page = str_replace('{_CRON_ID}', $config['config_id'], $page);
        return $page;
    }

    public function getOptionCron($active)
    {
        $page = '';
        if ($active['stat_type'] == 'v1' || $active['stat_type'] == 'v2') {
            $page .=  '<option value="20">20 mn</option>';
        }
        $page .=  '<option value="30">30 mn</option>';
        $page .=  '<option value="60">60 mn</option>';
        return $page;
    }

    public function getLocalhost()
    {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            return true;
        } else {
            return false;
        }
    }

    public function getListCronServer($param)
    {
        $this->page .= $this->searchHTML('listCronServer', 'cron');
        $this->page = str_replace('{_CRON_PATH}',  $param['1'], $this->page);
        $this->page = str_replace('{_CRONTAB_TIME}',  $param['2'], $this->page);
        $this->page = str_replace('{CRON_PATH}',  $this->l->trad('CRON_PATH'), $this->page);
        $this->page = str_replace('{CRONTAB_TIME}',  $this->l->trad('CRONTAB_TIME'), $this->page);
    }

    public function getListCronDirect($param)
    {
        $this->page .= $this->searchHTML('listCronDirect', 'cron');
        $this->page = str_replace('{_CRON-JOB_URL}',  $param['1'], $this->page);
        $this->page = str_replace('{_CRON_PATH}',  $param['2'], $this->page);
        $this->page = str_replace('{_CRONTAB_TIME}', $param['3'], $this->page);
        $this->page = str_replace('{CRON-JOB_URL}',  $this->l->trad('CRON-JOB_URL'), $this->page);        
        $this->page = str_replace('{CRON_PATH}',  $this->l->trad('CRON_PATH'), $this->page);
        $this->page = str_replace('{CRONTAB_TIME}',  $this->l->trad('CRONTAB_TIME'), $this->page);
    }
   
    public function getListInfoCron($param)
    {
        $this->page .= $this->searchHTML('listInfoCron', 'cron');
        $this->page = str_replace('{_STATION_TYPE}',  $param['1'], $this->page);
        $this->page = str_replace('{_STATION_LOCATION}',  $param['2'], $this->page);
        $this->page = str_replace('{_CRON_STATUS}',  $param['3'], $this->page);
        $this->page = str_replace('{_CRON_TIMER}',  $param['4'], $this->page);
        $this->page = str_replace('{_API_TIME}',  $param['5'], $this->page);
        $this->page = str_replace('{_CRON_TYPE}',  $param['6'], $this->page);
        $this->page = str_replace('{STATION_TYPE}',  $this->l->trad('STATION_TYPE'), $this->page);
        $this->page = str_replace('{STATION_LOCATION}',  $this->l->trad('STATION_LOCATION'), $this->page);
        $this->page = str_replace('{CRON_STATUS}',  $this->l->trad('CRON_STATUS'), $this->page);
        $this->page = str_replace('{CRON_TIMER}',  $this->l->trad('CRON_TIMER'), $this->page);
        $this->page = str_replace('{CRON_TYPE}',  $this->l->trad('CRON_TYPE'), $this->page);
    }

    public function alertCron($dial, $lg, $url)
    {
        $page = '<script type="text/javascript">';
        $page .= ' alert("' . $dial . '");';
        $page .= 'window.location.replace("index.php?' . $url . '&lg=' . $lg . '");';
        $page .= '</script>';
        echo $page;
    }




public function url(){
    return sprintf(
      "%s://%s%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['SERVER_NAME'],
      rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/'
    );
  }


  public function cronType($type){
    switch ($type) {
        case '0':
            $rep = $this->l->trad('CRON_TYPE_0');
            break;
        case '1':
            $rep = $this->l->trad('CRON_TYPE_1');
            break;
        case '2':
            $rep = $this->l->trad('CRON_TYPE_2');
            break;
        case '3':
            $rep = $this->l->trad('CRON_TYPE_3');
            break;
        default:
        $rep = $this->l->trad('CRON_TYPE_0');
    }
    return $rep;
  }

}
