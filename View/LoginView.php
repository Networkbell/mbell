<?php

class LoginView extends View{

    public function __construct(){
        
        parent::__construct();
    }
    

    public function constructHead()
    {
        $param = array(
            "META_KEY" => $this->l->trad('META_KEY'),
            "META_DESCRIPTION" => $this->l->trad('META_DESCRIPTION'),
            "MBELL_TITRE" => $this->l->trad('MBELL_TITRE_LOGIN'),
            "_CSS" => "maincolor",
            "_LOGO" => "1",
            "_ROOT" => "1",
            "_LG" => $this->l->getLg()
        );
        $this->page =  $this->getHead($param);
        $this->page .=  '<body>';
        $this->page .=  '<div class="container px-0">';
        $this->page .= $this->getHeader($param);
    }

    public function getHeader($param)
    {
        $this->page .= $this->searchHTML('headerInstall', 'install');
        $this->page = str_replace('{_LOGO}',  $param['_LOGO'], $this->page);
    }


    public function getFormInstall(){
        $this->constructHead();
        $param = array(
            "VALIDATION" => $this->l->trad('VALIDATION'),
            "LOGIN_TEXT_1" => $this->l->trad('LOGIN_TEXT_1'),
            "1" => $this->l->trad('LOGIN_USER_LABEL'),
            "2" => $this->l->trad('USERNAME_EMAIL'),
            "3" => $this->l->trad('LOGIN_USER_TEXT'),
            "4" => $this->l->trad('LOGIN_PASSWORD_LABEL'),
            "5" => $this->l->trad('PASSWORD'),
            "6" => $this->l->trad('LOGIN_PASSWORD_TEXT'),
            "LOGIN_TEXT_2" => $this->l->trad('LOGIN_TEXT_2'),
            '_INSTALL_SUBMIT' => 'verifLogin',
            "CONNECT" => $this->l->trad('CONNECT'),
            "_LG" => $this->l->getLg(),
        );
        $this->page .= '<main id="main_login">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['VALIDATION'] . '</h1>';
        $this->page .= $this->getInfo($param['LOGIN_TEXT_1']);

        $this->page .= '<form action="index.php?controller=login&action=logInstall&lg='.$param['_LG'].'" method="POST">';
        $this->page .= $this->getFormLogin($param);
        $this->page .= $this->getInfo($param['LOGIN_TEXT_2']);
        $this->page .= $this->getSubmit('login', $param['CONNECT']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function getFormHome(){
        $this->constructHead();
        $param = array(
            "VALIDATION" => $this->l->trad('VALIDATION'),
            "LOGIN_TEXT_1" => $this->l->trad('LOGIN_TEXT_1'),
            "1" => $this->l->trad('LOGIN_USER_LABEL'),
            "2" => $this->l->trad('USERNAME_EMAIL'),
            "3" => $this->l->trad('LOGIN_USER_TEXT'),
            "4" => $this->l->trad('LOGIN_PASSWORD_LABEL'),
            "5" => $this->l->trad('PASSWORD'),
            "6" => $this->l->trad('LOGIN_PASSWORD_TEXT'),
            "LOGIN_TEXT_2" => $this->l->trad('LOGIN_TEXT_2'),
            '_INSTALL_SUBMIT' => 'verifLogin',
            "CONNECT" => $this->l->trad('CONNECT'),
            "_LG" => $this->l->getLg(),
        );
        $this->page .= '<main id="main_login">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $param['VALIDATION'] . '</h1>';
        $this->page .= $this->getInfo($param['LOGIN_TEXT_1']);

        $this->page .= '<form action="index.php?controller=login&action=log&lg='.$param['_LG'].'" method="POST">';
        $this->page .= $this->getFormLogin($param);
        $this->page .= $this->getInfo($param['LOGIN_TEXT_2']);
        $this->page .= $this->getSubmit('login', $param['CONNECT']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }




    public function getFormLogin($param)
    {
        $this->page .= $this->searchHTML('formLogin', 'login');
        $this->page = str_replace('{LOGIN_USER_LABEL}',  $param['1'], $this->page);
        $this->page = str_replace('{USERNAME}',  $param['2'], $this->page);
        $this->page = str_replace('{LOGIN_USER_TEXT}',  $param['3'], $this->page);
        $this->page = str_replace('{LOGIN_PASSWORD_LABEL}',  $param['4'], $this->page);
        $this->page = str_replace('{PASSWORD}',  $param['5'], $this->page);
        $this->page = str_replace('{LOGIN_PASSWORD_TEXT}',  $param['6'], $this->page);
    }



}