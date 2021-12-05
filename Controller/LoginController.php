<?php




class LoginController extends Controller
{

    

    public function __construct()
    {
        $this->view = new LoginView();
        $this->model = new LoginModel();
        
        parent::__construct();      
    }

    public function installAction()
    {
        $this->view->getFormInstall();        
    }


    public function formAction()
    {
        $this->view->getFormHome();        
    }

    public function logInstallAction(){
        $lg = $this->l->getLg();
        $row = $this->model->verifLogin($this->paramPost);
        if ($row){
            header ('location:index.php?controller=install&action=step6&lg='.$lg);
        } else {
            header ('location:index.php?controller=login&action=install&lg='.$lg);
        }
    }


    public function logAction(){
        $lg = $this->l->getLg();
        $row = $this->model->verifLogin($this->paramPost);
        if ($row){
            header ('location:index.php?controller=pref&action=list&lg='.$lg);
        } else {
            header ('location:index.php?controller=login&action=form&lg='.$lg);
        }
    }

    public function logoutAction(){
        $lg = $this->l->getLg();
        $this->model->logout();
        header('location: index.php?controller=login&lg='.$lg);
    }

}



