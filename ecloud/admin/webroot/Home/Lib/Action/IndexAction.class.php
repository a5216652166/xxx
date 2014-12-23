<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {

    public function index(){
    	if(empty($_SESSION['user'])){
    		$this->success('请先登录', U('Index/login'));
            //$this->redirect('Index/login');
            //redirect(U('Index/login'));
    		return;
    	}
        
		$this->display();
    }

    public function login(){
        if(empty($_POST)){
            $this->display();
            return;
        }

    	if(empty($_POST['username'])){
            $this->errorMsg = '请填写用户名';
    		$this->display();
            return;
    	}

        if(empty($_POST['password'])){
            $this->errorMsg = '请填写密码';
            $this->display();
            return;
        }

    	$users = array(
    		'admin' => '123456',
    		'test' => 'fuck'
    	);

    	if($users[$_POST['username']] === $_POST['password']){
    		$_SESSION['user'] = array('username' => $_POST['username'], 'password' => $_POST['password']);

    		header('location:' . U('Index/index'));
    		return;
    	}

    	$this->errorMsg = '用户名不存在或密码不正确';
    	$this->display();
    }

    public function logout(){
    	session_destroy();

    	//$this->success('登出成功', U('Index/login'));
    	//header('location:' . U('Index/login'));
        $this->redirect('Index/login');
    }

}