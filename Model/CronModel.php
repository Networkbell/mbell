<?php
class CronModel extends Model
{
    public function __construct()
    {
        session_write_close(); //permet de débloquer le script avec la boucle infini généré par le cron pour permettre aux autres scripts de s'exécuter

        parent::__construct();
        $this->stationview = new StationView();
        $this->paramStat = new StationModel();
    }

    /**
     * permet d'arondir le 1er temps d'attente suivant (+)
     *
     * @param \DateTime $dt
     * @param [int] $precision
     * @return \DateTime
     */
    public function roundToNextMin(\DateTime $dt, $precision)
    {
        $s = $precision * 60;
        $dt->setTimestamp($s * (int) ceil($dt->getTimestamp() / $s));
        return $dt;
    }

    /**
     * permet d'arondir le temps (+ ou -)
     *
     * @param \DateTime $dt
     * @param [int] $precision
     * @return \DateTime
     */
    public function RoundMin(\DateTime $dt, $precision)
    {
        $s = $precision * 60;
        $dt->setTimestamp($s * (int) round($dt->getTimestamp() / $s));
        return $dt;
    }

    /**
     * Création de date à partir de timestamp pour CRON
     *
     * @param [int] $datas
     * @param [string] $timeZone
     * @return string
     */
    public function DateCreateCron($datas, $timeZone)
    {
        if (isset($datas)) {
            date_default_timezone_set($timeZone);
            $date = new DateTime();
            $date->setTimestamp($datas);
            $date_time =  $date->format('d.m.Y - H:i');
        } else {
            $date_time =  '&#8709;';
        }
        return $date_time;
    }

    /**
     * Créer un cron à heure juste (10mn/15mn)
     *
     * @param [string] $dateString
     * @param [int] $time_precision
     * @return float
     */
    public function waitactiveCron($dateString, $time_precision)
    {
        $t1 = new DateTime($dateString);
        // $time1 = $t1->getTimestamp();

        $t2 = $this->roundToNextMin($t1, $time_precision);
        $t2stamp = $t2->getTimestamp();
        $time = floatval($t2stamp);
        return $time;
    }

    /**
     * Calcul de la déviation entre temps API et temps serveur
     *
     * @param [string] $T1stringTime
     * @param [int] $time_precision
     * @return float
     */
    public function waitDeviationCron($T1stringTime, $time_precision)
    {
        //temps API
        $t1T = new DateTime($T1stringTime);
        $t1Dep = $t1T->getTimestamp();
        $t1Next =  $this->waitactiveCron($T1stringTime, $time_precision);
        $attente1 = $t1Next - $t1Dep;

        //temps Serveur
        $t2Dep = time();
        $date = new DateTime();
        $timeZone = $date->getTimezone();
        $fuseau =  $timeZone->getName();
        $T2stringTime = $this->stationview->liveDateRFC822($t2Dep, $fuseau);
        $t2Next =  $this->waitactiveCron($T2stringTime, $time_precision);
        $attente2 = $t2Next - $t2Dep;

        //$a = $t2Dep-$t1Dep; //différence temps API-SERVEUR
        $b = $attente1 - $attente2; //différence temps Attente API-SERVEUR
        //$c = $t2Dep+$attente2 ; //Temps Serveur + attente serveur = temps rond réel
        //$d = $t1Dep+$attente2+$b; //Temps API + attente serveur + différence temps Attente API-SERVEUR  = temps rond API (=$t1Next)
        return floatval($b + 10); //on ajoute 10 sec pour temps de calcul maximum Mbell et éviter de se retrouver à h:00 pile ou juste avant à quelque secondes près
    }



