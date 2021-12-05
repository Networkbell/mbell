<?php

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
}
