<?php


class InstallModel extends Model
{

    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Ajout dans la BDD user
     * 
     * @return void
     */
    public function addUser($paramPost)
    {

        require $this->file_admin;
        $user_tab = $table_prefix . 'user';
        $req = "INSERT INTO $user_tab VALUES(
            NULL, :user_login, :user_password, :user_email
            )";

        $pwd_peppered = $this->pepperKey($paramPost);
        $pwd_hashed = password_hash($pwd_peppered, PASSWORD_DEFAULT);

        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':user_login', $paramPost['user_login']);
            $this->requete->bindParam(':user_password', $pwd_hashed);
            $this->requete->bindParam(':user_email', $paramPost['user_email']);
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
     * Ajout dans la BDD station
     * 
     * @return void
     */
    public function addStation($paramPost)
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $stat_active = 1;

        try {
            $req = "INSERT INTO $station_tab VALUES(
            NULL, :stat_type, :stat_did, :stat_key, :stat_users, 
            :stat_password, :stat_token, :stat_active, :user_id
            )";

            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':stat_type', $paramPost['stat_type']);
            $this->requete->bindParam(':stat_did', $paramPost['stat_did']);
            $this->requete->bindParam(':stat_key', $paramPost['stat_key']);
            $this->requete->bindParam(':stat_users', $paramPost['stat_users']);
            $this->requete->bindParam(':stat_password', $paramPost['stat_password']);
            $this->requete->bindParam(':stat_token', $paramPost['stat_token']);
            $this->requete->bindParam(':stat_active', $stat_active);
            $this->requete->bindParam(':user_id', $paramPost['user_id']);

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


    






    public function addBDD($paramPost)
    {

        $file_source = 'config/admin_backup.php';


        // Tests sur "admin_backup.php" et chmod du FTP
        copy($file_source, $this->file_admin);
        if (!file_exists($this->file_admin)) {
            $response = 1;
        } else {

            // Injection dans admin.php des données du form bdd
            $file_content = file_get_contents($this->file_admin);
            $file_content = str_replace('{bdd_localhost}', $paramPost['hostbdd'], $file_content);
            $file_content = str_replace('{bdd_identifiant}', $paramPost['userbdd'], $file_content);
            $file_content = str_replace('{bdd_password}', $paramPost['passwordbdd'], $file_content);
            $file_content = str_replace('{bdd_mbell}', $paramPost['namebdd'], $file_content);
            $file_content = str_replace('{bdd_meta}', $paramPost['metabdd'], $file_content);
            $file_content = str_replace('{key_crypt}', $this->hexaKey(), $file_content);
            file_put_contents($this->file_admin, $file_content);

            // Test connexion bdd
            require $this->file_admin;
            try {
                $this->connexion = new PDO("mysql:host=" .
                    DB_HOST . ";dbname=" .
                    DB_NAME, DB_USER, DB_PASSWORD);
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $user_tab = $table_prefix . 'user';
                $station_tab = $table_prefix . 'station';
                $config_tab = $table_prefix . 'config';
                $tab_tab = $table_prefix . 'tab';

                // Test Création des tables
                try {

                    $response_user = $this->createUser($user_tab, DB_CHARSET);
                    $response_station = $this->createStation($station_tab, DB_CHARSET, $user_tab);
                    $response_config = $this->createConfig($config_tab, DB_CHARSET, $station_tab, $this->l->getLg());
                    $response_tab = $this->createTab($tab_tab, DB_CHARSET, $station_tab);

                    $this->requete = $this->connexion->prepare($response_user);
                    $result1 = $this->requete->execute();
                    $this->requete = $this->connexion->prepare($response_station);
                    $result2 = $this->requete->execute();
                    $this->requete = $this->connexion->prepare($response_config);
                    $result3 = $this->requete->execute();
                    $this->requete = $this->connexion->prepare($response_tab);
                    $result4 = $this->requete->execute();

                    if ($result1 && $result2 && $result3 && $result4) {
                        $response = 4;
                    } else {
                        if (MB_DEBUG) {
                            var_dump($result1);
                            echo "<br>";
                            var_dump($result2);
                            echo "<br>";
                            var_dump($result3);
                            echo "<br>";
                            var_dump($result4);
                            echo "<br>";
                        }
                        $response = 3;
                    }
                } catch (Exception $e) {
                    if (MB_DEBUG) {
                        die(var_dump($e->getMessage()));
                    }
                    $response = 3;
                }
            } catch (Exception $e) {
                if (MB_DEBUG) {
                    die($e->getMessage());
                }
                unlink($this->file_admin);
                $response = 2;
            }
        }
        return $response;
    }

    // Création de la Table *_user
    public function createUser($tab, $charset)
    {
        $create_tab = "CREATE TABLE IF NOT EXISTS $tab (
                user_id int(11) NOT NULL auto_increment,
                user_login varchar(60) NOT NULL default '',
                user_password varchar(255) NOT NULL default '',
                user_email varchar(100) NOT NULL default '',
                CONSTRAINT PK_user_id PRIMARY KEY (user_id)
            ) ENGINE = InnoDB DEFAULT CHARSET = $charset";

        return $create_tab;
    }

    // Création de la Table *_station
    public function createStation($tab, $charset, $tab2)
    {
        $constraint = "FK_" . $tab . "_" . $tab2;
        $create_tab = "CREATE TABLE IF NOT EXISTS $tab (
                stat_id int(11) NOT NULL auto_increment,
                stat_type varchar(24) NOT NULL default '',
                stat_did varchar(60) NOT NULL default '',
                stat_key varchar(60) NOT NULL default '',
                stat_users varchar(100) NOT NULL default '',
                stat_password varchar(255) NOT NULL default '',
                stat_token varchar(60) NOT NULL default '',
                stat_active tinyint(1) NOT NULL default 0,
                user_id int(11) NOT NULL,
                CONSTRAINT PK_stat_id PRIMARY KEY (stat_id),
                CONSTRAINT $constraint FOREIGN KEY (user_id) REFERENCES $tab2(user_id)
            ) ENGINE = InnoDB DEFAULT CHARSET = $charset";

        return $create_tab;
    }

    // Création de la Table *_config
    public function createConfig($tab, $charset, $tab2, $lg)
    {
        $constraint = "FK_" . $tab . "_" . $tab2;
        $create_tab = "CREATE TABLE IF NOT EXISTS  $tab (
                config_id int(11) NOT NULL auto_increment,
                config_lang varchar(4) NOT NULL default '$lg',
                config_sun varchar(8) NOT NULL default '0',
                config_aux1 tinyint(1) NOT NULL default 0,
                config_aux2 tinyint(1) NOT NULL default 0,
                config_aux3 tinyint(1) NOT NULL default 0,
                config_temp varchar(2) NOT NULL default 'C',
                config_wind varchar(4) NOT NULL default 'kph',
                config_rain varchar(3) NOT NULL default 'mm',
                config_press varchar(4) NOT NULL default 'hpa',
                config_css varchar(24) NOT NULL default 'bluedark',
                config_daynight tinyint(1) NOT NULL default 0,
                config_color varchar(24) NOT NULL default 'colored',
                config_icon tinyint(1) NOT NULL default 1,
                stat_id int(11) NOT NULL,
                CONSTRAINT PK_config_id PRIMARY KEY (config_id),
                CONSTRAINT $constraint FOREIGN KEY (stat_id) REFERENCES $tab2(stat_id),
                UNIQUE (stat_id)
            ) ENGINE = InnoDB DEFAULT CHARSET = $charset";

        return $create_tab;
    }

    // Création de la Table *_tab
    public function createTab($tab, $charset, $tab2)
    {
        $constraint = "FK_" . $tab . "_" . $tab2;
        $create_tab = "CREATE TABLE IF NOT EXISTS  $tab (
                tab_id int(11) NOT NULL auto_increment,
                tab_lines int(11) NOT NULL default 4,
                tab_1a int(11) NOT NULL default 1,
                tab_1b int(11) NOT NULL default 2,
                tab_1c int(11) NOT NULL default 3,
                tab_2a int(11) NOT NULL default 4,
                tab_2b int(11) NOT NULL default 5,
                tab_2c int(11) NOT NULL default 6,
                tab_3a int(11) NOT NULL default 7,
                tab_3b int(11) NOT NULL default 8,
                tab_3c int(11) NOT NULL default 9,
                tab_4a int(11) NOT NULL default 10,
                tab_4b int(11) NOT NULL default 11,
                tab_4c int(11) NOT NULL default 12,
                tab_5a int(11) NOT NULL default 43,
                tab_5b int(11) NOT NULL default 44,
                tab_5c int(11) NOT NULL default 13,
                tab_6a int(11) NOT NULL default 14,
                tab_6b int(11) NOT NULL default 15,
                tab_6c int(11) NOT NULL default 16,
                tab_7a int(11) NOT NULL default 30,
                tab_7b int(11) NOT NULL default 31,
                tab_7c int(11) NOT NULL default 24,
                tab_8a int(11) NOT NULL default 25,
                tab_8b int(11) NOT NULL default 37,
                tab_8c int(11) NOT NULL default 38,
                tab_9a int(11) NOT NULL default 26,
                tab_9b int(11) NOT NULL default 27,
                tab_9c int(11) NOT NULL default 28,
                tab_10a int(11) NOT NULL default 39,
                tab_10b int(11) NOT NULL default 40,
                tab_10c int(11) NOT NULL default 41,
                stat_id int(11) NOT NULL,
                CONSTRAINT PK_tab_id PRIMARY KEY (tab_id),
                CONSTRAINT $constraint FOREIGN KEY (stat_id) REFERENCES $tab2(stat_id),
                UNIQUE (stat_id)                
            ) ENGINE = InnoDB DEFAULT CHARSET = $charset";

        return $create_tab;
    }

    public function dropBDD()
    {

        if (file_exists($this->file_admin)) {
            require $this->file_admin;
            try {

                $user_tab = $table_prefix . 'user';
                $station_tab = $table_prefix . 'station';
                $config_tab = $table_prefix . 'config';
                $tab_tab = $table_prefix . 'tab';

                $response_tab = $this->dropTab($tab_tab);
                $response_config = $this->dropTab($config_tab);
                $response_station = $this->dropTab($station_tab);
                $response_user = $this->dropTab($user_tab);

                $this->requete = $this->connexion->prepare($response_tab);
                $this->requete->execute();
                $this->requete = $this->connexion->prepare($response_config);
                $this->requete->execute();
                $this->requete = $this->connexion->prepare($response_station);
                $this->requete->execute();
                $this->requete = $this->connexion->prepare($response_user);
                $this->requete->execute();

                // Delete admin.php
                unlink($this->file_admin);
            } catch (Exception $e) {
                if (MB_DEBUG) {
                    var_dump($e->getMessage());
                }
            }
        }
    }


    public function dropTab($tab)
    {
        $drop_tab = "DROP TABLE IF EXISTS $tab";
        return $drop_tab;
    }


    /**
     * Suppresion du contenu de toute la BDD $table
     * 
     * @return boolean
     */
    public function truncateTable($table)
    {

        require $this->file_admin;
        $tab = $table_prefix . $table;
        try {
            $this->requete = $this->connexion->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->requete = $this->connexion->query("TRUNCATE TABLE $tab");
            $this->requete = $this->connexion->query("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $e) {
            if (MB_DEBUG) {
                var_dump($e->getMessage());
            }
        }
    }

    /**
     * Défini Mbell comme étant installé
     * 
     * @return void
     */
    public function InstallTrue()
    {
        require $this->file_admin;

        // Injection dans admin.php de installed_true
        $file_content = file_get_contents($this->file_admin);
        $file_content = str_replace('{installed_false}', 'installed_true', $file_content);
        file_put_contents($this->file_admin, $file_content);
    }
}
