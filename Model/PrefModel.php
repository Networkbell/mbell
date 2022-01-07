<?php
class PrefModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }






    /**
     * Modification dans la BDD "config" des zones Options
     * 
     * @return boolean
     */
    public function updateConfig($config)
    {

        require $this->file_admin;
        $config_tab = $table_prefix . 'config';

        $req = "UPDATE $config_tab SET config_sun = :config_sun, config_aux1 = :config_aux1, config_aux2 = :config_aux2, config_aux3 = :config_aux3 WHERE config_id = :config_id";

        $aux1 = isset($config['var_aux1']) ? 1 : 0;
        $aux2 = isset($config['var_aux2']) ? 1 : 0;
        $aux3 = isset($config['var_aux3']) ? 1 : 0;

        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':config_id', $config['config_id']);
            $this->requete->bindParam(':config_sun', $config['var_sun']);
            $this->requete->bindParam(':config_aux1', $aux1);
            $this->requete->bindParam(':config_aux2', $aux2);
            $this->requete->bindParam(':config_aux3', $aux3);
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
     * Modification dans la BDD "config" des zones Default
     * 
     * @return boolean
     */
    public function updateDefault($config)
    {

        require $this->file_admin;
        $config_tab = $table_prefix . 'config';

        $req = "UPDATE $config_tab SET config_lang = :config_lang, config_temp = :config_temp, config_wind = :config_wind, config_rain = :config_rain, config_press = :config_press, config_css = :config_css, config_daynight = :config_daynight, config_color = :config_color, config_icon = :config_icon WHERE config_id = :config_id";


        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':config_id', $config['config_id']);
            $this->requete->bindParam(':config_lang', $config['var_lang']);
            $this->requete->bindParam(':config_temp', $config['var_temp']);
            $this->requete->bindParam(':config_wind', $config['var_wind']);
            $this->requete->bindParam(':config_rain', $config['var_rain']);
            $this->requete->bindParam(':config_press', $config['var_press']);
            $this->requete->bindParam(':config_css', $config['var_css']);
            $this->requete->bindParam(':config_daynight', $config['var_daynight']);
            $this->requete->bindParam(':config_color', $config['var_color']);
            $this->requete->bindParam(':config_icon', $config['var_icon']);
            /* $this->requete->bindParam(':config_cron', $config['var_cron']);*/ // inutile d'updater ici config_cron
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
     * Modification dans la BDD "tab" de la zone lines
     * 
     * @return boolean
     */
    public function updateLines($tab)
    {

        require $this->file_admin;
        $tab_tab = $table_prefix . 'tab';

        $req = "UPDATE $tab_tab SET tab_lines = :tab_lines WHERE tab_id = :tab_id";


        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':tab_id', $tab['tab_id']);
            $this->requete->bindParam(':tab_lines', $tab['var_lines']);
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
     * Modification dans la BDD "tab" des zones tab_1a à tab_10c
     * 
     * @return boolean
     */
    public function updateTab($tab)
    {

        require $this->file_admin;
        $tab_tab = $table_prefix . 'tab';

        $req = "UPDATE $tab_tab SET 
         tab_1a = :tab_1a, tab_1b = :tab_1b, tab_1c = :tab_1c,
         tab_2a = :tab_2a, tab_2b = :tab_2b, tab_2c = :tab_2c, 
         tab_3a = :tab_3a, tab_3b = :tab_3b, tab_3c = :tab_3c, 
         tab_4a = :tab_4a, tab_4b = :tab_4b, tab_4c = :tab_4c, 
         tab_5a = :tab_5a, tab_5b = :tab_5b, tab_5c = :tab_5c,
         tab_6a = :tab_6a, tab_6b = :tab_6b, tab_6c = :tab_6c, 
         tab_7a = :tab_7a, tab_7b = :tab_7b, tab_7c = :tab_7c, 
         tab_8a = :tab_8a, tab_8b = :tab_8b, tab_8c = :tab_8c,
         tab_9a = :tab_9a, tab_9b = :tab_9b, tab_9c = :tab_9c, 
         tab_10a = :tab_10a, tab_10b = :tab_10b, tab_10c = :tab_10c 
         WHERE tab_id = :tab_id";

        try {
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':tab_id', $tab['tab_id']);
            $this->requete->bindParam(':tab_1a', $tab['tab_1a']);
            $this->requete->bindParam(':tab_1b', $tab['tab_1b']);
            $this->requete->bindParam(':tab_1c', $tab['tab_1c']);
            $this->requete->bindParam(':tab_2a', $tab['tab_2a']);
            $this->requete->bindParam(':tab_2b', $tab['tab_2b']);
            $this->requete->bindParam(':tab_2c', $tab['tab_2c']);
            $this->requete->bindParam(':tab_3a', $tab['tab_3a']);
            $this->requete->bindParam(':tab_3b', $tab['tab_3b']);
            $this->requete->bindParam(':tab_3c', $tab['tab_3c']);
            $this->requete->bindParam(':tab_4a', $tab['tab_4a']);
            $this->requete->bindParam(':tab_4b', $tab['tab_4b']);
            $this->requete->bindParam(':tab_4c', $tab['tab_4c']);
            $this->requete->bindParam(':tab_5a', $tab['tab_5a']);
            $this->requete->bindParam(':tab_5b', $tab['tab_5b']);
            $this->requete->bindParam(':tab_5c', $tab['tab_5c']);
            $this->requete->bindParam(':tab_6a', $tab['tab_6a']);
            $this->requete->bindParam(':tab_6b', $tab['tab_6b']);
            $this->requete->bindParam(':tab_6c', $tab['tab_6c']);
            $this->requete->bindParam(':tab_7a', $tab['tab_7a']);
            $this->requete->bindParam(':tab_7b', $tab['tab_7b']);
            $this->requete->bindParam(':tab_7c', $tab['tab_7c']);
            $this->requete->bindParam(':tab_8a', $tab['tab_8a']);
            $this->requete->bindParam(':tab_8b', $tab['tab_8b']);
            $this->requete->bindParam(':tab_8c', $tab['tab_8c']);
            $this->requete->bindParam(':tab_9a', $tab['tab_9a']);
            $this->requete->bindParam(':tab_9b', $tab['tab_9b']);
            $this->requete->bindParam(':tab_9c', $tab['tab_9c']);
            $this->requete->bindParam(':tab_10a', $tab['tab_10a']);
            $this->requete->bindParam(':tab_10b', $tab['tab_10b']);
            $this->requete->bindParam(':tab_10c', $tab['tab_10c']);
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
 * CURL de downlaod ZIP
 * MAJ MBELL
 *
 * @param [float] $version
 * @param [string] $zipFile
 * @return string
 */
    public function downloadZip($version, $zipFile)
    {
        $ver = str_replace(".", "_", strval($version));
        $version_url = 'v' . $ver . 'mbell.zip';
        $url = 'http://www.meteobell.com/fichiers/zip/' . $version_url;
        $zipResource = fopen($zipFile, "w");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FILE, $zipResource);
        $page = curl_exec($ch);
        if (!$page) {
            $rep = "Error :- " . curl_error($ch);
        } else {
            $rep = false;
        }
        curl_close($ch);
        return $rep;
    }
    /**
     * Extract ZIP Archive
     * MAJ MBELL
     *
     * @param [string] $zipFile
     * @return string
     */
    public function extractZip($zipFile)
    {
        require $this->file_admin;
        $zip = new ZipArchive;
        $extractPath = $dir;
        $open = $zip->open($zipFile);
        if ($open == true) {
            $zip->extractTo($extractPath);
            $zip->close();
            $rep = false;
        } else {
            $rep = "Error :- " . $zip->getStatusString();
        }
        return $rep;
    }
}
