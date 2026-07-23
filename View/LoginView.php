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
            "_ROOT" => $this->dispatcher->getRoot(),
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


    public function getFormInstall()
    {
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

        $this->page .= '<form action="index.php?controller=login&action=logInstall&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormLogin($param);
        $this->page .= $this->getInfo($param['LOGIN_TEXT_2']);
        $this->page .= $this->getSubmit('login', $param['CONNECT']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function getFormHome()
    {
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
        $this->page .= '<form action="index.php?controller=login&action=log&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormLogin($param);
        $this->page .= $this->getInfo($param['LOGIN_TEXT_2']);
        $this->page .= '<div class="txt_offset col-4 col-lg-5 offset-8 offset-xl-9"><a href="index.php?controller=login&action=forget&lg=' . $param['_LG'] . '" >' . $this->l->trad('LOGIN_FORGET') . '</a></div>';      
        $this->page .= $this->getSubmit('login', $param['CONNECT']);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    
    }

        public function getLoginUser($user)
    {
        $this->constructHead();
        $param = array(
            "1" => $this->l->trad('LOGIN_USER_LABEL'),
            "2" => $user['user_login'],
            "3" => $this->l->trad('LOGIN_USER_TEXT'),
            "4" => $this->l->trad('LOGIN_PASSWORD_LABEL'),
            "5" => '*************',
            "6" => $this->l->trad('LOGIN_PASSWORD_TEXT'),
            "7" => $this->l->trad('LOGIN_EMAIL_LABEL'),
            "8" => $user['user_email'],
            "9" => $this->l->trad('LOGIN_EMAIL_TEXT'),
            "LOGIN_TEXT_2" => $this->l->trad('LOGIN_TEXT_2'),
            "_LG" => $this->l->getLg(),
            "required" => '',
            "_VAL_USER_ID" => $user['user_id'],
        );
        $this->page .= '<main id="main_login">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('MODIF_USER') . '</h1>';
        $this->page .= $this->getInfo($this->l->trad('LOGIN_TEXT_3'));
        $this->page .= $this->getInfo($this->l->trad('LOGIN_TEXT_4'));
        $this->page .= '<form action="index.php?controller=login&action=updatelog&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormUser($param);
        $this->page .= $this->getInfo($this->l->trad('LOGIN_TEXT_5'));
        $this->page .= $this->getSubmit('login', $this->l->trad('MODIFY'));
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }


    public function newPass($user)
    {
        $this->constructHead();
        $param = array(
            "2" => $user['user_login'],
            "4" => $this->l->trad('LOGIN_PASSWORD_LABEL'),
            "5" => '*************',
            "6" => $this->l->trad('LOGIN_PASSWORD_TEXT'),
            "8" => $user['user_email'],
            "_LG" => $this->l->getLg(),
            "required" => '',
            "_VAL_USER_ID" => $user['user_id'],
        );
        $this->page .= '<main id="main_login">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('MODIF_PASS') . '</h1>';
        $this->page .= $this->getInfo($this->l->trad('LOGIN_TEXT_8'));
        $this->page .= '<form action="index.php?controller=login&action=updatepass&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormPass($param);
        $this->page .= $this->getSubmit('login', $this->l->trad('MODIFY'));
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }




    public function getFormForget()
    {
        $this->constructHead();
        $param = array(
            "7" => $this->l->trad('LOGIN_EMAIL_LABEL'),
            "8" => '',
            "9" => $this->l->trad('LOGIN_EMAIL_VERIF'),          
            "_LG" => $this->l->getLg(),
            "required" => 'required',
        );
        $this->page .= '<main id="main_login">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('LOGIN_FORGET') . '</h1>';
        $this->page .= $this->getInfo($this->l->trad('LOGIN_TEXT_6'));
        $this->page .= '<form action="index.php?controller=login&action=mail&lg=' . $param['_LG'] . '" method="POST">';
        $this->page .= $this->getFormMail($param);
        $this->page .= $this->getInfo($this->l->trad('LOGIN_TEXT_7'));
        $this->page .= $this->getSubmit('login', $this->l->trad('VALIDATE'));
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';
        $this->display();
    }

    public function getLoginSendMail($response)
    {
        $param = array(
            "7" => 'login',
            "8" => 'form',
        );
        $this->constructHead();
        $this->page .= '<main id="main_installer">';
        $this->page .= '<section>';
        $this->page .= '<h1>' . $this->l->trad('MAIL_PASS_3') . '</h1>';
        $this->page .= $this->getInfo($response);
        $this->page .= $this->getButton($this->l->getLg(), $param['7'], $param['8'], $this->l->trad('CONNECT'));
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


    public function getFormMail($param)
    {      
        $this->page .= $this->searchHTML('formMail', 'login');
        $this->page = str_replace('{LOGIN_EMAIL_LABEL}',  $param['7'], $this->page);
        $this->page = str_replace('{EMAIL}',  $param['8'], $this->page);
        $this->page = str_replace('{LOGIN_EMAIL_TEXT}',  $param['9'], $this->page);
        $this->page = str_replace('{required}',  $param['required'], $this->page);  
    }


    public function getFormPass($param)
    {
        $this->page .= $this->searchHTML('formPass', 'login');        
        $this->page = str_replace('{required}',  $param['required'], $this->page);
        $this->page = str_replace('{_VAL_USER_ID}',  $param['_VAL_USER_ID'], $this->page);          
        $this->page = str_replace('{LOGIN_PASSWORD_LABEL}',  $param['4'], $this->page);
        $this->page = str_replace('{PASSWORD}',  $param['5'], $this->page);
        $this->page = str_replace('{LOGIN_PASSWORD_TEXT}',  $param['6'], $this->page);

    }
}