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

    public function userAction()
    {
        $user = $this->model->getUserSession();
        $this->view->getLoginUser($user);
    }

    public function forgetAction()
    {
        $this->view->getFormForget();
    }

    public function logInstallAction()
    {
        $lg = $this->l->getLg();
        $row = $this->model->verifLogin($this->paramPost);
        if ($row) {
            header('location:index.php?controller=install&action=step6&lg=' . $lg);
        } else {
            header('location:index.php?controller=login&action=install&lg=' . $lg);
        }
    }


    public function logAction()
    {
        $lg = $this->l->getLg();
        $row = $this->model->verifLogin($this->paramPost);
        if ($row) {
            header('location:index.php?controller=pref&action=list&lg=' . $lg);
        } else {
            header('location:index.php?controller=login&action=form&lg=' . $lg);
        }
    }

    public function logoutAction()
    {
        $lg = $this->l->getLg();
        $this->model->logout();
        header('location: index.php?controller=login&lg=' . $lg);
    }


    public function updatepassAction()
    {
        $row1 = $this->model->updateUser($this->paramPost);
        $lg = $this->l->getLg();
        if ($row1) {
            $row2 = $this->model->deletePassToken($this->paramPost);
            if ($row2) {
                $this->model->logout();
                header('location:index.php?controller=login&action=form&lg=' . $lg);
            } else {
                header('location:index.php?controller=home&action=list&lg=' . $lg);
            }
        } else {
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        }
    }

    public function updatelogAction()
    {


        $row = $this->model->updateUser($this->paramPost);
        $lg = $this->l->getLg();
        if ($row) {
            $this->model->logout();
            header('location:index.php?controller=login&action=form&lg=' . $lg);
        } else {
            header('location:index.php?controller=home&action=list&lg=' . $lg);
        }
    }


    public function mailAction()
    {
        $lg = $this->l->getLg();
        $email = $this->paramPost;
        $row1 = $this->model->validMail($email);


        if ($row1 != 1) {
            header('location:index.php?controller=login&action=forget&lg=' . $lg);
        } else {
            $key = $this->model->tempoMail($email);
            $row2 = $key;
            if ($row2) {
                $row3 = $this->model->sendMail($lg, $key, $email);
                $this->view->getLoginSendMail($row3);
            } else {
                header('location:index.php?controller=login&action=forget&lg=' . $lg);
            }
        }
    }

    public function reinitAction()
    {
        $lg = $this->l->getLg();
        $row1 = $this->model->validTokenMail($this->paramGet);
        if ($row1 != 1) {
            header('location:index.php?controller=login&action=form&lg=' . $lg);
        } else {
            $paramPass = $this->model->getTokenMail($this->paramGet);
            $user = $this->model->getUserMail($this->paramGet);
            $expDate = $paramPass['pass_expDate'];
            $curDate = date("Y-m-d H:i:s");
            if ($expDate >= $curDate) {
                $this->view->newPass($user);
            } else {
                header('location:index.php?controller=login&action=form&lg=' . $lg);
            }
        }
    }
}