    /**
     * Boucle Cron, 
     * le 1er temps d'attente corrige les déviations. 
     * Le 2eme, on attend 8 minutes avant de relancer le 1er
     *
     * @return boolean
     */
    public function activateCron()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        while ($this->getConfigActiveCron()['config_cron'] == 1) {

            //on va chercher les valeurs dans la boucle plutôt que le controller en amont pour que les données de l'API se mettent bien à jour une fois le cron lancé
            $station = $this->paramStat->getStationActive();
            $type = (isset($station['stat_type'])) ? $station['stat_type'] : null;

            $liveStation1 = ($type == 'live') ? $this->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
            $datas1 = $this->paramStat->getAPI();
            $dateString = $this->stationview->getAPIDatasUp($datas1, $station, $liveStation1)['time'];

            $time_sleep = ($type == 'live') ? 780 : 480; // 480 = 8 minutes / 780 = 13 minutes
            $time_precision = ($type == 'live') ? 15 : 10; // API v2 = 15mn / API v1 = 10mn
            $time_deviation = ($type == 'live') ? 0 : $this->waitDeviationCron($dateString, $time_precision); // pas de déviation avec API v2 car on utilise time() dans l'API

            $time = $this->waitactiveCron($dateString, $time_precision) + $time_deviation;

            time_sleep_until($time);

            $datas2 = $this->paramStat->getAPI(); //on remet à jour les datas après le temps d'attente
            $liveStation2 = ($type == 'live') ? $this->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
            $response = $this->addWeather($datas2, $station, $liveStation2);
            sleep($time_sleep);
        }
        return $response;
    }

    /**
     * Activation/désactivation dans la BDD "config" de config_cron
     *
     * @param [array] $config
     * @param [boolean] $status_cron
     * @return boolean
     */
    public function UpdateConfigCron($config, $status_cron)
    {

        require $this->file_admin;
        $config_tab = $table_prefix . 'config';

        $req = "UPDATE $config_tab SET config_cron = :config_cron WHERE config_id = :config_id";

        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':config_id', $config['config_id']);
            $this->requete->bindParam(':config_cron', $status_cron);
            $result = $this->requete->execute();
            $row = ($result) ? 1 : null;
        } catch (Exception $e) {
            if (MB_DEBUG) {
                var_dump($e->getMessage());
            }
            $row = null;
        }
        return $row;
    }


    /**
     * Selection de l'élément config_cron dans la BDD config et de la station associé active
     *
     * @return array
     */
    public function getConfigActiveCron()
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $config_tab = $table_prefix . 'config';
        $stat_statid = $station_tab . '.stat_id';
        $config_statid = $config_tab . '.stat_id';
        $stat_active = 1;

        $req = "SELECT config_id, config_cron, $config_statid FROM $config_tab INNER JOIN $station_tab ON $config_statid = $stat_statid WHERE stat_active = :stat_active";

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
 * Transforme en float les résultats (0 différent de null)
 *
 * @param [int, string, float] $data
 * @return float
 */
    public function floaVal($data)
    {
        if ($data == '&#8709;' && ($data != '0' || $data != 0.0 || $data != 0)) {
            $rep = null;
        } else {
            if ($data == '0' || $data == 0.0 || $data == 0) {
                $rep = 0.0;
            } else {
                $rep = floatval($data);
            }
        }
        return $rep;
    }

    /**
     * Ajout dans la BDD datas
     *
     * @param [array] $datas API général
     * @param [array] $station BDD mb_stat
     * @param [array] $livestation pour Live API stations
     * @return boolean
     */
    public function addWeather($datas, $station, $livestation)
    {

        require $this->file_admin;
        $data_tab = $table_prefix . 'data';

        //temps serveur
        $data_time_cron = time();

        //on arrondi le temps affiché 
        $station = $this->paramStat->getStationActive();
        $type = (isset($station['stat_type'])) ? $station['stat_type'] : null;

        //temps API
        $time_precision = ($type == 'live') ? 15 : 10;
        $time = $this->stationview->getAPIDatasUp($datas, $station, $livestation)['time'];
        $timeZone = $this->stationview->getAPIDatasUp($datas, $station, $livestation)['fuseau'];
        $dt = new DateTime($time);
        // API live ne se met à jour que toutes les 15mn et donne un ts = entre 2 valeurs de 15mn, donc on arrondis avec ceil (peut générer un décalage de plus de 5-10mn par rapport à time_cron)
        // API v1 le décalage est minime mais est plus variable + ou - (donc on arrondis avec round)
        $tRound = ($type == 'live') ? $this->roundToNextMin($dt, $time_precision)  : $this->RoundMin($dt, $time_precision);
        $tstamp =  $tRound->getTimestamp();
        $data_time_api = $this->DateCreateCron($tstamp, $timeZone);
        //$data_time_api = $time; // pour heure exacte

        $data_temp = $this->floaVal($this->stationview->getAPIDatasUp($datas, $station, $livestation)['c_temp']);
        $data_heat = $this->floaVal($this->stationview->getTempFtoC($this->stationview->getAPIDatas($datas, $station, $livestation)['heat_index_f']));
        $data_windchill = $this->floaVal($this->stationview->getTempFtoC($this->stationview->getAPIDatas($datas, $station, $livestation)['windchill_f']));
        $data_dewpoint = $this->floaVal($this->stationview->getTempFtoC($this->stationview->getAPIDatas($datas, $station, $livestation)['dewpoint_f']));
        $data_hum = $this->floaVal($this->stationview->getAPIDatas($datas, $station, $livestation)['relative_humidity']);
        $data_press = $this->floaVal($this->stationview->getAPIDatasUp($datas, $station, $livestation)['mb_pressure']);
        $data_wind_dir = $this->floaVal($this->stationview->getAPIDatas($datas, $station, $livestation)['wind_degrees']);
        $data_wind_moy = $this->floaVal($this->stationview->getWindMphToKph($this->stationview->getAPIDatas($datas, $station, $livestation)['wind_ten_min_avg_mph']));
        $data_wind_raf = $this->floaVal($this->stationview->getWindMphToKph($this->stationview->getAPIDatas($datas, $station, $livestation)['wind_ten_min_gust_mph']));
        $data_rain_day = $this->floaVal($this->stationview->getRainInToMm($this->stationview->getAPIDatas($datas, $station, $livestation)['rain_day_in']));
        $data_rr_high_15mn = $this->floaVal($this->stationview->getAPIDatas($datas, $station, $livestation)['rain_rate_hi_last_15_min_mm']);
        $data_rr_high_hour = $this->floaVal($this->stationview->getRainInToMm($this->stationview->getAPIDatas($datas, $station, $livestation)['rain_rate_hour_high_in_per_hr']));
        $data_rr_last = $this->floaVal($this->stationview->getRainInToMm($this->stationview->getAPIDatas($datas, $station, $livestation)['rain_rate_in_per_hr']));
        $data_solar = $this->floaVal($this->stationview->getAPIDatas($datas, $station, $livestation)['solar_radiation']);
        $data_uv = $this->floaVal($this->stationview->getAPIDatas($datas, $station, $livestation)['uv_index']);
        /*
        var_dump($data_time_cron);
        var_dump($data_time_api);
        var_dump($data_temp);
        var_dump($data_press);
        var_dump($data_hum);
        var_dump($data_wind_dir);
        var_dump($data_wind_moy);
        var_dump($data_wind_raf);
        var_dump($data_heat);
        var_dump($data_windchill);
        var_dump($data_solar);
        var_dump($data_uv);
        var_dump($data_rain_day);
        var_dump($data_rr_high_15mn);
        var_dump($data_rr_high_hour);
        var_dump($data_rr_last);
        var_dump($data_dewpoint);
        var_dump($data_tab);
*/
        try {
            $req = "INSERT INTO $data_tab VALUES(
            NULL, :data_time_cron, :data_time_api, 
            :data_temp, :data_heat, :data_windchill,  
            :data_dewpoint, :data_hum, :data_press,
            :data_wind_dir, :data_wind_moy, :data_wind_raf,
            :data_rain_day, :data_rr_high_15mn, :data_rr_high_hour, :data_rr_last,
            :data_solar, :data_uv
            )";

            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':data_time_cron', $data_time_cron);
            $this->requete->bindParam(':data_time_api', $data_time_api);
            $this->requete->bindParam(':data_temp', $data_temp);
            $this->requete->bindParam(':data_heat', $data_heat);
            $this->requete->bindParam(':data_windchill', $data_windchill);
            $this->requete->bindParam(':data_dewpoint', $data_dewpoint);
            $this->requete->bindParam(':data_hum', $data_hum);
            $this->requete->bindParam(':data_press', $data_press);
            $this->requete->bindParam(':data_wind_dir', $data_wind_dir);
            $this->requete->bindParam(':data_wind_moy', $data_wind_moy);
            $this->requete->bindParam(':data_wind_raf', $data_wind_raf);
            $this->requete->bindParam(':data_rain_day', $data_rain_day);
            $this->requete->bindParam(':data_rr_high_15mn', $data_rr_high_15mn); //API v2
            $this->requete->bindParam(':data_rr_high_hour', $data_rr_high_hour); //API v1
            $this->requete->bindParam(':data_rr_last', $data_rr_last);
            $this->requete->bindParam(':data_solar', $data_solar);
            $this->requete->bindParam(':data_uv', $data_uv);


            $result = $this->requete->execute();
            $row = ($result) ? 1 : null;
            return $row;
        } catch (Exception $e) {
            if (MB_DEBUG) {
                var_dump($e->getMessage());
            }
            $row = null;
            return $row;
        }
    }

    /**
     * Selection du dernier élément data_time_cron dans la BDD data 
     *
     * @return array
     */
    public function getLastTimeCron()
    {

        require $this->file_admin;
        $data_tab = $table_prefix . 'data';
        $req = "SELECT data_time_cron FROM $data_tab ORDER BY data_id DESC LIMIT 1";

        try {
            $this->requete = $this->connexion->prepare($req);
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
     * Détecte si le cron est désactivé ou non
     *
     * @return boolean
     */
    public function IsServerCronDisabled()
    {
        $currentTime = time();
        $timedisabled = $currentTime - (60 * 30);
        $lastTime = (isset($this->getLastTimeCron()['data_time_cron'])) ? $this->getLastTimeCron()['data_time_cron'] : null;
        if ($lastTime <  $timedisabled && $lastTime != null) {
            $response = true;
        } else {
            $response = false;
        }
        return $response;
    }
}
