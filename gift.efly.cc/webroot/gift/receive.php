<?php
	if($_GET['opt'] === 'receive'){
		receive();
	}

	function receive(){
		
		require_once('./db.class.php');
		$db = new DB();
		$db->openConn();
		if(empty($_POST['code'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '礼品券账号不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['pwd'])){
			$arr['info'] = 'error'; 
			$arr['data'] = '礼品券密码不能为空';
			echo json_encode($arr);
			exit;
		}
		
		$sql = "select * from Ad_Gift where UserCode='".$_POST['code']."' and PassWord='".$_POST['pwd']."' ";
		$result = $db->query($sql);
		if(empty($result)){
			$arr['info'] = 'error'; 
			$arr['data'] = '输入的礼品券不存在';
			echo json_encode($arr);
			exit;
		}
		
		
		$arr['info'] = 'success'; 
		$arr['data'] = 'ok'; 
		
		echo json_encode($arr);
	}
	
?>

