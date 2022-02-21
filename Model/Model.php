<?php

abstract class Model
{

    protected $connexion;
    protected $requete;
    protected $file_admin;
    protected $paramStat;

    public function __construct()
    {
        $this->dispatcher = new Dispatcher;
        $this->l = new Lang();
        $this->file_admin = dirname(dirname(__FILE__)) . '/config/admin.php';

        //CONNEXION BDD
        if (file_exists($this->file_admin)) {
            require $this->file_admin;
            try {
                $this->connexion = new PDO("mysql:host=" .
                    DB_HOST . ";dbname=" .
                    DB_NAME, DB_USER, DB_PASSWORD);
                // Activation des erreurs PDO
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // mode de fetch par défaut : FETCH_ASSOC / FETCH_OBJ / FETCH_BOTH
                //$this->connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                if ($_GET['controller'] == "install") {
                    if (MB_DEBUG) {
                        die($e->getMessage());
                    }
                    unlink($this->file_admin);
                    $response = 2;
                    return ($response);
                }
                if (MB_DEBUG) {
                    die($e->getMessage());
                }
            }
        }
    }

    public function __destruct()
    {
        try {
            $this->connexion = null; //Closes connection
            $this->requete = null;
        } catch (PDOException $e) {
            if (MB_DEBUG) {
                die($e->getMessage());
            }
        }
    }

    public function close($connexion, $requete)
    {
        try {
            $connexion = null; //Closes connection
            $requete = null;
        } catch (PDOException $e) {
            if (MB_DEBUG) {
                die($e->getMessage());
            }
        }
    }



    /**
     * Type de station
     * 
     * @return string
     */
    public function getStation()
    {
        $station = (isset($_GET['station']) ? ((in_array($_GET['station'], array('v0', 'v1', 'v2', 'live', 'weewx'))) ? $_GET['station'] : 'v0') : 'v0');
        return $station;
    }



    /**
     * Créé une clef ssl 32bit aléatoire en hexa
     * 
     * @return string
     */
    public function hexaKey()
    {
        $crypt = bin2hex(openssl_random_pseudo_bytes(32));
        return $crypt;
    }


    /**
     * Créé une une clé de hachage avec "pepper"
     * 
     * @return string
     */
    public function pepperKey($paramPost)
    {
        require $this->file_admin;
        $pwd = $paramPost['user_password'];
        $pwd_peppered = hash_hmac("sha256", $pwd, KEY_CRYPT);
        return $pwd_peppered;
    }


    /**
     * Selection des éléments dans la BDD "user"
     * en fonction de la $_SESSION user en cours
     * ne fonctionne que si l'user est connecté
     * 
     * @return type(string, int)
     */
    public function getUserSession()
    {


        require $this->file_admin;
        $user_tab = $table_prefix . 'user';

        try {
            $this->requete = $this->connexion->prepare("SELECT * FROM $user_tab WHERE user_login = :user_login");
            $this->requete->bindParam(':user_login', $_SESSION['user_login']);
            $result = $this->requete->execute();
            $list = array();
            if ($result) {
                $list = $this->requete->fetch(PDO::FETCH_ASSOC);
            }
            return $list;
        } catch (Exception $e) {
            die(var_dump($e->getMessage()));
        }
    }


    /**
     * Ajout dans la BDD config avec valeurs par défaut
     * 
     * @return void
     */
    public function addConfig($info)
    {
        require $this->file_admin;
        $config_tab = $table_prefix . 'config';

        //DEFAULT VALUE
        $config_lang = $this->l->getLg();
        $config_sun = '0'; //0 - sun - uv - sun_uv
        $config_aux1 = 0;
        $config_aux2 = 0;
        $config_aux3 = 0;
        $config_temp = 'C';
        $config_wind = 'kph';
        $config_rain = 'mm';
        $config_press = 'hpa';
        $config_css = 'bluedark';
        $config_daynight = 0;
        $config_color = 'colored';
        $config_icon = 1;
        $config_cron = 0;
        $config_crontime = 0;
        $stat_id = $info['stat_id'];

        $req = "INSERT INTO $config_tab VALUES(NULL, :config_lang, :config_sun, :config_aux1, :config_aux2, :config_aux3, :config_temp, :config_wind, :config_rain, :config_press, :config_css, :config_daynight, :config_color, :config_icon, :config_cron, :config_crontime, :stat_id)";
        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':config_lang', $config_lang);
            $this->requete->bindParam(':config_sun', $config_sun);
            $this->requete->bindParam(':config_aux1', $config_aux1);
            $this->requete->bindParam(':config_aux2', $config_aux2);
            $this->requete->bindParam(':config_aux3', $config_aux3);
            $this->requete->bindParam(':config_temp', $config_temp);
            $this->requete->bindParam(':config_wind', $config_wind);
            $this->requete->bindParam(':config_rain', $config_rain);
            $this->requete->bindParam(':config_press', $config_press);
            $this->requete->bindParam(':config_css', $config_css);
            $this->requete->bindParam(':config_daynight', $config_daynight);
            $this->requete->bindParam(':config_color', $config_color);
            $this->requete->bindParam(':config_icon', $config_icon);
            $this->requete->bindParam(':config_cron', $config_cron);
            $this->requete->bindParam(':config_crontime', $config_crontime);
            $this->requete->bindParam(':stat_id', $stat_id);
            $result = $this->requete->execute();
            $row = ($result) ? $stat_id : null;
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
     * Ajout dans la BDD tab avec valeurs par défaut
     * 
     * @return void
     */
    public function addTable($info)
    {
        require $this->file_admin;
        $tab_tab = $table_prefix . 'tab';
        $type = $info['stat_type'];

        //DEFAULT VALUE
        $tab_lines = 4;
        $tab_1a = '1-0';
        $tab_1b = '2-0';
        $tab_1c = '3-0';
        $tab_2a = '4-0';
        $tab_2b = '5-0';
        $tab_2c = '6-0';
        $tab_3a = ($type == "live" || $type == "weewx") ? '12-0' : '7-0'; //12 hum relative / 7 +forte rafale
        $tab_3b = '8-0';
        $tab_3c = ($type == "live" || $type == "weewx") ? '13-0' : '9-0'; //13 precip mois / 9 évapo
        $tab_4a = '10-0';
        $tab_4b = '11-0';
        $tab_4c = ($type == "live" || $type == "weewx") ? '14-0' :  '12-0';  // 14 precip annuel / 12 hum relative
        $tab_5a = '43-0';
        $tab_5b = '44-0';
        $tab_5c = ($type == "live" || $type == "weewx") ? '9-0' : '13-0'; //9 évapo / 13 precip mois
        $tab_6a = ($type == "live" || $type == "weewx") ? '7-0' : '14-0'; //7 +forte rafale / 14 precip annuel
        $tab_6b = '15-0';
        $tab_6c = '16-0';
        $tab_7a = '30-0';
        $tab_7b = '31-0';
        $tab_7c = '24-0';
        $tab_8a = '25-0';
        $tab_8b = '37-0';
        $tab_8c = '38-0';
        $tab_9a = '26-0';
        $tab_9b = '27-0';
        $tab_9c = '28-0';
        $tab_10a = '39-0';
        $tab_10b = '40-0';
        $tab_10c = '41-0';
        $stat_id = $info['stat_id'];

        $req = "INSERT INTO $tab_tab VALUES(NULL, :tab_lines, :tab_1a, :tab_1b, :tab_1c, :tab_2a, :tab_2b, :tab_2c, :tab_3a, :tab_3b, :tab_3c, :tab_4a, :tab_4b, :tab_4c, :tab_5a, :tab_5b, :tab_5c, :tab_6a, :tab_6b, :tab_6c, :tab_7a, :tab_7b, :tab_7c, :tab_8a, :tab_8b, :tab_8c, :tab_9a, :tab_9b, :tab_9c,:tab_10a, :tab_10b, :tab_10c, :stat_id)";
        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':tab_lines', $tab_lines);
            $this->requete->bindParam(':tab_1a', $tab_1a);
            $this->requete->bindParam(':tab_1b', $tab_1b);
            $this->requete->bindParam(':tab_1c', $tab_1c);
            $this->requete->bindParam(':tab_2a', $tab_2a);
            $this->requete->bindParam(':tab_2b', $tab_2b);
            $this->requete->bindParam(':tab_2c', $tab_2c);
            $this->requete->bindParam(':tab_3a', $tab_3a);
            $this->requete->bindParam(':tab_3b', $tab_3b);
            $this->requete->bindParam(':tab_3c', $tab_3c);
            $this->requete->bindParam(':tab_4a', $tab_4a);
            $this->requete->bindParam(':tab_4b', $tab_4b);
            $this->requete->bindParam(':tab_4c', $tab_4c);
            $this->requete->bindParam(':tab_5a', $tab_5a);
            $this->requete->bindParam(':tab_5b', $tab_5b);
            $this->requete->bindParam(':tab_5c', $tab_5c);
            $this->requete->bindParam(':tab_6a', $tab_6a);
            $this->requete->bindParam(':tab_6b', $tab_6b);
            $this->requete->bindParam(':tab_6c', $tab_6c);
            $this->requete->bindParam(':tab_7a', $tab_7a);
            $this->requete->bindParam(':tab_7b', $tab_7b);
            $this->requete->bindParam(':tab_7c', $tab_7c);
            $this->requete->bindParam(':tab_8a', $tab_8a);
            $this->requete->bindParam(':tab_8b', $tab_8b);
            $this->requete->bindParam(':tab_8c', $tab_8c);
            $this->requete->bindParam(':tab_9a', $tab_9a);
            $this->requete->bindParam(':tab_9b', $tab_9b);
            $this->requete->bindParam(':tab_9c', $tab_9c);
            $this->requete->bindParam(':tab_10a', $tab_10a);
            $this->requete->bindParam(':tab_10b', $tab_10b);
            $this->requete->bindParam(':tab_10c', $tab_10c);
            $this->requete->bindParam(':stat_id', $stat_id);
            $result = $this->requete->execute();
            $row = ($result) ? $stat_id : null;
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
     * Retourne l'URL de l'API Live station : https://api.weatherlink.com/v2/stations/
     * 
     * 
     */
    public function getLiveURLStation($key, $secret)
    {
        $parameters = array(
            "api-key" => $key,
            "api-secret" => $secret,
            "t" => time()
        );

        ksort($parameters);
        $apiSecret = $parameters["api-secret"];
        unset($parameters["api-secret"]);

        $data = "";
        foreach ($parameters as $key => $value) {
            $data = $data . $key . $value;
        }

        $apiSignature = hash_hmac("sha256", $data, $apiSecret);
        $apiStation = "https://api.weatherlink.com/v2/stations/?api-key=" . $parameters["api-key"] . "&api-signature=" . $apiSignature . "&t=" . $parameters["t"];

        return $apiStation;
    }



    /**
     * Retourne les datas dans l'URL Live station : https://api.weatherlink.com/v2/stations/
     * 
     *
     */
    public function getLiveAPIStation($key, $secret)
    {
        $data = @file_get_contents($this->getLiveURLStation($key, $secret));
        $json = json_decode($data, true);
        $this->jsonDebug();
        return $json;
    }

    /**
     * Retourne l'id_station dans l'URL Live station : https://api.weatherlink.com/v2/stations/ si le stat_type est "weatherlink live"
     * Attention le json retourné est sous forme d'array
     * 
     *
     */
    public function getStationID($key, $secret, $type, $nbr)
    {
        $zero = '';
        $nombre = $nbr - 1;
        if ($type == 'live') {
            $stationDatas = $this->getLiveAPIStation($key, $secret);

            $station_id = isset($stationDatas['stations'][$nombre]['station_id']) ? $stationDatas['stations'][$nombre]['station_id'] : $zero;
        } else {
            $station_id = $zero;
        }
        return $station_id;
    }



    public function jsonDebug()
    {
        require $this->file_admin;
        if (MB_DEBUG) {
            if (json_last_error()) {
                $jsDebug = '';
                switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                        $jsDebug .= ' - Aucune erreur';
                        break;
                    case JSON_ERROR_DEPTH:
                        $jsDebug .= ' - Profondeur maximale atteinte';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $jsDebug .= ' - Inadéquation des modes ou underflow';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $jsDebug .= ' - Erreur lors du contrôle des caractères';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $jsDebug .= " - Erreur de syntaxe ; JSON malformé ; Erreur d'API ; Mauvais identifiants vers votre API";
                        break;
                    case JSON_ERROR_UTF8:
                        $jsDebug .= ' - Caractères UTF-8 malformés, probablement une erreur d\'encodage';
                        break;
                    default:
                        $jsDebug .= ' - Erreur inconnue';
                        break;
                }
                return var_dump($jsDebug);
            }
        }
    }


    /**
     * Selection d'un élément dans la BDD config et de la station associé
     * 
     * @return type(string, int)
     */
    public function getConfigActive()
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $config_tab = $table_prefix . 'config';
        $stat_statid = $station_tab . '.stat_id';
        $config_statid = $config_tab . '.stat_id';
        $stat_active = 1;

        $req = "SELECT config_id, config_lang, config_sun, config_aux1, config_aux2, config_aux3, config_temp, config_wind, config_rain, config_press, config_css, config_daynight, config_color, config_icon, config_cron, config_crontime, $config_statid FROM $config_tab INNER JOIN $station_tab ON $config_statid = $stat_statid WHERE stat_active = :stat_active";

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
 * Selection d'un élément dans la BDD tab de la station associé
 *
 * @return array
 */
    public function getTabActive()
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $tab_tab = $table_prefix . 'tab';
        $stat_statid = $station_tab . '.stat_id';
        $tab_statid = $tab_tab . '.stat_id';
        $stat_active = 1;

        $req = "SELECT tab_id, tab_lines, tab_1a, tab_1b, tab_1c, tab_2a, tab_2b, tab_2c, tab_3a, tab_3b, tab_3c, tab_4a, tab_4b, tab_4c, tab_5a, tab_5b, tab_5c, tab_6a, tab_6b, tab_6c, tab_7a, tab_7b, tab_7c, tab_8a, tab_8b, tab_8c, tab_9a, tab_9b, tab_9c, tab_10a, tab_10b, tab_10c, $tab_statid FROM $tab_tab INNER JOIN $station_tab ON $tab_statid = $stat_statid WHERE stat_active = :stat_active";

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
            if (MB_DEBUG) {
                die($e->getMessage());
            }
        }
    }
}
