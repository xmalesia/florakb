<?php

class user extends Controller {
	
	var $models = FALSE;
	var $view;

	
	function __construct()
	{
		global $basedomain;
		$this->loadmodule();
		$this->view = $this->setSmarty();
		$this->view->assign('basedomain',$basedomain);
        $this->msg = new Messages();
    }
	
	function loadmodule()
	{
        //used for check name, twitter, and email only
        $this->loginHelper = $this->loadModel('loginHelper');
        
        $this->userHelper = $this->loadModel('userHelper');
	}
	
	function index(){
    	//return $this->loadView('home');
    }
    
    function editProfile(){
        $msg = $this->msg->display('all', false);
        $this->view->assign('msg', $msg);
        return $this->loadView('editProfile');
    }
    
    function checkPassword(){
        $data = $_POST['password'];
        $checkPassword = $this->loginHelper->CheckPassword($_SESSION['login'],$data);
        if($checkPassword){
            $return = true;
        }else{
            $return = false;
        }
        echo $return;
        exit;
    }
    
    function doEditProfile(){
        $data = $_POST;
        $editProfile = $this->userHelper->editProfile($data);
        $getUserData = $this->userHelper->getUserData('id',$_SESSION['login']['id']);
        $startSession = $this->loginHelper->setSession($getUserData);
        if($editProfile){
            $this->msg->add('s', 'Update Success');
        }else{
            $this->msg->add('e', 'Update Failed');
        }
        header('Location: ../user/editProfile');
    }
    
    function editPassword(){
        $msg = $this->msg->display('all', false);
        $this->view->assign('msg', $msg);
        return $this->loadView('editPassword');
    }
}

?>