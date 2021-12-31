<?php


class StationModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }





        /**
     * Retourne l'URL de l'API Live current : https://api.weatherlink.com/v2/stations/
     * 
     * 
     */
    public function getLiveURLCurrent($key, $secret, $live_id)
    {
        $parameters = array(
            "api-key" => $key,
            "api-secret" => $secret,
            "station-id" => $live_id,
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
        $apiCurrent = "https://api.weatherlink.com/v2/current/" . $parameters["station-id"] . "?api-key=" . $parameters["api-key"] . "&api-signature=" . $apiSignature . "&t=" . $parameters["t"] ;


        return $apiCurrent;
    }


    /**
     * simplifie l'API live 
     * array_merge_recursive génère un $single_array[key][0,1] si il y 2 clefs identiques (exemples plusieurs stations)
     *
     * @param [array] $array
     * @return array
     */
    public function liveAPIreduc($array)
    {
        $new_array = array();
        foreach ($array as $element1) {
            if (is_array($element1) && count($element1) > 0) {
                foreach ($element1 as $j => $element2) {
                    if (is_array($element2) && count($element2) > 0) {
                        foreach ($element2 as $element3) {
                            if (is_array($element3) && count($element3) > 0) {
                                foreach ($element3 as $element4) {
                                    if (is_array($element4) && count($element4) > 0) {
                                        $new_array[$j] = $element4;
                                        $result = [];
                                        array_walk_recursive($new_array, function ($value, $key) use (&$result) {
                                            $result[$key][] = $value;
                                        });
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }


    public function getAPI()
    {
        $active = $this->getStationActive();
        $my_type = $active['stat_type'];
        $my_did = $active['stat_did'];
        $my_key = $active['stat_key'];
        $my_pass = $active['stat_password'];
        $my_token = $active['stat_token'];
        $my_livekey = $active['stat_livekey'];
        $my_livesecret = $active['stat_livesecret'];
        $my_liveid = $active['stat_liveid'];

        if ($my_type == 'v1') {
            $data = file_get_contents('http://api.weatherlink.com/v1/NoaaExt.json?DID=' . $my_did . '&key=' . $my_key);
            $json = json_decode($data);
            $this->jsonDebug();
        }
        if ($my_type == 'v2') {
            $data = file_get_contents('http://api.weatherlink.com/v1/NoaaExt.json?user=' . $my_did . '&pass=' . $my_pass . '&apiToken=' . $my_token);
            $json = json_decode($data);
            $this->jsonDebug();
        }
        if ($my_type == 'live') {            
            $data = @file_get_contents($this->getLiveURLCurrent($my_livekey, $my_livesecret, $my_liveid));           
            $array= json_decode($data, true);
            $this->jsonDebug();
            $json = $this->liveAPIreduc($array);
        }        
        return $json;   
    }



    /**
     * Selection d'un élément dans la BDD stations et de l'user associé
     * 
     * @return type(string, int)
     */
    public function getStationActive()
    {

        require $this->file_admin;
        $station_tab = $table_prefix . 'station';
        $user_tab = $table_prefix . 'user';
        $stat_userid = $station_tab . '.user_id';
        $user_userid = $user_tab . '.user_id';
        $stat_active = 1;

        $req = "SELECT stat_id, stat_type, stat_did, stat_key, stat_users, stat_password, 
        stat_token, stat_livekey, stat_livesecret, stat_liveid, stat_active, $stat_userid, user_login, user_password, user_email 
        FROM $station_tab 
        INNER JOIN $user_tab 
        ON $stat_userid = $user_userid 
        WHERE stat_active = :stat_active";

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
}
