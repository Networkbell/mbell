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

        while ($this->getConfigActiveCron()['config_cron'] == 1 && $this->getConfigActiveCron()['config_crontime'] == 0) {

            //on va chercher les valeurs dans la boucle plutôt que le controller en amont pour que les données de l'API se mettent bien à jour une fois le cron lancé
            $station = $this->paramStat->getStationActive();
            $type = (isset($station['stat_type'])) ? $station['stat_type'] : null;
            $livenbr = ($type == 'live') ? $station['stat_livenbr'] - 1 : 0;
            $livetab = 0; // pas besoin ici

            $liveStation1 = ($type == 'live') ? $this->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
            $datas1 = $this->paramStat->getAPI();
            $dateString = $this->stationview->getAPIDatasUp($datas1, $station, $liveStation1, $livenbr, $livetab)['time'];

            $time_sleep = ($type == 'live') ? 780 : 480; // 780 = 13 minutes / 480 = 8 minutes
            $time_precision = ($type == 'live') ? 15 : 10; // API v2 = 15mn / API v1 = 10mn
            $time_deviation = ($type == 'live' || $type == 'weewx') ? 0 : $this->waitDeviationCron($dateString, $time_precision); // pas de déviation avec API v2 car on utilise time() dans l'API

            $time = $this->waitactiveCron($dateString, $time_precision) + $time_deviation;

            time_sleep_until($time);

            $datas2 = $this->paramStat->getAPI(); //on remet à jour les datas après le temps d'attente
            $liveStation2 = ($type == 'live') ? $this->getLiveAPIStation($station['stat_livekey'], $station['stat_livesecret']) : '';
            $response = $this->addWeather($datas2, $station, $liveStation2, $livenbr);
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
            $this->requete = $this->connexion->query("SET SESSION WAIT_TIMEOUT=3300"); //55mn
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':config_id', $config['config_id']);
            $this->requete->bindParam(':config_cron', $status_cron);
            $result = $this->requete->execute();
            $row = ($result) ? 1 : null;
            $this->close($this->connexion, $this->requete);
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

        $req = "SELECT config_id, config_cron, config_crontime, $config_statid FROM $config_tab INNER JOIN $station_tab ON $config_statid = $stat_statid WHERE stat_active = :stat_active";

        try {
            $this->requete = $this->connexion->query("SET SESSION WAIT_TIMEOUT=3300"); //55mn
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':stat_active', $stat_active);

            $result = $this->requete->execute();
            $list = array();
            if ($result) {
                $list = $this->requete->fetch(PDO::FETCH_ASSOC);
            }
            $this->close($this->connexion, $this->requete);
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
     * Pour WL avec plusieurs capteurs -> permet l'enregistrement mb_data CRON avec data unique
     * Ne garde que les choix uniques dans la bdd mb_tab 
     * 1. Tri en fonction du nombre de ligne choisi (array_slice)
     * 2. Tri sur le 1er paramètre. Doit être unique (en gardant en info le 2e parametre associé). Exemple : 1 seule valeur de vent moyen
     * 3. On ne garde que la référence unique la plus faible dans le nombre de capteur. Exemple : si vent moyen capteur 1 et 2 existent, on garde seulement le capteur 1
     * 4. le Tri sur le capteur se fait sur le 1er trouvé (foreach traite à l'envers en gardant le dernier d'où array_reverse)
     * 5. on donne comme clef le type de case et comme valeur le capteur associé
     */
    public function uniqueArrayTab($tab)
    {
        $tab_line = ($tab['tab_lines'] * 3);
        unset($tab['tab_id']);
        unset($tab['tab_lines']);
        unset($tab['stat_id']);
        $array = $newarray = $finalarray = array();
        $slice = array_slice($tab, 0, $tab_line);
        foreach ($slice as $key => $value) {
            $nexplod = explode('-', $value);
            $ltab = $nexplod[0];
            $itab = $nexplod[1];
            $new_array = array($ltab, $itab);
            $array[$key] = $new_array;
        }
        foreach (array_reverse($array) as $v) {
            $newarray[$v[0]] = $v;
        }
        $data = array_values($newarray);
        foreach ($data as $i) {
            $finalarray[$i[0]] = $i[1];
        }
        return $finalarray;
    }

    /**
     * Ajout dans la BDD datas
     *
     * @param [array] $datas API général
     * @param [array] $station BDD mb_stat
     * @param [array] $livestation pour Live API stations
     * @return boolean
     */
    public function addWeather($datas, $station, $livestation, $livenbr)
    {
        require $this->file_admin;
        $data_tab = $table_prefix . 'data';

        //temps serveur
        $data_time_cron = time();

        //
        $station = $this->paramStat->getStationActive();
        $type = (isset($station['stat_type'])) ? $station['stat_type'] : null;

        // pour WL avec plusieurs sensors, on garde que le 1er capteur actif
        $tab = $this->getTabActive();
        $uniquetab = $this->uniqueArrayTab($tab);

        // test existance itab et variantes
        $itabtemp = $uniquetab[2] ?? '0';
        $itabheat = $uniquetab[5] ?? ($uniquetab[2] ?? '0');
        $itabwindchill = $uniquetab[5] ?? ($uniquetab[2] ?? '0');
        $itabdewpoint = $uniquetab[11] ?? '0';
        $itabhum = $uniquetab[12] ?? '0';
        $itabpress = $uniquetab[10] ?? '0';
        $itabwinddir = $uniquetab[1] ?? ($uniquetab[46] ?? '0');
        $itabwindmoy = $uniquetab[1] ?? '0';
        $itabwindraf = $uniquetab[4] ?? '0';
        $itabrainday = $uniquetab[6] ?? ($uniquetab[46] ?? '0');
        $itabrrhigh15mn = $uniquetab[3] ?? '0';
        $itabrrhighhour = $uniquetab[3] ?? '0';
        $itabrrlast = $uniquetab[3] ?? '0';
        $itabsolar = $uniquetab[8] ?? ($uniquetab[22] ?? '0');
        $itabuv = $uniquetab[8] ?? ($uniquetab[23] ?? '0');

        //API avec WL (+itab)
        $WLctemp = $this->stationview->getAPIDatasUp($datas, $station, $livestation, $livenbr, $itabtemp);
        $WLfheat = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabheat);
        $WLfwindchill = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabwindchill);
        $WLfdewpoint = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabdewpoint);
        $WLhum = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabhum);
        $WLpress = $this->stationview->getAPIDatasUp($datas, $station, $livestation, $livenbr, $itabpress);
        $WLwinddir = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabwinddir);
        $WLwindmoy = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabwindmoy);
        $WLwindraf = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabwindraf);
        $WLrainday = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabrainday);
        $WLrrhigh15mn = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabrrhigh15mn);
        $WLrrhighhour = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabrrhighhour);
        $WLrrlast = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabrrlast);
        $WLsolar = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabsolar);
        $WLuv = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, $itabuv);

        //API sans WL
        $apiDatasUP = $this->stationview->getAPIDatasUp($datas, $station, $livestation, $livenbr, '0');
        $apiDatas = $this->stationview->getAPIDatas($datas, $station, $livestation, $livenbr, '0');

        //temps API
        $time_precision = ($type == 'live') ? 15 : 10;
        $time = $apiDatasUP['time'];
        $timeZone = $apiDatasUP['fuseau'];
        $dt = new DateTime($time);
        // API live ne se met à jour que toutes les 15mn et donne un ts = entre 2 valeurs de 15mn, donc on arrondis avec ceil (peut générer un décalage de plus de 5-10mn par rapport à time_cron)
        // API v1 le décalage est minime mais est plus variable + ou - (donc on arrondis avec round)
        $tRound = ($type == 'live') ? $this->roundToNextMin($dt, $time_precision)  : $this->RoundMin($dt, $time_precision);
        $tstamp =  $tRound->getTimestamp();
        $data_time_api = $this->DateCreateCron($tstamp, $timeZone);
        //$data_time_api = $time; // pour heure exacte

        $data_temp = $this->floaVal(($type == 'live') ? $WLctemp['c_temp'] : $apiDatasUP['c_temp']);
        $data_heat = $this->floaVal($this->stationview->getTempFtoC(($type == 'live') ? $WLfheat['heat_index_f'] : $apiDatas['heat_index_f']));
        $data_windchill = $this->floaVal($this->stationview->getTempFtoC(($type == 'live') ? $WLfwindchill['windchill_f'] : $apiDatas['windchill_f']));
        $data_dewpoint = $this->floaVal($this->stationview->getTempFtoC(($type == 'live') ? $WLfdewpoint['dewpoint_f'] : $apiDatas['dewpoint_f']));
        $data_hum = $this->floaVal(($type == 'live') ? $WLhum['relative_humidity'] : $apiDatas['relative_humidity']);
        $data_press = $this->floaVal(($type == 'live') ? $WLpress['mb_pressure'] : $apiDatasUP['mb_pressure']);
        $data_wind_dir = $this->floaVal(($type == 'live') ? $WLwinddir['wind_degrees'] : $apiDatas['wind_degrees']);
        $data_wind_moy = $this->floaVal($this->stationview->getWindMphToKph(($type == 'live') ? $WLwindmoy['wind_ten_min_avg_mph'] : $apiDatas['wind_ten_min_avg_mph']));
        $data_wind_raf = $this->floaVal($this->stationview->getWindMphToKph(($type == 'live') ? $WLwindraf['wind_ten_min_gust_mph'] : $apiDatas['wind_ten_min_gust_mph']));
        $data_rain_day = $this->floaVal($this->stationview->getRainInToMm(($type == 'live') ? $WLrainday['rain_day_in'] : $apiDatas['rain_day_in']));
        $data_rr_high_15mn = $this->floaVal(($type == 'live') ? $WLrrhigh15mn['rain_rate_hi_last_15_min_mm'] : $apiDatas['rain_rate_hi_last_15_min_mm']);
        $data_rr_high_hour = $this->floaVal($this->stationview->getRainInToMm(($type == 'live') ? $WLrrhighhour['rain_rate_hour_high_in_per_hr'] : $apiDatas['rain_rate_hour_high_in_per_hr']));
        $data_rr_last = $this->floaVal($this->stationview->getRainInToMm(($type == 'live') ? $WLrrlast['rain_rate_in_per_hr'] : $apiDatas['rain_rate_in_per_hr']));
        $data_solar = $this->floaVal(($type == 'live') ? $WLsolar['solar_radiation'] : $apiDatas['solar_radiation']);
        $data_uv = $this->floaVal(($type == 'live') ? $WLuv['uv_index'] : $apiDatas['uv_index']);

        /*  var_dump($data_time_cron);
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
            $this->requete = $this->connexion->query("SET SESSION WAIT_TIMEOUT=3300"); //55mn
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
            $this->close($this->connexion, $this->requete);
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

        try {
            $req = "SELECT data_time_cron FROM $data_tab ORDER BY data_id DESC LIMIT 1";
            $this->requete = $this->connexion->query("SET SESSION WAIT_TIMEOUT=3300"); //55mn
            $this->requete = $this->connexion->prepare($req);
            $result = $this->requete->execute();
            $list = array();
            if ($result) {
                $list = $this->requete->fetch(PDO::FETCH_ASSOC);
            }
            $this->close($this->connexion, $this->requete);
            $row = ($list) ? $list : null;
            return $row;
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


    /**
     * Modification dans la BDD "config" de config_crontime
     * 
     * @return boolean
     */
    public function updateCron($post)
    {
        require $this->file_admin;
        $tab_config = $table_prefix . 'config';
        try {
            $req = "UPDATE $tab_config SET config_crontime = :config_crontime WHERE config_id = :config_id";
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':config_id', $post['config_id']);
            $this->requete->bindParam(':config_crontime', $post['config_crontime']);
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
}
