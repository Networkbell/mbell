<?php
class ChangeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Selection de toute la liste dans BDD station et user associé de user connecté avec stations non-activé et stat_type == v1/v2/live
     * 
     * @return type(string, int)
     */
    public function getAllStationUserLogin($stat_type)
    {
        require $this->file_admin;
        $user_tab = $table_prefix . 'user';
        $station_tab = $table_prefix . 'station';
        $id_stat_user = $user_tab . '.user_id';
        $id_stat_station = $station_tab . '.user_id';
        $stat_active = 0;


        try {

            $req = "SELECT stat_id, stat_type, stat_did, stat_key, 
            stat_users, stat_password, stat_token, 
            stat_livekey, stat_livesecret, stat_livenbr, stat_liveid, 
            stat_wxurl, stat_wxid, stat_wxkey, stat_wxsign, 
            $id_stat_station FROM $station_tab INNER JOIN $user_tab ON $id_stat_station = $id_stat_user 
            WHERE user_login = :user_login AND stat_active = :stat_active AND stat_type = :stat_type
            ";

            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':user_login', $_SESSION['user_login']);
            $this->requete->bindParam(':stat_active', $stat_active);
            $this->requete->bindParam(':stat_type', $stat_type);
            $result = $this->requete->execute();

            if ($result) {
                $list = $this->requete->fetchAll(PDO::FETCH_ASSOC);
            }
            return $list;
        } catch (Exception $e) {
            if (MB_DEBUG) {
                var_dump($e->getMessage());
            }
        }
    }


    /**
     * Selection d'un élément dans la BDD "station"
     * 
     * @return type(string, int)
     */
    public function getItem($id)
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';

        try {
            $this->requete = $this->connexion->prepare("SELECT * FROM $station_tab WHERE stat_id = :stat_id");
            $this->requete->bindParam(':stat_id', $id['stat_id']);
            $result = $this->requete->execute();
            $list = array();
            if ($result) {
                $list = $this->requete->fetch(PDO::FETCH_ASSOC);
            }
            return $list;
        } catch (Exception $e) {
            if (MB_DEBUG) {
                var_dump($e->getMessage());
            }
        }
    }



    /**
     * Ajout dans la BDD station avec statut inactif
     * 
     * @return void
     */
    public function addStationChange($paramPost)
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';

        $stat_type = $paramPost['stat_type'];
        $live_key = $paramPost['stat_livekey'];
        $live_secret = $paramPost['stat_livesecret'];
        $live_nbr = $paramPost['stat_livenbr'];
        $live_id = $this->getStationID($live_key, $live_secret, $stat_type, $live_nbr);
        $stat_active = 0;

        try {
            $req = "INSERT INTO $station_tab VALUES(
            NULL, :stat_type, :stat_did, :stat_key, 
            :stat_users, :stat_password, :stat_token,  
            :stat_livekey, :stat_livesecret, :stat_livenbr, :stat_liveid, :stat_wxurl, :stat_wxid, :stat_wxkey, :stat_wxsign, :stat_active, :user_id
            )";

            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':stat_type', $stat_type);
            $this->requete->bindParam(':stat_did', $paramPost['stat_did']);
            $this->requete->bindParam(':stat_key', $paramPost['stat_key']);
            $this->requete->bindParam(':stat_users', $paramPost['stat_users']);
            $this->requete->bindParam(':stat_password', $paramPost['stat_password']);
            $this->requete->bindParam(':stat_token', $paramPost['stat_token']);
            $this->requete->bindParam(':stat_livekey', $live_key);
            $this->requete->bindParam(':stat_livesecret', $live_secret);
            $this->requete->bindParam(':stat_livenbr', $live_nbr);
            $this->requete->bindParam(':stat_liveid', $live_id);
            $this->requete->bindParam(':stat_wxurl', $paramPost['stat_wxurl']);
            $this->requete->bindParam(':stat_wxid', $paramPost['stat_wxid']);
            $this->requete->bindParam(':stat_wxkey', $paramPost['stat_wxkey']);
            $this->requete->bindParam(':stat_wxsign', $paramPost['stat_wxsign']);
            $this->requete->bindParam(':stat_active', $stat_active);
            $this->requete->bindParam(':user_id', $paramPost['user_id']);

            $result = $this->requete->execute();
            $row = ($result) ? $this->connexion->lastInsertId('stat_id') : null;
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
     * Suppression d'un élément dans la BDD "station" "config" "tab"
     * 
     * @return boolean
     */
    public function deleteBDD($id)
    {

        if (file_exists($this->file_admin)) {
            require $this->file_admin;

            try {

                $station_tab = $table_prefix . 'station';
                $config_tab = $table_prefix . 'config';
                $tab_tab = $table_prefix . 'tab';

                $response_tab = $this->deleteTab($tab_tab);
                $response_config = $this->deleteTab($config_tab);
                $response_station = $this->deleteTab($station_tab);

                $this->requete = $this->connexion->prepare($response_tab);
                $this->requete->bindParam(':stat_id', $id['stat_id']);
                $result1 = $this->requete->execute();
                $this->requete = $this->connexion->prepare($response_config);
                $this->requete->bindParam(':stat_id', $id['stat_id']);
                $result2 = $this->requete->execute();
                $this->requete = $this->connexion->prepare($response_station);
                $this->requete->bindParam(':stat_id', $id['stat_id']);
                $result3 = $this->requete->execute();
                $row = ($result1 && $result2 && $result3) ? 1 : null;
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

    public function deleteTab($tab)
    {
        $delete_tab = "DELETE FROM $tab WHERE stat_id = :stat_id";
        return $delete_tab;
    }



    /**
     * Modifications dans la BDD "station" 
     * 
     * @return boolean
     */
    public function updateBDD($paramPost)
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';

        $stat_type = $paramPost['stat_type'];
        $live_key = $paramPost['stat_livekey'];
        $live_secret = $paramPost['stat_livesecret'];
        $live_nbr = $paramPost['stat_livenbr'];
        $live_id = $this->getStationID($live_key, $live_secret, $stat_type, $live_nbr);

        $req = "UPDATE $station_tab SET stat_did = :stat_did, stat_key = :stat_key, stat_users = :stat_users, 
        stat_password = :stat_password, stat_token = :stat_token,
        stat_livekey = :stat_livekey, stat_livesecret = :stat_livesecret, stat_livenbr = :stat_livenbr, stat_liveid = :stat_liveid,
        stat_wxurl = :stat_wxurl, stat_wxid = :stat_wxid, stat_wxkey = :stat_wxkey, stat_wxsign = :stat_wxsign
        WHERE stat_id = :stat_id";


        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':stat_id', $paramPost['stat_id']);
            $this->requete->bindParam(':stat_did', $paramPost['stat_did']);
            $this->requete->bindParam(':stat_key', $paramPost['stat_key']);
            $this->requete->bindParam(':stat_users', $paramPost['stat_users']);
            $this->requete->bindParam(':stat_password', $paramPost['stat_password']);
            $this->requete->bindParam(':stat_token', $paramPost['stat_token']);
            $this->requete->bindParam(':stat_livekey', $live_key);
            $this->requete->bindParam(':stat_livesecret', $live_secret);
            $this->requete->bindParam(':stat_livenbr', $live_nbr);
            $this->requete->bindParam(':stat_liveid', $live_id);
            $this->requete->bindParam(':stat_wxurl', $paramPost['stat_wxurl']);
            $this->requete->bindParam(':stat_wxid', $paramPost['stat_wxid']);
            $this->requete->bindParam(':stat_wxkey', $paramPost['stat_wxkey']);
            $this->requete->bindParam(':stat_wxsign', $paramPost['stat_wxsign']);

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
     * Modifications dans la BDD "station" de la station active en OFF et de la station choisi en ON
     * Modifications dans la BDD "config" associé à la station active du cron et activation du cron de la station choisi si besoin
     * Le cron suit toujtours la station active
     * 
     * @return boolean
     */
    public function activateStation($id_stat, $active, $cron)
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $config_tab = $table_prefix . 'config';
        $stat_statid = $station_tab . '.stat_id';
        $config_statid = $config_tab . '.stat_id';

        $stat_activate = 1;
        $stat_disabled = 0;
        $cron_activate = $cron['config_cron'] ;

        $req_stat_disabled = "UPDATE $station_tab SET stat_active = :stat_disabled  WHERE stat_id = :stat_id_disabled";
        $req_stat_activate = "UPDATE $station_tab SET stat_active = :stat_activate  WHERE stat_id = :stat_id_activate";

        $req_config_disabled = "UPDATE $config_tab SET config_cron = :config_disabled  WHERE config_id = :config_id_disabled";
        $req_config_activate = "UPDATE $config_tab INNER JOIN $station_tab ON $config_statid = $stat_statid SET config_cron = :config_activate  WHERE $config_statid = :config_id_activate";

        try {
            //on désactive la station active
            $this->requete = $this->connexion->prepare($req_stat_disabled);
            $this->requete->bindParam(':stat_id_disabled', $active['stat_id']);
            $this->requete->bindParam(':stat_disabled', $stat_disabled);
            $result1 = $this->requete->execute();
            //on désactive le cron actif (qu'il soit déjà actif ou non)
            $this->requete = $this->connexion->prepare($req_config_disabled);
            $this->requete->bindParam(':config_id_disabled', $cron['config_id']);
            $this->requete->bindParam(':config_disabled', $stat_disabled);
            $result2 = $this->requete->execute();
            //on active la station choisi
            $this->requete = $this->connexion->prepare($req_stat_activate);
            $this->requete->bindParam(':stat_id_activate', $id_stat['stat_id']);
            $this->requete->bindParam(':stat_activate', $stat_activate);
            $result3 = $this->requete->execute();
            //on active le cron de la station choisi si actif (sinon on le désactive)
            $this->requete = $this->connexion->prepare($req_config_activate);
            $this->requete->bindParam(':config_id_activate', $id_stat['stat_id']);
            $this->requete->bindParam(':config_activate', $cron_activate);
            $result4 = $this->requete->execute();
            $row = ($result1 && $result2 && $result3 && $result4) ? 1 : null;
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
