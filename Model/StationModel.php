<?php


class StationModel extends Model
{

    public function __construct()
    {
        parent::__construct();
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


    public function getAPI()
    {
        $active = $this->getStationActive();
        $my_type = $active['stat_type'];
        $my_did = $active['stat_did'];
        $my_key = $active['stat_key'];
        $my_pass = $active['stat_password'];
        $my_token = $active['stat_token'];

        if ($my_type == 'v1') {
            $data = file_get_contents('http://api.weatherlink.com/v1/NoaaExt.json?DID=' . $my_did . '&key=' . $my_key);
        }
        if ($my_type == 'v2') {
            $data = file_get_contents('http://api.weatherlink.com/v1/NoaaExt.json?user=' . $my_did . '&pass=' . $my_pass . '&apiToken=' . $my_token);
        }
        
        $json = json_decode($data);
        $this->jsonDebug();
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
        stat_token, stat_active, $stat_userid, user_login, user_password, user_email 
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
