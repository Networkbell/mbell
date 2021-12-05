<?php

abstract class Model
{

        protected $connexion;
        protected $requete;
        protected $file_admin;
        protected $paramStat;

        public function __construct()
        {
                $this->l = new Lang();
                $this->file_admin = 'config/admin.php';
                
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

     /**
     * Type de station
     * 
     * @return string
     */
        public function getStation()
        {
                $station = (isset($_GET['station']) ? ((in_array($_GET['station'], array('v0', 'v1', 'v2'))) ? $_GET['station'] : 'v0') : 'v0');
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
        $stat_id = $info['stat_id'];

        $req = "INSERT INTO $config_tab VALUES(NULL, :config_lang, :config_sun, :config_aux1, :config_aux2, :config_aux3, :config_temp, :config_wind, :config_rain, :config_press, :config_css, :config_daynight, :config_color, :config_icon, :stat_id)";
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

        //DEFAULT VALUE
        $tab_lines = 4;
        $tab_1a = 1;
        $tab_1b = 2;
        $tab_1c = 3;
        $tab_2a = 4;
        $tab_2b = 5;
        $tab_2c = 6;
        $tab_3a = 7;
        $tab_3b = 8;
        $tab_3c = 9;
        $tab_4a = 10;
        $tab_4b = 11;
        $tab_4c = 12;
        $tab_5a = 43;
        $tab_5b = 44;
        $tab_5c = 13;
        $tab_6a = 14;
        $tab_6b = 15;
        $tab_6c = 16;
        $tab_7a = 30;
        $tab_7b = 31;
        $tab_7c = 24;
        $tab_8a = 25;
        $tab_8b = 37;
        $tab_8c = 38;
        $tab_9a = 26;
        $tab_9b = 27;
        $tab_9c = 28;
        $tab_10a = 39;
        $tab_10b = 40;
        $tab_10c = 41;
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



}
