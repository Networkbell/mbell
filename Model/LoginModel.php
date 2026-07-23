<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require MBELLPATH . 'PHPMailer/src/Exception.php';
require MBELLPATH . 'PHPMailer/src/PHPMailer.php';
require MBELLPATH . 'PHPMailer/src/SMTP.php';


class LoginModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function verifLogin($paramPost)
    {

        require $this->file_admin;
        $user_tab = $table_prefix . 'user';

        $pwd_peppered = $this->pepperKey($paramPost);

        try {
            $this->requete = $this->connexion->prepare("SELECT * FROM $user_tab WHERE user_login = :user_login OR user_email = :user_email");
            $this->requete->bindParam(':user_login', $paramPost['user_login']);
            $this->requete->bindParam(':user_email', $paramPost['user_login']); //user_login remplace user_email dans le formulaire           
            $this->requete->execute();
            $result = $this->requete->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                if (password_verify($pwd_peppered, $result['user_password'])) {
                    $_SESSION['user_login'] = $paramPost['user_login'];
                    $row = 1;
                } else {
                    unset($_SESSION['user_login']);
                    $row = 0;
                }
            } else {
                $row = 0;
            }
        } catch (exception $e) {
            die('Erreur:' . $e->getMessage());
            $row = 0;
        }
        return $row;
    }

    public function logout()
    {
        unset($_SESSION['user_login']);
    }






    /**
     * Modification dans la BDD "config" des zones Options
     * 
     * @return boolean
     */
    public function updateUser($userpost)
    {

        require $this->file_admin;
        $user_tab = $table_prefix . 'user';

        try {
            $req1 = "UPDATE $user_tab SET user_login = :user_login WHERE user_id = :user_id";
            $req2 = "UPDATE $user_tab SET user_password = :user_password WHERE user_id = :user_id";
            $req3 = "UPDATE $user_tab SET user_email = :user_email WHERE user_id = :user_id";

            $user_id = $userpost['user_id'];
            $user_login = $userpost['user_login'];
            $user_password = $userpost['user_password'];
            $user_email = $userpost['user_email'];

            if ($user_login != '') {
                $this->requete = $this->connexion->prepare($req1);
                $this->requete->bindParam(':user_id', $user_id);
                $this->requete->bindParam(':user_login', $user_login);
                $result1 = $this->requete->execute();
                $row = ($result1) ? 1 : null;
            }
            if ($user_password != '') {

                $pwd_peppered = $this->pepperKey($userpost);
                $pwd_hashed = password_hash($pwd_peppered, PASSWORD_DEFAULT);

                $this->requete = $this->connexion->prepare($req2);
                $this->requete->bindParam(':user_id', $user_id);
                $this->requete->bindParam(':user_password', $pwd_hashed);
                $result2 = $this->requete->execute();
                $row = ($result2) ? 1 : null;
            }
            if ($user_email != '') {
                $this->requete = $this->connexion->prepare($req3);
                $this->requete->bindParam(':user_id', $user_id);
                $this->requete->bindParam(':user_email', $user_email);
                $result3 = $this->requete->execute();
                $row = ($result3) ? 1 : null;
            } else {
                $row = 1;
            }
        } catch (Exception $e) {
            if (MB_DEBUG) {
                var_dump($e->getMessage());
            }
            $row = null;
        }
        return $row;
    }

    public function validMail($mailpost)
    {
        require $this->file_admin;
        $user_tab = $table_prefix . 'user';

        if (isset($mailpost["verif_email"]) && (!empty($mailpost["verif_email"]))) {
            $email = $mailpost["verif_email"];
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$email) {
                $row = 0; //email invalid
            } else {
                try {
                    $req = "SELECT * FROM $user_tab WHERE user_email = :user_email";
                    $this->requete = $this->connexion->prepare($req);
                    $this->requete->bindParam(':user_email',  $email);
                    $this->requete->execute();
                    $result = $this->requete->fetch(PDO::FETCH_ASSOC);
                    if ($result) {
                        $row = 1; //email valide et existe
                    } else {
                        $row = 2; //email valide mais n'existe pas
                    }
                } catch (Exception $e) {
                    if (MB_DEBUG) {
                        die($e->getMessage());
                        $row = 3; //erreur PHP
                    }
                }
            }
        } else {
            $row = 4; //email non envoyé, recommencer
        }
        return $row;
    }



    public function tempoMail($mailpost)
    {

        require $this->file_admin;
        $pass_tab = $table_prefix . 'pass';
        $email = $mailpost["verif_email"];

        $expFormat = mktime(
            date("H") + 1,
            date("i"),
            date("s"),
            date("m"),
            date("d"),
            date("Y")
        );
        $expDate = date("Y-m-d H:i:s", $expFormat);
        $key = hash_hmac("sha256", $email, $this->hexaKey());

        try {
            $req = "INSERT INTO $pass_tab VALUES(:pass_email, :pass_key, :pass_expDate)";
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':pass_email', $email);
            $this->requete->bindParam(':pass_key', $key);
            $this->requete->bindParam(':pass_expDate', $expDate);
            $result = $this->requete->execute();
            $row = ($result) ? $key : null;
        } catch (Exception $e) {
            if (MB_DEBUG) {
                error_log('[MBELL MAIL] tempoMail:error : ' . $e->getMessage());
            }
            $row = null;
        }
        return $row;
    }


    public function sendMail($lg, $key, $mailpost)
    {
        $email = $mailpost["verif_email"];
        $url = $this->dispatcher->url();
        $resetUrl = $url . 'index.php?controller=login&action=reinit&key=' . urlencode($key) . '&email=' . urlencode($email) . '&lg=' . urlencode($lg);

        $message = $this->l->trad('MAIL_PASS_1');
        $message .= '<p><a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '</a></p>';
        $message .= $this->l->trad('MAIL_PASS_2');

        $subject = $this->l->trad('MAIL_PASS_3');

        $resultLocal = $this->sendMailLocal($email, $subject, $message, $url);
        if ($resultLocal === true) {
            return $this->l->trad('MAIL_PASS_4');
        }

        $this->debugMail('local_mail_failed', $resultLocal);

        $resultRelay = $this->sendMailRelay($email, $subject, $message, $resetUrl, $lg, $url);
        if ($resultRelay === true) {
            return $this->l->trad('MAIL_PASS_4');
        }

        $this->debugMail('relay_mail_failed', $resultRelay);

        return 'Error Mail : local=' . $resultLocal . ' | relay=' . $resultRelay;
    }

    private function sendMailLocal($email, $subject, $message, $url)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isMail();
            $mail->setFrom($this->getMailFromAddress($url), 'MBell');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->MsgHTML($message);
            $mail->AltBody = $this->buildResetAltBody($message);
            $mail->CharSet = 'UTF-8';

            $mail->send();

            return true;
        } catch (\Exception $e) {
            return ($mail->ErrorInfo != '') ? $mail->ErrorInfo : $e->getMessage();
        }
    }

    private function sendMailRelay($email, $subject, $message, $resetUrl, $lg, $url)
    {
        $relayUrl = $this->getMailRelayUrl();
        if ($relayUrl == '') {
            return 'relay_disabled';
        }

        $payload = array(
            'type' => 'password_reset',
            'site_url' => $url,
            'timestamp' => time(),
            'lg' => $lg,
            'to' => $email,
            'subject' => $subject,
            'html' => $message,
            'text' => $this->buildResetAltBody($message),
            'reset_url' => $resetUrl,
        );

        return $this->postRelayJson($relayUrl, $payload);
    }

    private function getMailFromAddress($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $host = strtolower((string) $host);
        $host = preg_replace('/^www\./', '', $host);
        $host = preg_replace('/[^a-z0-9\.\-]/', '', $host);

        if ($host == '' || strpos($host, '.') === false) {
            return 'noreply@localhost.localdomain';
        }

        return 'noreply@' . $host;
    }

    private function buildResetAltBody($message)
    {
        $text = str_replace(array('</p>', '<br>', '<br/>', '<br />'), "\n", $message);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    private function getMailRelayUrl()
    {
        if (defined('MB_MAIL_RELAY_URL')) {
            return trim(MB_MAIL_RELAY_URL);
        }

        return '';
    }

    private function getMailRelayToken()
    {
        if (defined('MB_MAIL_RELAY_TOKEN')) {
            return trim(MB_MAIL_RELAY_TOKEN);
        }

        return '';
    }

    private function sanitizeRelayPayload($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->sanitizeRelayPayload($item);
            }

            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        if (function_exists('mb_check_encoding') && mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        }

        if (function_exists('iconv')) {
            $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);
            if ($converted !== false) {
                return $converted;
            }
        }

        return utf8_encode($value);
    }

    private function postRelayJson($relayUrl, $payload)
    {
        $payload = $this->sanitizeRelayPayload($payload);

        $jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $jsonOptions = $jsonOptions | JSON_INVALID_UTF8_SUBSTITUTE;
        }

        $json = json_encode($payload, $jsonOptions);
        if ($json === false) {
            $error = 'relay_json_error: ' . json_last_error_msg();
            $this->debugMail('relay_json_encode_failed', $error);

            return $error;
        }

        $relayToken = $this->getMailRelayToken();
        if ($relayToken == '') {
            return 'relay_token_missing';
        }

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($json),
            'X-MBell-Token: ' . $relayToken,
        );

        if (function_exists('curl_init')) {
            $ch = curl_init($relayUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            $body = curl_exec($ch);

            if ($body === false) {
                $error = 'relay_curl_error: ' . curl_error($ch);
                curl_close($ch);
                $this->debugMail('relay_curl_failed', $error);

                return $error;
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $this->parseRelayResponse($httpCode, $body);
        }

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $json,
                'ignore_errors' => true,
                'timeout' => 15,
            ),
        ));

        $body = file_get_contents($relayUrl, false, $context);
        if ($body === false) {
            $this->debugMail('relay_stream_failed', 'relay_stream_error');

            return 'relay_stream_error';
        }

        $httpCode = 0;
        if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $matches)) {
            $httpCode = (int) $matches[1];
        }

        return $this->parseRelayResponse($httpCode, $body);
    }

    private function parseRelayResponse($httpCode, $body)
    {
        if ((int) $httpCode < 200 || (int) $httpCode >= 300) {
            $cleanBody = trim(strip_tags((string) $body));
            $error = ($cleanBody != '') ? 'relay_http_' . (int) $httpCode . ' : ' . $cleanBody : 'relay_http_' . (int) $httpCode;
            $this->debugMail('relay_http_failed', $error);

            return $error;
        }

        $response = json_decode($body, true);
        if (!is_array($response)) {
            $error = 'relay_invalid_response';
            $this->debugMail('relay_invalid_json_response', $body);

            return $error;
        }

        if (!empty($response['success'])) {
            return true;
        }

        if (!empty($response['message'])) {
            return $response['message'];
        }

        return 'relay_send_failed';
    }

    private function debugMail($label, $data = null)
    {
        if (MB_DEBUG !== true) {
            return;
        }

        if (is_array($data) || is_object($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        error_log('[MBELL MAIL] ' . $label . (($data !== null) ? ' : ' . $data : ''));
    }


    public function validTokenMail($paramGet)
    {
        require $this->file_admin;
        $pass_tab = $table_prefix . 'pass';
        $token = $paramGet['key'];
        $email = $paramGet['email'];
        $row = 0;

        if (isset($token) && isset($email)) {
            try {
                $req = "SELECT COUNT(*) FROM $pass_tab WHERE pass_key = :pass_key and pass_email = :pass_email";
                $this->requete = $this->connexion->prepare($req);
                $this->requete->bindParam(':pass_key',  $token);
                $this->requete->bindParam(':pass_email',  $email);
                $this->requete->execute();
                $result = $this->requete->fetchColumn();
                if ($result) {
                    $row = 1;
                } else {
                    $row = 2;
                }
            } catch (Exception $e) {
                if (MB_DEBUG) {
                    die($e->getMessage());
                }
            }
        }

        return $row;
    }

    public function getTokenMail($paramGet)
    {
        require $this->file_admin;
        $pass_tab = $table_prefix . 'pass';
        $token = $paramGet['key'];
        $email = $paramGet['email'];
        try {
            $req = "SELECT * FROM $pass_tab WHERE pass_key = :pass_key and pass_email = :pass_email";
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':pass_key',  $token);
            $this->requete->bindParam(':pass_email',  $email);
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

        return array();
    }

    public function getUserMail($paramGet)
    {
        require $this->file_admin;
        $user_tab = $table_prefix . 'user';
        $email = $paramGet['email'];
        try {
            $req = "SELECT * FROM $user_tab WHERE user_email = :user_email";
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':user_email',  $email);
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

        return array();
    }


    public function deletePassToken($paramPost){
        require $this->file_admin;
        $pass_tab = $table_prefix . 'pass'; 
        $email = $paramPost['user_email'];
        try {
            $req = "DELETE FROM $pass_tab WHERE pass_email = :pass_email";
            $this->requete = $this->connexion->prepare($req);
            $this->requete->bindParam(':pass_email',  $email);
            $result = $this->requete->execute();
            $row = ($result) ? 1 : null;                       
        } catch (Exception $e) {
            if (MB_DEBUG === true) {
                error_log('[MBELL MAIL] deletePassToken:error : ' . $e->getMessage());
            }
            $row = null;            
        }
        return $row;
    }
}   
