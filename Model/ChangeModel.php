<?php
class ChangeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * Selection de toute la liste dans BDD station et user associé de user connecté avec stations non-activé
     * 
     * @return type(string, int)
     */
    public function getAllStationUserLogin()
    {
        require $this->file_admin;
        $user_tab = $table_prefix . 'user';
        $station_tab = $table_prefix . 'station';
        $id_stat_user = $user_tab . '.user_id';
        $id_stat_station = $station_tab . '.user_id';
        $stat_active = 0;

        try {

            $req = "SELECT stat_id, stat_type, stat_did, stat_key, stat_users, stat_password, stat_token, $id_stat_station FROM $station_tab INNER JOIN $user_tab ON $id_stat_station = $id_stat_user WHERE user_login = :user_login AND stat_active = :stat_active";

            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':user_login', $_SESSION['user_login']);
            $this->requete->bindParam(':stat_active', $stat_active);
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
    public function addStation($paramPost)
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $stat_active = 0;

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
    public function updateBDD($station){

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';

        $req = "UPDATE $station_tab SET stat_did = :stat_did, stat_key = :stat_key, stat_users = :stat_users, stat_password = :stat_password, stat_token = :stat_token WHERE stat_id = :stat_id";

        
        try {
            $this->requete= $this->connexion->prepare($req);
            $this->requete->bindParam(':stat_id', $station['stat_id']);
            $this->requete->bindParam(':stat_did', $station['stat_did']);
            $this->requete->bindParam(':stat_key', $station['stat_key']);
            $this->requete->bindParam(':stat_users', $station['stat_users']);
            $this->requete->bindParam(':stat_password', $station['stat_password']);
            $this->requete->bindParam(':stat_token', $station['stat_token']);
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
     * 
     * @return boolean
     */
    public function activateStation($id, $active){

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $stat_active1 = 1;
        $stat_active0 = 0;

        $req0 = "UPDATE $station_tab SET stat_active = :stat_active0  WHERE stat_id = :stat_id0";
        $req1 = "UPDATE $station_tab SET stat_active = :stat_active1  WHERE stat_id = :stat_id1";
       
        
        try {
            $this->requete= $this->connexion->prepare($req0);
            $this->requete->bindParam(':stat_id0', $active['stat_id']);
            $this->requete->bindParam(':stat_active0', $stat_active0);
            $result0 = $this->requete->execute(); 
            $this->requete= $this->connexion->prepare($req1);
            $this->requete->bindParam(':stat_id1', $id['stat_id']);
            $this->requete->bindParam(':stat_active1', $stat_active1);
            $result1 = $this->requete->execute(); 
            $row = ($result0 && $result1) ? 1 : null;
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
