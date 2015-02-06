<?php
	session_start();
	
	if($_GET['opt'] === 'receive'){
		receive();
	}else if($_GET['opt'] === 'insert'){
		insert();
	}else if($_GET['opt'] === 'recharge'){
		recharge();
	}else if($_GET['opt'] === 'create'){
		create();
	}else if($_GET['opt'] === 'download'){
		download();
	}else if($_GET['opt'] == 'confirmReceiveInfo'){
		confirmReceiveInfo();
	}else{
		echo '<script type="text/javascript">window.location.href = "./index.html";</script>'; 	
	}


	//创建vpn账户 2
	function create(){
		require_once('./db.class.php');
		$db = new DB();
		$db->openConn();

		if(empty($_POST['ReceiverName'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '联系人姓名不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['CompanyName'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '公司名称不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['ReceiverPhone'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '联系人手机不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['ReceiverMail'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '邮箱地址不能为空';
			echo json_encode($arr);
			exit;
		}
		/*if(empty($_POST['ReceiverAdd'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '收货地址不能为空';
			echo json_encode($arr);
			exit;
		}*/
		
		/*$sql = "select * from Ad_Gift where ID=".$_POST['ID'];
		$vo = $db->query($sql);*/

		$param = '&realname='.$_POST['ReceiverName'].'&phoneNum='.$_POST['ReceiverPhone'].'&address=&email='.$_POST['ReceiverMail'].'&desc='.$_POST['CompanyName'];

		$data = file_get_contents("http://api.efly.cc/vpn/interface_user_add.php?opt=insert&user=".$_POST['user']."&pwd=".$_POST['pwd'].$param);
		$user = json_decode($data,true);
		if($user[0] != 0){
			$arr['info'] = 'error'; 
			$arr['data'] = $user[2];
			echo json_encode($arr);
			exit;
		}
		
		date_default_timezone_set('PRC');
		/*$sql = "update Ad_Gift set 
			ReceiverName='" . $_POST['ReceiverName'] . "' 
			, CompanyName='" . $_POST['CompanyName'] . "' 
			, ReceiverPhone='" . $_POST['ReceiverPhone'] . "' 
			, ReceiverMail='" . $_POST['ReceiverMail'] . "' 
			, ReceiverAdd='" . $_POST['ReceiverAdd'] . "' 
			, TS='" . date('Y-m-d H:i:s',time()) . "' 
			, Status = 1 
			where ID=".$_POST['ID'];*/

		$sql = "update Ad_Gift set 
			ReceiverName='" . $_POST['ReceiverName'] . "' 
			, CompanyName='" . $_POST['CompanyName'] . "' 
			, ReceiverPhone='" . $_POST['ReceiverPhone'] . "' 
			, ReceiverMail='" . $_POST['ReceiverMail'] . "' 
			, ReceiverAdd='vpn账号无需填写地址' 
			, TS='" . date('Y-m-d H:i:s',time()) . "' 
			, Status = 1 
			where ID=".$_POST['ID'];

		//$sql = "update Ad_Gift set  Status=1, TS='" . date('Y-m-d H:i:s',time()) . "' where ID=".$_POST['ID'];
		$rs = $db->execute($sql);
		if($rs === false){
			$arr['info'] = 'error'; 
			$arr['data'] = '数据库错误';
			echo json_encode($arr);
			exit;
		}
		
		$arr['info'] = 'success'; 
		$arr['data'] = 'ok';
		unset ($_SESSION['code']);
		unset ($_SESSION['pwd']);
		echo json_encode($arr);
		
	}

	//再次确认VPN盒子收货信息 1
	function confirmReceiveInfo(){
		require_once('./db.class.php');
		$db = new DB();
		$db->openConn();
		
		date_default_timezone_set('PRC');
		$sql = "update Ad_Gift set 
			ReceiverName='" . $_POST['ReceiverName'] . "' 
			, ReceiverPhone='" . $_POST['ReceiverPhone'] . "' 
			, ReceiverAdd='" . $_POST['ReceiverAdd'] . "' 
			, TS='" . date('Y-m-d H:i:s',time()) . "' 
			, Status = 1 
			where ID=".$_POST['ID'];

		$rs = $db->execute($sql);
		if($rs === false){
			$arr['info'] = 'error'; 
			$arr['data'] = '数据库错误';
			echo json_encode($arr);
			exit;
		}

		$arr['info'] = 'success'; 
		$arr['data'] = 'ok';
		unset ($_SESSION['code']);
		unset ($_SESSION['pwd']);
		echo json_encode($arr);
	}
	
	//充值睿江云账户 3
	function recharge(){
		require_once('./db.class.php');
		$db = new DB();
		$db->openConn();
		
		$sql = "select * from Ad_Gift where ID=".$_POST['ID'];
		$vo = $db->query($sql);
		
		$data = file_get_contents("http://api.efly.cc/ecloud/coupon.php?opt=recharge&mail=".$_POST['mail']."&pwd=".$_POST['pwd']."&code=".$vo[0]['UserCode']);
		$result = json_decode($data,true);
		if($result['ret'] != 0 ){
			$arr['info'] = 'error'; 
			$arr['data'] = $result['error'];
			echo json_encode($arr);
			exit;
		}
		$data2 = file_get_contents("http://api.efly.cc/ecloud/user.php?opt=query&mail=".$_POST['mail']);
		$user = json_decode($data2,true);
		
		date_default_timezone_set('PRC');
		$sql = "update Ad_Gift set  Status=1,ReceiverName='" . $user[0]['userName'] . "' , CompanyName='" . $user[0]['companyName'] . "' , ReceiverPhone='" . $user[0]['phone'] . "' , ReceiverMail='" . $_POST['mail'] . "' , ReceiverAdd='" . $user[0]['address'] . "' , TS='" . date('Y-m-d H:i:s',time()) . "' where ID=".$_POST['ID'];
		
		$rs = $db->execute($sql);
		if($rs === false){
			$arr['info'] = 'error'; 
			$arr['data'] = '数据库错误';
			echo json_encode($arr);
			exit;
		}
		
		$arr['info'] = 'success'; 
		$arr['data'] = 'ok';
		unset ($_SESSION['code']);
		unset ($_SESSION['pwd']);
		echo json_encode($arr);
		
	}
	
	//验证账户密码 index
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
		if((!empty($result[0]['ReceiverName']) && !empty($result[0]['CompanyName']) && !empty($result[0]['ReceiverMail']) && !empty($result[0]['ReceiverPhone']) && !empty($result[0]['ReceiverAdd'])) || $result[0]['Status']==1){
			$arr['info'] = 'error'; 
			$arr['data'] = '输入的礼品券已经领取，如有疑问咨询客服。';
			echo json_encode($arr);
			exit;
		}
		
		$_SESSION['code'] = $_POST['code'];
		$_SESSION['pwd'] = $_POST['pwd'];
		
		$arr['info'] = 'success';
		$arr['page'] = $result[0]['GiftPage'];
		$arr['data'] = $result[0]['ID']; 
		
		echo json_encode($arr);
	}
	
	//插入用户信息 1
	function insert(){
		
		require_once('./db.class.php');
		$db = new DB();
		$db->openConn();
		if(empty($_POST['ReceiverName'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '联系人姓名不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['CompanyName'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '公司名称不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['ReceiverPhone'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '联系人手机不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['ReceiverMail'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '邮箱地址不能为空';
			echo json_encode($arr);
			exit;
		}
		if(empty($_POST['ReceiverAdd'])){			
			$arr['info'] = 'error'; 
			$arr['data'] = '收货地址不能为空';
			echo json_encode($arr);
			exit;
		}

		$sql = "select * from Ad_Gift where ID=".$_POST['ID'];
		$result = $db->query($sql);
		if(empty($result)){
			$arr['info'] = 'error'; 
			$arr['data'] = '输入的礼品券不存在';
			echo json_encode($arr);
			exit;
		}


		$param = '&realname='.$_POST['ReceiverName'].'&phoneNum='.$_POST['ReceiverPhone'].'&address='.$_POST['ReceiverAdd'].'&email='.$_POST['ReceiverMail'].'&desc='.$_POST['CompanyName'];

		//调用vpn添加接口
		$data = file_get_contents("http://api.efly.cc/vpn/interface_user_add.php?opt=insert&user=".$_SESSION['code']."&pwd=".$_SESSION['pwd']."rjkjhezi".$param);
		$user = json_decode($data,true);
		if($user[0] != 0){
			$arr['info'] = 'error'; 
			$arr['data'] = $user[2];
			echo json_encode($arr);
			exit;
		}
		
		date_default_timezone_set('PRC');
		$sql = "update Ad_Gift set  ReceiverName='" . $_POST['ReceiverName'] . "' , CompanyName='" . $_POST['CompanyName'] . "' , ReceiverPhone='" . $_POST['ReceiverPhone'] . "' , ReceiverMail='" . $_POST['ReceiverMail'] . "' , ReceiverAdd='" . $_POST['ReceiverAdd'] . "' , TS='" . date('Y-m-d H:i:s',time()) . "' where ID=".$_POST['ID'];
		$rs = $db->execute($sql);
		if($rs === false){
			$arr['info'] = 'error'; 
			$arr['data'] = '数据库错误';
			echo json_encode($arr);
			exit;
		}
		
		$arr['info'] = 'success'; 
		$arr['data'] = 'ok'; 
		
		unset ($_SESSION['code']);
		unset ($_SESSION['pwd']);
		
		echo json_encode($arr);
	}

	//下载安全协议
	function download(){
		$file_dir = './';
		$file_name = 'agreement.doc';

		$file = fopen($file_dir . $file_name,"r"); // 打开文件
		// 输入文件标签
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".filesize($file_dir . $file_name));
		Header("Content-Disposition: attachment; filename=" . $file_name);
		// 输出文件内容
		echo fread($file,filesize($file_dir . $file_name));
		fclose($file);
	}
	
?>

