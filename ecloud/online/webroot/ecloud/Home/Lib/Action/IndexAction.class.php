<?php
Vendor('PHPMailer.class#phpmailer');
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
	function _empty(){ 
        header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码 
        $this->display("Public:404"); 
    }
	public function verify(){
		//导入Image类库
		import("ORG.Util.Image");
		Image::buildImageVerify(4,1,"png",60,30,"myverify"); //(length,mode,type,width,height,verifyName)
	}
	public function gift(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			$this->assign('code',$_GET['code']);
			$this->assign('pwd',$_GET['pwd']);
			$this->display();	
		}else{
			if(!empty($_POST['mail']) && !empty($_POST['pwd']) && !empty($_POST['code']) && !empty($_POST['cpwd'])){
				$data = file_get_contents("http://api.efly.cc/ecloud/coupon.php?opt=recharge&mail=".$_POST['mail']."&pwdmd5=".md5($_POST['pwd'])."&code=".$_POST['code']."&cpwd=".$_POST['cpwd']);
				$result = json_decode($data,true);
				if($result['ret']!=0){
					$this->ajaxReturn($result['error'],'error',0);
				}
			
				$data2 = file_get_contents("http://api.efly.cc/ecloud/user.php?opt=query&mail=".$_POST['mail']);
				$user = json_decode($data2,true);
				if($result['ret']!=0){
					$this->ajaxReturn($result['error'],'error',0);
				}
				$data3 = file_get_contents("http://api.efly.cc/gift/gift.php?opt=update&code=".$_POST['code']."&userName=".$user[0]['userName']."&companyName=".$user[0]['companyName']."&phone=".$user[0]['phone']."&address=".$user[0]['address']."&mail=".$_POST['mail']);
				$result2 = json_decode($data3,true);
				if($result['ret']!=0){
					$this->ajaxReturn($result['error'],'error',0);
				}
				$cnd["Name"] = trim($_POST['mail']);
				$cnd["Passwd"] = trim($_POST['pwd']);
				$list = M()->table("Ad_Login");
				$rslt = $list->where($cnd)->find();
				if(!empty($rslt)){
					$_SESSION['user'] = $rslt["Name"];
					$_SESSION['customID'] = $rslt["GlobalCustom_ID"];
					$_SESSION['id'] = $rslt["ObjID"];
					$_SESSION['type'] = $rslt["ObjType"];
					$_SESSION['audit'] = $rslt["Audit"];
				}
				$this->ajaxReturn("成功充值代金券",'success',1);
			}
		}
	}
	public function index(){
		$this->redirect('Index/login');
	}
	//消息详情页面
	public function msgDetail(){
		if(!empty($_GET['id'])){
			$msg = M();
			$entity = $msg->table('Ad_Message')->where("ID=".$_GET['id'])->find();
			//标记为已读
			$msg->table('Ad_Message')->where("ID=".$_GET['id'])->setField("Status",1);
			
			$this->assign('entity',$entity);
			$this->display();
		}
	}
	//标记消息
	public function maskMsg(){
		if(isset($_SESSION['user'])){
			if(!empty($_POST['id'])){
				$msg = M();
				$map['ID'] = array('in',$_POST['id']);
				$is_ok = $msg->table('Ad_Message')->where($map)->setField('Status',1);
				if($is_ok === false){
					$this->ajaxReturn('标记消息记录失败，请联系管理员。','error',0);
				}
				$this->ajaxReturn('标记为已读完成','success',1);
			}
		}else{
			header("Location: ".__APP__."/Index/login");
		}			
	}
	//删除消息
	public function deleteMsg(){
		if(isset($_SESSION['user'])){
			if(!empty($_POST['id'])){
				$msg = M();
				$map['ID'] = array('in',$_POST['id']);
				$is_ok = $msg->table('Ad_Message')->where($map)->delete();
				if($is_ok === false){
					$this->ajaxReturn('删除消息记录失败，请联系管理员。','error',0);
				}
				$this->ajaxReturn(1,'success',1);
			}		
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//消息中心
	public function msgMgr(){
		if(isset($_SESSION['user'])){
		
			$login = M();
			$user = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();		
			
			$msg = M();
			
			$page = 1;
			if(!empty($_GET['p'])){
				$page = $_GET['p'];
			}
			$map['Login_ID'] = $user['ID'];
			if(!empty($_GET['type'])){
				$map['Type'] = $_GET['type'];
			}
			$msgList = $msg->table('Ad_Message')->where($map)->limit(($page-1) * 10,10)->select();	
			$sum = $msg->table('Ad_Message')->where($map)->count();	
			if($sum<=10){
				$pageCount = 1;
			}else{
				if($sum % 10 == 0){
					$pageCount = $sum / 10;
				}else{
					$pageCount = ($sum - $sum % 10) / 10 + 1;
				}
			}
				
			//未读
			$map['Status'] = 0;
			$unread_count = $msg->table('Ad_Message')->where($map)->count();
			
			$this->assign('page',$page);
			$this->assign('pageCount',$pageCount);
			$this->assign('sum',$sum);
			$this->assign('count',count($msgList));
			$this->assign('msgList',$msgList);
			$this->assign('unread_count',$unread_count);
			$this->display();	
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//修改用户密码
	public function updateUserPwd(){
		if(isset($_SESSION['user'])){
			if(!empty($_POST['oldpwd']) && !empty($_POST['newpwd'])){
				$login = M();	
				$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."' and Passwd='".$_POST['oldpwd']."'")->find();
				if(!empty($ret)){
					$is_ok = $login->table('Ad_Login')->where('ID='.$ret['ID'])->setField('Passwd',$_POST['newpwd']);
					if($is_ok === false){
						$this->ajaxReturn('修改密码失败，请联系管理员。','error',0);
					}else{
						$this->ajaxReturn('修改用户密码成功。','success',1);
					}
				}else{
					$this->ajaxReturn('旧密码输入错误，请核对后在试。','error',0);
				}
			}
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	public function docs(){
		$this->display();	
	}
	//账户充值
	public function recharge(){
		if(!empty($_SESSION['user'])){			
			if($_SERVER['REQUEST_METHOD' ] === 'GET'){				
				$this->assign('balance',$this->returnBalance());		
				$this->display();
			}else{
				$order = M();
				$login = M()->table('Ad_Login');
				$ret = $login->where("Name='".$_SESSION['user']."'")->find();
				$ord_count = count($order->query("select * from `Ad_Order` where CreateTS between '".date('Y-m-d',time())." 00:00:00"."'  and '".date('Y-m-d',time())." 23:59:59"."' "));				
				$ord_code = "WEB".date('YmdHis',time()).str_pad($ord_count+1,5,'0',STR_PAD_LEFT);
				
				$data['Login_ID'] = $ret['ID'];				
				$data['Code'] = $ord_code;				
				$data['Type'] = '充值';
				$data['IsPay'] = 0;
				$data['Money'] = $_POST['money'];
				$data['AliPay'] = $_POST['money'];				
				$is_ok = $order->table('Ad_Order')->add($data);
				if($is_ok===false){
					$this->ajaxReturn('账户充值失败，请联系管理员。','error',0);
				}
					$this->ajaxReturn($is_ok,'success',1);
				
			}
		}else{			
			header("Location: ".__APP__."/Index/login");
		}
		
	}
	//添加充值记录
	public function addRecharge(){
		if(!empty($_SESSION['user'])){	
			if(!empty($_POST['id'])){
				$order = M();
				$recharge = M();
				$ret = $order->table('Ad_Order')->where('ID='.$_POST['id'])->find();
				$data['Login_ID'] = $ret['Login_ID'];
				$data['Type'] = "在线充值";
				$data['Proof'] = $ret['Code'];
				$data['Money'] = $ret['Money'];
				$is_ok = $recharge->table('Ad_Recharge')->add($data);
				if($is_ok===false){
					$this->ajaxReturn('账户充值失败，请联系管理员。','error',0);
				}
				$data2['IsPay'] = 1;
				$data2['PayTS'] = date('Y-m-d H:i:s');
				$order->table('Ad_Order')->where('ID='.$_POST['id'])->setField($data2);
				$this->ajaxReturn(1,'success',1);
			}
		}else{			
			header("Location: ".__APP__."/Index/login");
		}
	}
	//消费记录
	public function consumption(){
		if(!empty($_SESSION['user'])){
			$login = M();
			$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
			$page = 1;
			if(!empty($_GET['p'])){
				$page = $_GET['p'];
			}
			$order = M();			
			$ordlist = $order->query('select * from `Ad_Order` o  where o.Login_ID='.$ret['ID'].' and Type="充值" order by o.ID desc limit '. ($page-1) * 10 .',10 ');			
			$count = $order->query('select count(*) as count from `Ad_Order` o  where o.Login_ID='.$ret['ID'].' and Type="充值" order by o.ID desc');
			if($count[0]['count']<=10){
				$pageCount = 1;
			}else{
				if($count[0]['count'] % 10 == 0){
					$pageCount = $count[0]['count'] / 10;
				}else{
					$pageCount = ($count[0]['count'] - $count[0]['count'] % 10) / 10 + 1;
				}
			}
		
			$this->assign('page',$page);		
			$this->assign('pageCount',$pageCount);
			$this->assign('sum',$count[0]['count']);	
			$this->assign('count',count($ordlist));	
			$this->assign('ordlist',$ordlist);

			$this->display();				
		}else{			
			header("Location: ".__APP__."/Index/login");
		}
	}
	//切换企业用户
	public function changeUserType(){
		if(!empty($_SESSION['user'])){			
			if($_SERVER['REQUEST_METHOD' ] === 'GET'){
				$login = M();
				$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
				$person = M();
				$tem = $person->table('Ad_Person')->where('ID='.$ret['ObjID'])->find();
				$this->assign('relt',$tem);
				$this->display();				
			}else{
				$person = M();
				$is_ok = $person->table('Ad_Person')->where('ID='.$_SESSION['id'])->delete();
				if($is_ok===false){
					$this->ajaxReturn('用户身份切换失败，请联系管理员','error',0);
				}
				
				$data['CompanyName'] = $_POST['CompanyName'];
				$data['CompanyCode'] = $_POST['CompanyCode'];
				$data['Address'] = $_POST['Address'];
				$data['LegalPersonName'] = $_POST['LegalPersonName'];
				$data['LegalPersonPhone'] = $_POST['LegalPersonPhone'];
				$data['BeiAn'] = $_POST['BeiAn'];
				$data['LinkmanName'] = $_POST['LinkmanName'];
				$data['LinkmanPhone'] = $_POST['LinkmanPhone'];
				$data['LinkmanIdNum'] = strtoupper($_POST['LinkmanIdNum']);
				$data['LinkmanAdd'] = $_POST['LinkmanAdd'];
				
				
				$Company = M()->table('Ad_Company');
				$is_ok2 = $Company->add($data);
				
				if($is_ok2===false){
					$this->ajaxReturn('用户身份切换失败，请联系管理员','error',0);
				}else{
					$_SESSION['name'] = $_POST['CompanyName'];	
					$_SESSION['id'] = $is_ok2;
					$_SESSION['type'] = 2;
					
					$login = M();
					$data = array('ObjType'=>2,'ObjID'=>$is_ok2);
					$is_ok3 = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->setField($data);
					if($is_ok3===false){
						$this->ajaxReturn('用户身份切换失败，请联系管理员','error',0);
					}
					$this->ajaxReturn('用户身份切换成功','success',1);
				}
			}
		}else{			
			header("Location: ".__APP__."/Index/login");
		}
	}
	
	//购买页面
	public function buy(){
		$login = M();
		$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
		$order = M();
		$ordlist = $order->table('Ad_Order')->where("Login_ID=".$ret['ID']." and IsPay=0 and Type='购买'")->order("ID desc")->find();
		if(!empty($ordlist)){
			$orderVPS = M();
			$result = $orderVPS->table('Ad_OrderNeed')->where('Order_ID='.$ordlist['ID'])->find();			
			$this->assign('result',$result);
		}
		//机房
		$data = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/HouseList.php");
		$houseList = json_decode($data,true);
		//系统
		$data2 = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/TemplateList.php");
		$osList = json_decode($data2,true);
		
		$this->assign('balance',$this->returnBalance());
		$this->assign('houseList',$houseList);
		$this->assign('osList',$osList);
		$this->display();
	}
	//修改用户信息
	public function saveUserInfo(){
		if(isset($_SESSION['user'])){
			if($_SESSION['type']==1){
				
				$data['TrueName'] = $_POST['TrueName'];
				$data['IDNum'] = strtoupper($_POST['IDNum']);
				$data['Address'] = $_POST['Address'];
				$data['Address_'] = $_POST['Address_'];
				$data['BeiAn'] = $_POST['BeiAn'];
				$data['Phone'] = $_POST['Phone'];
				
				/*if(!empty($_POST['BillDay'])){
					$data['BillDay'] = $_POST['BillDay'];
				}*/
				
				$Person = M()->table('Ad_Person');
				$is_ok = $Person->where('ID='.$_POST['ID'])->save($data);
				
				if($is_ok===false){
					$this->ajaxReturn('用户资料修改失败，请联系管理员','error',0);
				}else{
					$_SESSION['name'] = $_POST['TrueName'];
					$this->ajaxReturn('用户资料修改成功','success',1);
				}
				
			}else{
				$data['CompanyName'] = $_POST['CompanyName'];
				$data['CompanyCode'] = $_POST['CompanyCode'];
				$data['Address'] = $_POST['Address'];
				$data['LegalPersonName'] = $_POST['LegalPersonName'];
				$data['LegalPersonPhone'] = $_POST['LegalPersonPhone'];
				$data['BeiAn'] = $_POST['BeiAn'];
				$data['LinkmanName'] = $_POST['LinkmanName'];
				$data['LinkmanPhone'] = $_POST['LinkmanPhone'];
				$data['LinkmanIdNum'] = strtoupper($_POST['LinkmanIdNum']);
				$data['LinkmanAdd'] = $_POST['LinkmanAdd'];
				
				/*if(!empty($_POST['BillDay'])){
					$data['BillDay'] = $_POST['BillDay'];
				}*/
				
				$Company = M()->table('Ad_Company');
				$is_ok = $Company->where('ID='.$_POST['ID'])->save($data);
				
				if($is_ok===false){
					$this->ajaxReturn('用户资料修改失败，请联系管理员','error',0);
				}else{
					$_SESSION['name'] = $_POST['CompanyName'];
					$this->ajaxReturn('用户资料修改成功','success',1);
				}
			}
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	
	//去付款
	public function getorder(){
		if(!empty($_POST['id'])){
			$orderVPS = M();
			$ret = $orderVPS->table('Ad_OrderNeed')->where('Order_ID='.$_POST['id'])->find();
			$this->ajaxReturn($ret,'success',1);
		}else{
			$this->ajaxReturn('订单支付失败，请联系管理员。','error',0);
		}
	}	
	//删除充值记录
	public function deleteRechargeOrder(){
		if(!empty($_POST['id'])){
			$order = M();
			$is_ok = $order->table('Ad_Order')->where('ID='.$_POST['id'])->delete();
			if($is_ok===false){
				$this->ajaxReturn('取消订单失败，请联系管理员。','error',0);	
			}
			$this->ajaxReturn(1,'success',1);
		}
	}
	//删除订单
	public function deleteOrder(){
		if(!empty($_POST['id'])){
			$orderVPS = M();
			$is_ok = $orderVPS->table('Ad_OrderNeed')->where('Order_ID='.$_POST['id'])->delete();
			$order = M();
			$is_ok2 = $order->table('Ad_Order')->where('ID='.$_POST['id'])->delete();
			if($is_ok===false || $is_ok2===false){
				$this->ajaxReturn('取消订单失败，请联系管理员。','error',0);	
			}
			/*else{
				$login = M();
				$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
				$order = M();
				
				$ordlist = $order->query('select * from `Ad_Order` o left join `Ad_OrderNeed` ov on o.ID=ov.Order_ID where o.Login_ID='.$ret['ID'].' and Type!="充值" order by o.ID desc');
				foreach($ordlist as $key => $val){
					$ordlist[$key]['today'] = date("Y-m-d");
				}				
			}*/
			$this->ajaxReturn(1,'success',1);	
		}else{
			$this->ajaxReturn('取消订单失败，请联系管理员。','error',0);
		}
	}
	//查找工单
	public function getWorkList(){
		if(!empty($_POST['oid'])){
			$order = M();
			$ret = $order->table('Ad_Order')->where('ID='.$_POST['oid'])->find();
			$data = file_get_contents(C('INTERFACE_URL')."/ibss/wlist_query.php?opt=get_by_contract&Contract_Code='".$ret['ContractCode']."'");
			$obj = json_decode($data,true);
			$this->ajaxReturn($obj,'success',1);
		}else{
			$this->ajaxReturn('获取工单数据失败，请联系管理员。','error',0);
		}		
	}
	//批量操作虚拟机
	public function batchOperationVM(){
		if(!empty($_POST['code']) && !empty($_POST['opt'])){
			$arr = explode(',',$_POST['code']);
			foreach($arr as $val){				
				if(substr($val,0,8) == "A-08-509"){
					//新版接口
					$data = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$val."&Opt=Set&PowerState=".$_POST['opt']);
					$ret = json_decode($data,true);
					if($ret['ret']!=0){
						$this->ajaxReturn('云主机操作失败，请联系管理员。','error',0);
					}
				}else{
					if($_POST['opt']=="Run"){
						$opt = "on";
					}else if ($_POST['opt']=="Halt"){
						$opt = "off";
					}else if ($_POST['opt']=="Reboot"){
						$opt = "reboot";
					}
					//旧版接口
					$data = file_get_contents(C('OPERATION_VM')."/?opt=setvmpower&id=".$val."&state=".$opt);
					$ret = json_decode($data,true);
					if($ret['ret']!=0){
						$this->ajaxReturn(0,'error',0);
					}
				}
			}
			$this->ajaxReturn(1,'success',1);
		}
	}
	//操作虚拟机
	public function operationVM(){
		if(!empty($_POST['code']) && !empty($_POST['opt'])){
			if(substr($_POST['code'],0,8) == "A-08-509"){
				//新版接口
				$data = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$_POST['code']."&Opt=Set&PowerState=".$_POST['opt']);
				$ret = json_decode($data,true);
				if($ret['ret']!=0){
					$this->ajaxReturn('云主机操作失败，请联系管理员。','error',0);
				}
			}else{
				//旧版接口
				if($_POST['opt']=="Run"){
					$opt = "on";
				}else if ($_POST['opt']=="Halt"){
					$opt = "off";
				}else if ($_POST['opt']=="Reboot"){
					$opt = "reboot";
				}
				//旧版接口
				$data = file_get_contents(C('OPERATION_VM')."/?opt=setvmpower&id=".$_POST['code']."&state=".$opt);
				$ret = json_decode($data,true);
				if($ret['ret']!=0){
					$this->ajaxReturn(0,'error',0);
				}
			}
			
			$this->ajaxReturn($ret['ret'],'success',1);
		}
	}
	//用户信息
	public function account(){
		if(!empty($_SESSION['user'])){
			$login = M();
			$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
			$order = M();
			$ordlist = $order->query('select * from `Ad_Order` o left join `Ad_OrderNeed` ov on o.ID=ov.Order_ID where o.Login_ID='.$ret['ID'].' and Type!="充值" order by o.ID desc limit 0,3 ');
			$rslt = $order->table('Ad_Order')->where('Login_ID='.$ret['ID'])->select();
			$cloudCount = 0;
			$fuzaiCount = 0;
			//获取vps数量
			$orderID = $order->table('Ad_Order')->where('Login_ID='.$ret['ID'])->getField('ID',true);
			$ids = "";
			foreach($orderID as $val){
				$ids .= $val . ',';
			}
			$ids = substr($ids,0,-1);
			
			$Ad_OrderVPSBuy = M();
			$vpsList = $Ad_OrderVPSBuy->query('select * from Ad_OrderVPSBuy ov left join Ad_VPSBuy v on v.ID=ov.VPSBuy_ID where ov.Order_ID in ('.$ids.') ');
			
			/*
			$vpsLists = array();
			if(!empty($rslt)){
				foreach($rslt as $val){
					if(!empty($val['ContractCode'])){
						$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_by_contract&Contract_Code=".$val['ContractCode']."&Dev_Type=vps");
						$vpsList = json_decode($data,true);
						if(!empty($vpsList)){
							$cloudCount++;
						}
					}
				}
			}*/
			
			$this->assign('balance',$this->returnBalance());
			$this->assign('ordlist',$ordlist);
			$this->assign('cloudCount',count($vpsList));
			$this->display();
		}else{
			$this->redirect("/Index/login");
		}
	}
	//订单管理
	public function orderMgr(){
		if(!empty($_SESSION['user'])){
			$login = M();
			$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
			$page = 1;
			if(!empty($_GET['p'])){
				$page = $_GET['p'];
			}
			$order = M();			
			$ordlist = $order->query('select * from `Ad_Order` o left join `Ad_OrderNeed` ov on o.ID=ov.Order_ID where o.Login_ID='.$ret['ID'].' and Type!="充值" order by o.ID desc limit '. ($page-1) * 10 .',10 ');			
			$count = $order->query('select count(*) as count from `Ad_Order` o left join `Ad_OrderNeed` ov on o.ID=ov.Order_ID where o.Login_ID='.$ret['ID'].' and Type!="充值" order by o.ID desc');
			if($count[0]['count']<=10){
				$pageCount = 1;
			}else{
				if($count[0]['count'] % 10 == 0){
					$pageCount = $count[0]['count'] / 10;
				}else{
					$pageCount = ($count[0]['count'] - $count[0]['count'] % 10) / 10 + 1;
				}
			}
		
			$this->assign('page',$page);		
			$this->assign('pageCount',$pageCount);
			$this->assign('sum',$count[0]['count']);	
			$this->assign('count',count($ordlist));	
			$this->assign('ordlist',$ordlist);
			$this->assign('balance',$this->returnBalance());
			$this->assign('today',date("Y-m-d"));
			$this->display();
		}else{
			$this->redirect("/Index/login");
		}
	}
	//会员信息
	public function userMgr(){
		if(!empty($_SESSION['user'])){
			$login = M();
			$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();			
			if($ret['ObjType']==1){
				//个人	
				$person = M();
				$relt = $person->table('Ad_Person')->where('ID='.$ret['ObjID'])->find();
				$this->assign('relt',$relt);
			}else{
				//企业	
				$company = M();
				$relt = $company->table('Ad_Company')->where('ID='.$ret['ObjID'])->find();
				$this->assign('relt',$relt);
			}
			$this->assign('login',$ret);
			$this->assign('balance',$this->returnBalance());
			$this->display();
		}else{
			$this->redirect("/Index/login");
		}
	}
	//控制台
	public function console(){
		if(!empty($_SESSION['user'])){
			
			//新版XEN接口
			$login = M();
			$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
			$order = M();
			
			$orderID = $order->table('Ad_Order')->where('Login_ID='.$ret['ID'])->getField('ID',true);
			$ids = "";
			foreach($orderID as $val){
				$ids .= $val . ',';
			}
			$ids = substr($ids,0,-1);
			
			$Ad_OrderVPSBuy = M();
			$vpsList = $Ad_OrderVPSBuy->query('select * from Ad_OrderVPSBuy ov left join Ad_VPSBuy v on v.ID=ov.VPSBuy_ID where ov.Order_ID in ('.$ids.') ');
			
			foreach($vpsList as $key => $value){
				if(strtotime(date('Y-m-d H:i:s'))>strtotime($value['PayEnd'])){
					$vpsList[$key]['is_expire'] = "yes";
					$data = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$value['PropertyCode']."&Opt=Set&PowerState=Halt");
					$return = json_decode($data,true);
				}else{
					$vpsList[$key]['is_expire'] = "no";
				}
				$url[$value['PropertyCode']] = C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$value['PropertyCode']."&Opt=Get";	
				$url2[$value['PropertyCode']] = C('INTERFACE_URL')."/ecloud_admin/GetVMVNCUrl.php?VMCode=".$value['PropertyCode'];
				$url3[$value['PropertyCode']] = C('INTERFACE_URL')."/ecloud_admin/VMSummary.php?VMCode=".$value['PropertyCode'];
				$vpsList[$key]['Brand'] = "RJCloud";
				$vpsList[$key]['Type'] = "云服务器";
				$vpsList[$key]['No'] = "RJ001";
				$vpsList[$key]['Code'] = $value['PropertyCode'];
				
				//详情
				$Ad_OrderNeed = M();
				$orderNeed = $Ad_OrderNeed->table('Ad_OrderNeed')->where('Order_ID='.$value['Order_ID'])->find();
				$vpsList[$key]['CPU'] = $orderNeed['CPU'];
				$vpsList[$key]['RAM'] = $orderNeed['RAM'];
				$vpsList[$key]['DISK'] = $orderNeed['DISK'];
				$vpsList[$key]['HouseName'] = $orderNeed['HouseName'];
				
				
			}
			//获取状态
			foreach($url as $k => $v){
				$rs[$k] = popen('curl -s -m 5 "'.$v.'"','r');
			}
			foreach($rs as $k => $v){
				foreach($vpsList as $kk => $value){
					if($k == $value['PropertyCode']){
						//新版查询借口
						if(substr($value['PropertyCode'],0,8)=="A-08-509"){
							$data2 = fread($v,2096);
							$tem = json_decode($data2,true);
							$tem2 = json_decode($tem['result'],true);
							$vpsList[$kk]['vmStatus'] = $tem2['PowerState'];
						}else{							
							//旧版查询借口
							$data2 = file_get_contents(C('OPERATION_VM')."/?opt=getvmstate&id=".$value['Code']);
							$tem = json_decode($data2,true);
							if($tem['result']=="poweredOff"){
								$status = "Halted";
							}else{
								$status = "Running";
							}
							$vpsList[$kk]['vmStatus'] = $status;
						}
					}
				}
			}
			//获取host地址和端口
			foreach($url2 as $k => $v){
				$rs2[$k] = popen('curl -s -m 5 "'.$v.'"','r');
			}
			foreach($rs2 as $k => $v){
				foreach($vpsList as $kk => $value){
					if($k == $value['PropertyCode']){
						$data2 = fread($v,2096);
						$tem = json_decode($data2,true);					
						$tem2 = json_decode($tem['result'],true);
						$vpsList[$kk]['port'] = $tem2['VNCProxyPort'];
						$vpsList[$kk]['host'] = $tem2['VNCProxyHost'];					
						$vpsList[$kk]['key'] = $tem2['VNCKey'];
					}
				}
			}
			//获取IP
			foreach($url3 as $k => $v){
				$rs3[$k] = popen('curl -s -m 5 "'.$v.'"','r');
			}
			foreach($rs3 as $k => $v){
				foreach($vpsList as $kk => $value){
					if($k == $value['PropertyCode']){
						$data2 = fread($v,2096);
						$tem = json_decode($data2,true);					
						$tem2 = json_decode($tem['result'],true);
						$vpsList[$kk]['IP'] = $tem2['IP'];
					}
				}
			}
			
			
			$this->assign('vpsList', $vpsList);			
			$this->assign('balance',$this->returnBalance());
			$this->assign('login',$ret);
			$this->assign('today',date("Y-m-d"));
			$this->assign('ordlist',$ordlist);
			$this->display();
			/*
			$login = M();
			$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
			$order = M();
			$ordlist = $order->query('select * from `Ad_Order` o left join `Ad_OrderNeed` ov on o.ID=ov.Order_ID where o.Login_ID='.$ret['ID'].' order by o.ID desc');
			
			$rslt = $order->table('Ad_Order')->where('Login_ID='.$ret['ID'])->select();
			$cou = 0;
			$vpsLists = array();
			if(!empty($rslt)){
				foreach($rslt as $val){
					if(!empty($val['ContractCode'])){
						$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_by_contract&Contract_Code=".$val['ContractCode']."&Dev_Type=vps");
						$vpsList = json_decode($data,true);
						if(!empty($vpsList)){
							foreach($vpsList as $value){
																
								$url[$value['Code']] = C('OPERATION_VM')."/?opt=getvmstate&id=".$value['Code'];
								
								$value['MyCode'] = substr($value['Code'],0,8);
								$value['Order_ID'] = $val['ID'];
								$ip_arr = explode(';',$value['IP_Addr']);
								if(count($ip_arr) == 1){
									$value['IP'] = str_replace(';','',$value['IP_Addr']);
								}else{
									$value['IP'] = str_replace(';','<p/>',$value['IP_Addr']);
								}
								$Ad_OrderNeed = M();
								$orderNeed = $Ad_OrderNeed->table('Ad_OrderNeed')->where('Order_ID='.$value['Order_ID'])->find();
								$value['CPU'] = $orderNeed['CPU'];
								$value['RAM'] = $orderNeed['RAM'];
								$value['DISK'] = $orderNeed['DISK'];
								$value['HouseName'] = $orderNeed['HouseName'];
								
								$vpsLists[$cou] = $value;
								$cou ++;
							}
						}
					}
				}
				foreach($url as $k => $v){
					$rs[$k] = popen('curl -s -m 5 "'.$v.'"','r');
				}
				foreach($rs as $k => $v){
					foreach($vpsLists as $kk => $value){
						if($k == $value['Code']){
							$data2 = fread($v,2096);
							$tem = json_decode($data2,true);
							if($tem['result']=="poweredOff"){
								$status = "Halted";
							}else{
								$status = "Running";
							}
							$vpsLists[$kk]['vmStatus'] = $status;
						}
					}
				}
				//插入续费表信息
				foreach($vpsLists as $value){
					//Ad_VPSBuy信息
					$vap['PropertyCode'] = $value['Code'];
					//Ad_OrderVPSBuy信息
					$vap_pay['Order_ID'] = $value['Order_ID'];
					
					$order = M();
					$tem = $order->table("Ad_Order")->where('ID='.$value['Order_ID'])->find();
					$vap_pay['PayStart'] = $tem['PayTS'];
					$vps = M();
					$rslt = $vps->table('Ad_OrderNeed')->where('Order_ID='.$value['Order_ID'])->find();
					
					if($rslt['Time']=='月'){
						$vap_pay['PayEnd'] = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($tem['PayTS'])));
					}else if($rslt['Time']=='半年'){
						$vap_pay['PayEnd'] = date('Y-m-d H:i:s',strtotime('+6 month',strtotime($tem['PayTS'])));
					}else if($rslt['Time']=='年'){
						$vap_pay['PayEnd'] = date('Y-m-d H:i:s',strtotime('+12 month',strtotime($tem['PayTS'])));
					}
					
					
					$vpsBuy = M();
					$orderVpsBuy = M();
					$entity = $vpsBuy->table('Ad_VPSBuy')->where("PropertyCode='".$value['Code']."'")->find();
					if(empty($entity)){
						$is_ok = $vpsBuy->table('Ad_VPSBuy')->add($vap);
						$vap_pay['VPSBuy_ID'] = $is_ok;					
						$orderVpsBuy->table('Ad_OrderVPSBuy')->add($vap_pay);
					}
				}
				//获取到期时间
				foreach($vpsLists as $key => $value){
					$vpsPay = M();
					$orderVpsBuy = M();
					$tem = $vpsPay->table('Ad_VPSBuy')->where("PropertyCode='".$value['Code']."'")->find();
					$result = $orderVpsBuy->table('Ad_OrderVPSBuy')->where('VPSBuy_ID='.$tem['ID'])->order('PayEnd desc')->find();
					$vpsLists[$key]['PayEnd'] = $result['PayEnd'];
					if(strtotime(date('Y-m-d H:i:s'))>strtotime($result['PayEnd'])){
						$vpsLists[$key]['is_expire'] = "yes";
						$data = file_get_contents(C('OPERATION_VM')."/?opt=setvmpower&id=".$value['Code']."&state=off");
						$return = json_decode($data,true);
					}else{
						$vpsLists[$key]['is_expire'] = "no";
					}
				}
				
				$this->assign('vpsList', $vpsLists);
				
			}		
			$this->assign('balance',$this->returnBalance());
			$this->assign('login',$ret);
			$this->assign('today',date("Y-m-d"));
			$this->assign('ordlist',$ordlist);
			$this->display();
			*/
		}else{
			$this->redirect("/Index/login");
		}
	}
	//修改价格
	public function updatePrice(){
		$price = $this->returnPrice($_POST['cpu'],$_POST['ram'],$_POST['disk'],$_POST['bgp'],$_POST['iplc'],$_POST['count'],$_POST['time']);
		$this->ajaxReturn($price,'success',1);
	}
	//续费
	public function renewPay(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			$data['OS'] = $_GET['sys'];
			$data['CPU'] = $_GET['cpu'];
			$data['RAM'] = $_GET['ram'];
			$data['DISK'] = $_GET['disk'];
			$data['BandWidthIPLC'] = $_GET['iplc'];
			$data['BandWidthBGP'] = $_GET['bgp'];
			$data['Count'] = $_GET['count'];
			$data['HouseName'] = $_GET['house'];
			$data['Oid'] = $_GET['oid'];
			
			$this->assign("price",$this->returnPrice($_GET['cpu'],$_GET['ram'],$_GET['disk'],$_GET['bgp'],$_GET['iplc'],$_GET['count'],'月'));
			
			$this->assign("relt",$data);
			$this->assign("balance",$this->returnBalance());
			$this->display();
		}else{
			$vps = M();
			$ord = $vps->table('Ad_OrderNeed')->where('Order_ID='.$_POST['oid'])->find();
			$this->ajaxReturn($ord,'success',1);	
		}
	}
	//新增合同和工单
	public function insert(){
		if(!empty($_POST['id'])){
			 
			//新版XEN接口
			$Order = M();
			$orderData = array('IsPay'=>1,'PayTS'=>date('Y-m-d H:i:s'));
			//修改订单状态
			$is_ok = $Order->table('Ad_Order')->where('ID='.$_POST['id'])->setField($orderData);
			if($is_ok === false){
				$this->ajaxReturn('修改订单状态失败，请联系管理员。','error',0);
			}
			
			$tem = $Order->table('Ad_Order')->where('ID='.$_POST['id'])->find();
			$OrderNeed = M();
			$vps = $OrderNeed->table('Ad_OrderNeed')->where('Order_ID='.$tem['ID'])->find();
			//模版list
			$result = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/TemplateList.php");
			$tempList = json_decode($result,true);
			$tempCode = "";
			foreach($tempList as $val){
				if($val['SystemName'] == $vps['OS']){
					$tempCode = $val['TemplateCode'];
				}
			}
			$RAM[512] = 512;
			$RAM[1] = 1024;
			$RAM[2] = 2048;
			$RAM[4] = 4096;
			$RAM[8] = 8192;
			$RAM[16] = 16384;
			$RAM[32] = 32768;
			
			$ip_type = "VM_BGPIP";
			$bandwith = $vps['BandWidthBGP'];
			if($vps['BandWidthBGP'] == 0){
				$ip_type = "VM_IPLCIP";				
				$bandwith = $vps['BandWidthIPLC'];
			}
			$disk = "";
			if($vps['DISK'] != 0){
				$disk = $vps['DISK'];
			}
			
			$create = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/VMTemplateCreate.php?StockHouseName=".$vps['HouseName']."&TemplateCode=".$tempCode."&Cpu=".(int)$vps['CPU']."&Ram=".$RAM[(int)$vps['RAM']]."&Count=".$vps['Count']."&DiskSize=".$disk."&PublicIPType=".$ip_type."&Bandwidth=".$bandwith);
			$createTem = json_decode($create,true);
			if($createTem['ret'] != 0){
				$this->ajaxReturn('创建云主机失败，请联系管理员。','error',0);
			}
			$vmcode = json_decode($createTem['result'],true);
			foreach($vmcode as $value){
				$data['PropertyCode'] = $value;
				//添加vps信息
				$Ad_VPSBuy = M();
				$is_ok2 = $Ad_VPSBuy->table('Ad_VPSBuy')->add($data);
				
				if($is_ok2 === false){
					$this->ajaxReturn('交易失败，请联系管理员。','error',0);
				}
				
				$Ad_OrderVPSBuy = M();
				$data2['Order_ID'] = $_POST['id'];
				$data2['VPSBuy_ID'] = $is_ok2;
				$data2['PayStart'] = $tem['PayTS'];
				
				if($vps['Time']=='月'){
					$data2['PayEnd'] = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($tem['PayTS'])));
				}else if($vps['Time']=='半年'){
					$data2['PayEnd'] = date('Y-m-d H:i:s',strtotime('+6 month',strtotime($tem['PayTS'])));
				}else if($vps['Time']=='年'){
					$data2['PayEnd'] = date('Y-m-d H:i:s',strtotime('+12 month',strtotime($tem['PayTS'])));
				}
				
				$is_ok3 = $Ad_OrderVPSBuy->table('Ad_OrderVPSBuy')->add($data2);
				if($is_ok3 === false){
					$this->ajaxReturn('交易失败，请联系管理员。','error',0);
				}
			}			
			$this->ajaxReturn(1,'success',1);
			//推送事件
			$data2 = file_get_contents(C('INTERFACE_URL').'event/async_event_push.php?EventName=睿江云支付成功&EventData={"Code":"'.$tem['Code'].'","Type":"'.$tem['Type'].'","IsPay":'.$tem['IsPay'].',"ContractCode":"'.$tem['ContractCode'].'","Money":"'.$tem['Money'].'","AliPay":"'.$tem['AliPay'].'","BalancePay":"'.$tem['BalancePay'].'"}');
			/*
			$Order = M();
			$tem = $Order->table('Ad_Order')->where('ID='.$_POST['id'])->find();
			
			$OrderVPS = M();
			$vps = $OrderVPS->table('Ad_OrderNeed')->where('Order_ID='.$tem['ID'])->find();
			
			if($vps['BandWidthBGP']!=0){
				$bandwidth = $vps['BandWidthBGP'] . "M BGP带宽 ;";
			}else{
				$bandwidth = $vps['BandWidthIPLC'] . "M IPLC带宽 ;";
			}
			if($vps['Time']=='月'){
				$time = " 1 个月 " ;
				$standard_time='+1 month';
			}
			else if($vps['Time']=='半年'){
				$time = " 6 个月 " ;
				$standard_time='+6 month';
			}else{
				$time = " 1 年 " ;
				$standard_time='+12 month';
			}
			$str = $vps['CPU'] . " CPU ; " . $vps['RAM'] . " 内存 ; " . $vps['DISK'] . "G 硬盘 ; 系统：" . $vps['OS'] . " ; " . $bandwidth . " 时长：" . $time . " ; 数量：". $vps['Count'] ."<br/>";
			
			// post 请求方案
			include_once('HttpClient.class.php');
			
			$params = array('opt'=>'insert','data'=>json_encode(array('type'=>'业务新增','sellType'=>'IDC','content'=>'<p style="color:red"><h3>客户【' . $_SESSION['name'] . '】施工要求</h3>：<br/>'.$str,'stockId'=>'160','cabinet'=>'','ucount'=>'NULL','ucount2'=>'NULL','ucount4'=>'NULL','cmoney'=>$vps['Price'],'payType'=>$vps['Time'].'付','beginDate'=>date('Y-m-d'),'endDate'=>date('Y-m-d',strtotime($standard_time)),'customId'=>C('customID'),'globalCustomId'=>$_SESSION['customID'],'about'=>$str.';','cpayDate'=>date('Y-m-d'),'prePay'=>'1')));
			$pageContents = HttpClient::quickPost(C('INTERFACE_URL').'/ibss/contract.1.php', $params);
			$ret = json_decode($pageContents,true);
			
			$params2 = array('opt'=>'insert','data'=>json_encode(array('type'=>'新业务上架单','businessType'=>'IDC业务','ywtype'=>'租用','contractCode'=>$ret['error'],'doReq'=>'客户【' . $_SESSION['name'] . '】配置要求 : '. "\n" .$str.';','stockHouseId'=>'160','doTime'=>date('Y-m-d'))));
			
			$pageContents2 = HttpClient::quickPost(C('INTERFACE_URL').'/ibss/worklist.php', $params2);
			//修改订单状态
			$pageContents2 = json_decode($pageContents2,true);
			if($pageContents2['ret']==0){
				$data = array('IsPay'=>1,'ContractCode'=>$ret['error'],'PayTS'=>date('Y-m-d H:i:s'));
				$Order->table('Ad_Order')->where('ID='.$_POST['id'])->setField($data);
				//推送事件
				$data2 = file_get_contents(C('INTERFACE_URL').'event/async_event_push.php?EventName=睿江云支付成功&EventData={"Code":"'.$ret['Code'].'","Type":"'.$ret['Type'].'","IsPay":'.$ret['IsPay'].',"ContractCode":"'.$ret['ContractCode'].'","Money":"'.$ret['Money'].'","AliPay":"'.$ret['AliPay'].'","BalancePay":"'.$ret['BalancePay'].'"}');
				
				$this->ajaxReturn(1,'success',1);
			}else{
				$this->ajaxReturn('订单状态修改失败，请联系管理员。','error',0);
			}
		*/
		}else{
			$this->ajaxReturn('交易失败，请联系管理员。','error',0);
		}
	}
	//返回订单状态
	public function finishPayment(){
		$order = M();
		$ret = $order->table('Ad_Order')->where('ID='.$_POST['id'])->find();		
		if(!empty($ret['Code'])){
			$data = file_get_contents(C('INTERFACE_URL')."/alipay/order_query.php?OrderCode=".$ret['Code']."&DepartCode=ESS");
			$obj = json_decode($data,true);
			if($obj[0]['Status']=="SUCC"){				
				$result = $order->table('Ad_Order')->where('ID='.$_POST['id'])->find();		
				if($result['IsPay']==0){
					$this->ajaxReturn('WAIT','success',1);		
				}else{
					$this->ajaxReturn($obj[0]['Status'],'success',1);
				}
			}
		}
	}
	public function returnOrderStatus($id){
		$order = M();
		$ret = $order->table('Ad_Order')->where('ID='.$id)->find();		
		if(!empty($ret['Code'])){
			$data = file_get_contents(C('INTERFACE_URL')."/alipay/order_query.php?OrderCode=".$ret['Code']."&DepartCode=ESS");
			$obj = json_decode($data,true);			
			return $obj[0]['Status'];
		}
	}
	//订单页面 去付款 余额支付
	public function gotopay(){		
		$order = M();
		$tem = $order->table('Ad_Order')->where("ID=".$_POST['id'])->getField('Money');
		
		//余额不足
		if($this->returnBalance()<$tem){
			$this->ajaxReturn('对不起，您的账户余额不足，使用支付宝合并付款？','error',0);	
		}
//		print($order->getLastSql());exit;
		//修改到期时间
		$VPSPay = M();
		$time;
		$ret = $VPSPay->table('Ad_OrderVPSBuy')->where('Order_ID='.$_POST['oid'])->find();
		
		if(strtotime($ret['PayEnd']) < strtotime(date('Y-m-d H:i:s',time()))){
			if($_POST['time']=='月'){
				$time = date('Y-m-d H:i:s',strtotime('+1 month',strtotime(date('Y-m-d H:i:s',time()))));
			}else if($_POST['time']=='半年'){
				$time = date('Y-m-d H:i:s',strtotime('+6 month',strtotime(date('Y-m-d H:i:s',time()))));
			}else if($_POST['time']=='年'){
				$time = date('Y-m-d H:i:s',strtotime('+12 month',strtotime(date('Y-m-d H:i:s',time()))));
			}
		}else{
			if($_POST['time']=='月'){
				$time = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($ret['PayEnd'])));
			}else if($_POST['time']=='半年'){
				$time = date('Y-m-d H:i:s',strtotime('+6 month',strtotime($ret['PayEnd'])));
			}else if($_POST['time']=='年'){
				$time = date('Y-m-d H:i:s',strtotime('+12 month',strtotime($ret['PayEnd'])));
			}
		}		
		
		$is_ok2 = $VPSPay->table('Ad_OrderVPSBuy')->where('Order_ID='.$_POST['oid'])->setField('PayEnd',$time);
		if($is_ok2===false){			
			$this->ajaxReturn('付款失败，请联系管理员。','error',0);
		}else{
		//修改状态
			$data = array('IsPay'=>1,'PayTS'=>date('Y-m-d H:i:s'),'BalancePay'=>$tem,'AliPay'=>0);
			$order->table('Ad_Order')->where('ID='.$_POST['id'])->setField($data);
			$this->ajaxReturn(1,'success',1);
		}
	}
	//合并付款
	public function mergePay(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){		
			$Order = M();
			//修改支付方式		
			$tem = $Order->table('Ad_Order')->where("ID=".$_GET['id'])->find();
			
			$OrderVPS = M();
			$ret = $OrderVPS->table('Ad_OrderNeed')->where("Order_ID=".$_GET['id'])->find();
			if($ret['BandWidthBGP']!=0){
				$bandwidth = $ret['BandWidthBGP'] . "M BGP带宽 ;";
			}else{
				$bandwidth = $ret['BandWidthIPLC'] . "M IPLC带宽 ;";
			}
			if($ret['Time']=='月'){
				$time = " 1 个月 " ;
			}
			else if($ret['Time']=='半年'){
				$time = " 6 个月 " ;
			}else{
				$time = " 1 年 " ;
			}
			$str = $ret['CPU'] . " CPU ; " . $ret['RAM'] . " 内存 ; " . $ret['DISK'] . "G 硬盘 ; 系统：" . $ret['OS'] . " ; " . $bandwidth . " 时长：" . $time . " ; 数量：". $ret['Count'] ."<br/>";
			
			//修改支付方式
			$data['AliPay'] = $ret['Price'] - $this->returnBalance();
			$data['BalancePay'] = $this->returnBalance();
			
			$Order->table('Ad_Order')->where("ID=".$_GET['id'])->save($data);
			
			$this->assign('url', C('INTERFACE_URL')."/alipay/order_pay.php");
			$this->assign('DepartCode', "ESS");
			$this->assign('OrderCode', $tem['Code']);
					
			$name = urlencode("睿江云主机");
			$name = str_replace('%C2%80','',$name);
			$this->assign('OrderName', urldecode($name));
			
			$this->assign('OrderDesc', $str);
			$this->assign('OrderMoney', $ret['Price']-$this->returnBalance());
			
			$this->display();
		}else{			
			$this->gotoAlipay('ESS',$_POST['OrderCode'],$_POST['OrderName'],$_POST['OrderDesc'],$_POST['OrderMoney'],$_POST['OrderUrl']);
		}
		
		
	}
	//去付款   账户余额
	public function payment2(){
		$Order = M();
		
		$tem = $Order->table('Ad_Order')->where("ID=".$_POST['id'])->getField('Money');
		//余额不足
		if($this->returnBalance()<$tem){
			$this->ajaxReturn('对不起，您的账户余额不足，使用支付宝合并付款？','error',0);	
		}else{
			//修改支付方式
			$data['BalancePay'] = $tem;
			$data['AliPay'] = 0;
			$Order->table('Ad_Order')->where("ID=".$_POST['id'])->save($data);
			$this->ajaxReturn(1,'success',0);	
		}
	}
	//返回余额
	public function returnBalance(){
		$login = M();
		$Order = M();
		$Recharge = M();
		$coupon = M();
		$user =$login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
		//代金券记录
		$clist = $coupon->table('Ad_CashCoupon')->where('Login_ID='.$user['ID'])->select();
		//充值记录
		$rlist = $Recharge->table('Ad_Recharge')->where('Login_ID='.$user['ID'])->select();
		//账户方式 付完款的 记录
		$olist = $Order->table('Ad_Order')->where("Login_ID=".$user['ID']." and BalancePay!='0.00' and IsPay=1 ")->select();
		$c_sum;$r_sum;$o_sum;
		foreach($clist as $val){
			$c_sum += $val['Money'];
		}
		foreach($olist as $val){
//			$o_sum += $val['Money'];
			$o_sum += $val['BalancePay'];
		}
		foreach($rlist as $val){
			$r_sum += $val['Money'];
		}
		return $r_sum+$c_sum-$o_sum;
	}
	//去付款   支付宝
	public function payment(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){		
			$Order = M();
			$tem = $Order->table('Ad_Order')->where("ID=".$_GET['id'])->find();
			$OrderVPS = M();
			$ret = $OrderVPS->table('Ad_OrderNeed')->where("Order_ID=".$_GET['id'])->find();
			if($ret['BandWidthBGP']!=0){
				$bandwidth = $ret['BandWidthBGP'] . "M BGP带宽 ;";
			}else{
				$bandwidth = $ret['BandWidthIPLC'] . "M IPLC带宽 ;";
			}
			if($ret['Time']=='月'){
				$time = " 1 个月 " ;
			}
			else if($ret['Time']=='半年'){
				$time = " 6 个月 " ;
			}else{
				$time = " 1 年 " ;
			}
			$str = $ret['CPU'] . " CPU ; " . $ret['RAM'] . " 内存 ; " . $ret['DISK'] . "G 硬盘 ; 系统：" . $ret['OS'] . " ; " . $bandwidth . " 时长：" . $time . " ; 数量：". $ret['Count'] ."<br/>";
			
			//修改支付方式
			
			$data['AliPay'] = $ret['Price'];
			$data['BalancePay'] = 0;
			
			$Order->table('Ad_Order')->where("ID=".$_GET['id'])->save($data);
			
			$this->assign('url', C('INTERFACE_URL')."/alipay/order_pay.php");
			$this->assign('DepartCode', "ESS");
			$this->assign('OrderCode', $tem['Code']);
					
			$name = urlencode("睿江云主机");
			$name = str_replace('%C2%80','',$name);
			$this->assign('OrderName', urldecode($name));
			
			$this->assign('OrderDesc', $str);
			$this->assign('OrderMoney', $ret['Price']);
			
			$this->display();
		}else{			
			$this->gotoAlipay('ESS',$_POST['OrderCode'],$_POST['OrderName'],$_POST['OrderDesc'],$_POST['OrderMoney'],$_POST['OrderUrl']);
		}
	}
	public function rechargePayment(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){	
			$Order = M();
			$tem = $Order->table('Ad_Order')->where("ID=".$_GET['id'])->find();		
			$this->assign('url', C('INTERFACE_URL')."/alipay/order_pay.php");
			$this->assign('DepartCode', "ESS");
			$this->assign('OrderCode', $tem['Code']);				
			$name = urlencode("睿江云主机");
			$name = str_replace('%C2%80','',$name);
			$this->assign('OrderName', urldecode($name));		
			$this->assign('OrderDesc', "睿江云账户充值");
			$this->assign('OrderMoney', $tem['Money']);
			$this->display();
		}else{
			$this->gotoAlipay('ESS',$_POST['OrderCode'],$_POST['OrderName'],$_POST['OrderDesc'],$_POST['OrderMoney'],$_POST['OrderUrl']);
		}
	}
	public function gotoAlipay($DepartCode,$OrderCode,$OrderName,$OrderDesc,$OrderMoney,$OrderUrl){
		include_once('HttpClient.class.php');			
		$params = array('DepartCode'=>$DepartCode,'OrderCode'=>$OrderCode,'OrderName'=>$OrderName,'OrderDesc'=>$OrderDesc,'OrderMoney'=>$OrderMoney,'OrderUrl'=>$OrderUrl);
		$pageContents = HttpClient::quickPost(C('INTERFACE_URL')."/alipay/order_pay.php", $params);
		print_r($pageContents);exit;
	}
	//支付宝续费
	public function renewOrder(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			$Order = M();
			$tem = $Order->table('Ad_Order')->where("ID=".$_GET['id'])->find();
			//修改支付方式
			if(!empty($_GET['type'])){
				$data['AliPay'] = $tem['Money'];
				$data['BalancePay'] = 0;
				$Order->table('Ad_Order')->where("ID=".$_GET['id'])->save($data);
			}
			
			$OrderVPS = M();
			$ret = $OrderVPS->table('Ad_OrderNeed')->where("Order_ID=".$_GET['id'])->find();
			if($ret['BandWidthBGP']!=0){
				$bandwidth = $ret['BandWidthBGP'] . "M BGP带宽 ;";
			}else{
				$bandwidth = $ret['BandWidthIPLC'] . "M IPLC带宽 ;";
			}
			if($ret['Time']=='月'){
				$time = " 1 个月 " ;
			}
			else if($ret['Time']=='半年'){
				$time = " 6 个月 " ;
			}else{
				$time = " 1 年 " ;
			}
			$str = $ret['CPU'] . " CPU ; " . $ret['RAM'] . " 内存 ; " . $ret['DISK'] . "G 硬盘 ; 系统：" . $ret['OS'] . " ; " . $bandwidth . " 时长：" . $time . " ; 数量：". $ret['Count'] ."<br/>";
			
			$this->assign('url', C('INTERFACE_URL')."/alipay/order_pay.php");
			$this->assign('DepartCode', "ESS");
			$this->assign('OrderCode', $tem['Code']);
					
			$name = urlencode("睿江云主机");
			$name = str_replace('%C2%80','',$name);
			$this->assign('OrderName', urldecode($name));
			
			$this->assign('OrderDesc', $str);
			$this->assign('OrderMoney', $ret['Price']);			
			$this->display();
		}else{			
			$this->gotoAlipay('ESS',$_POST['OrderCode'],$_POST['OrderName'],$_POST['OrderDesc'],$_POST['OrderMoney'],$_POST['OrderUrl']);
		}
	}
	//支付宝续费
	public function addRenew(){
		//先添加order
		$login = M()->table('Ad_Login');
		$ret = $login->where("Name='".$_SESSION['user']."'")->find();
		$order = M();					
		$orderVPS = M();			
		$VPSPay = M();
		$ord_count = count($order->query("select * from `Ad_Order` where CreateTS between '".date('Y-m-d',time())." 00:00:00"."'  and '".date('Y-m-d',time())." 23:59:59"."' "));
		$ord_code = "WEB".date('YmdHis',time()).str_pad($ord_count+1,5,'0',STR_PAD_LEFT);
		//添加订单
		$data['Login_ID'] = $ret['ID'];
		$data['Code'] = $ord_code;
		$data['IsPay'] = 0;
		$data['CreateTS'] = date('Y-m-d H:i:s');
		$data['ContractCode'] = '';
		$data['Money'] = $this->returnPrice($_POST['cpu'],$_POST['ram'],$_POST['disk'],$_POST['bgp'],$_POST['iplc'],$_POST['num'],$_POST['time']);
		//$data['PayType'] = $_POST['payType'];
		$data['Type'] = '续费';
		$data['Old_Order_ID'] = $_POST['oid'];
		$is_ok = $order->table('Ad_Order')->add($data);
		//添加vps订单
		$data2['Order_ID'] = $is_ok;
		$data2['CPU'] = $_POST['cpu'];
		$data2['RAM'] = $_POST['ram'];
		$data2['DISK'] = $_POST['disk'];	
		$data2['BandWidthBGP'] = $_POST['bgp'];
		$data2['BandWidthIPLC'] = $_POST['iplc'];
		$data2['OS'] = $_POST['os'];
		$data2['HouseName'] = $_POST['house'];	
		$data2['Price'] = $this->returnPrice($_POST['cpu'],$_POST['ram'],$_POST['disk'],$_POST['bgp'],$_POST['iplc'],$_POST['num'],$_POST['time']);
		$data2['Time'] = $_POST['time'];
		$data2['Count'] = $_POST['count'];
		$orderVPS->table('Ad_OrderNeed')->add($data2);	
		
		$tem['id'] = $is_ok;
		$tem['time'] = $_POST['time'];
		$tem['oid'] = $_POST['oid'];
		$this->ajaxReturn($tem,'success',1);
		
	}
	//修改到期时间
	public function updateOrder(){
		$Order = M();
		if($this->returnOrderStatus($_POST['id']) != "SUCC"){
			$this->ajaxReturn('续费云主机失败，请联系管理员。','error',0);		
		}
		
		$data = array('IsPay'=>1,'PayTS'=>date('Y-m-d H:i:s'));
		$is_ok = $Order->table('Ad_Order')->where('ID='.$_POST['id'])->setField($data);		
		if($is_ok === false){
			$this->ajaxReturn('续费云主机失败，请联系管理员。','error',0);
		}
		
		$VPSPay = M();
		$time;
		$ret = $VPSPay->table('Ad_OrderVPSBuy')->where('Order_ID='.$_POST['oid'])->find();
		//修改vspPay
		if(strtotime($ret['PayEnd']) < strtotime(date('Y-m-d H:i:s',time()))){
			if($_POST['time']=='月'){
				$time = date('Y-m-d H:i:s',strtotime('+1 month',strtotime(date('Y-m-d H:i:s',time()))));
			}else if($_POST['time']=='半年'){
				$time = date('Y-m-d H:i:s',strtotime('+6 month',strtotime(date('Y-m-d H:i:s',time()))));
			}else if($_POST['time']=='年'){
				$time = date('Y-m-d H:i:s',strtotime('+12 month',strtotime(date('Y-m-d H:i:s',time()))));
			}
		}else{
			if($_POST['time']=='月'){
				$time = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($ret['PayEnd'])));
			}else if($_POST['time']=='半年'){
				$time = date('Y-m-d H:i:s',strtotime('+6 month',strtotime($ret['PayEnd'])));
			}else if($_POST['time']=='年'){
				$time = date('Y-m-d H:i:s',strtotime('+12 month',strtotime($ret['PayEnd'])));
			}
		}
		$is_ok2 = $VPSPay->table('Ad_OrderVPSBuy')->where('Order_ID='.$_POST['oid'])->setField('PayEnd',$time);
		if($is_ok2===false){			
			$this->ajaxReturn('续费云主机失败，请联系管理员。','error',0);
		}else{
			$this->ajaxReturn(1,'success',1);
		}
	}
	//余额续费
	public function renewOrder2(){		
		//余额不足
		if($this->returnBalance()<$this->returnPrice($_POST['cpu'],$_POST['ram'],$_POST['disk'],$_POST['bgp'],$_POST['iplc'],$_POST['num'],$_POST['time'])){
			$this->ajaxReturn('对不起，您的账户余额不足，使用支付宝合并付款？','error',0);	
		}
		$login = M()->table('Ad_Login');
		$ret = $login->where("Name='".$_SESSION['user']."'")->find();
		$order = M();					
		$orderVPS = M();			
		$VPSPay = M();
		
		$ord_count = count($order->query("select * from `Ad_Order` where CreateTS between '".date('Y-m-d',time())." 00:00:00"."'  and '".date('Y-m-d',time())." 23:59:59"."' "));
		$ord_code = "WEB".date('YmdHis',time()).str_pad($ord_count+1,5,'0',STR_PAD_LEFT);
		//添加订单
		$data['Login_ID'] = $ret['ID'];
		$data['Code'] = $ord_code;
		$data['IsPay'] = 1;
		$data['CreateTS'] = date('Y-m-d H:i:s');
		$data['PayTS'] = date('Y-m-d H:i:s');
		$data['ContractCode'] = '';
		$data['Money'] = $this->returnPrice($_POST['cpu'],$_POST['ram'],$_POST['disk'],$_POST['bgp'],$_POST['iplc'],$_POST['num'],$_POST['time']);
		//$data['PayType'] = $_POST['payType'];
		$data['Type'] = '续费';
		$data['Old_Order_ID'] = $_POST['oid'];
		$is_ok = $order->table('Ad_Order')->add($data);
		
		//添加vps订单
		$data2['Order_ID'] = $is_ok;
		$data2['CPU'] = $_POST['cpu'];
		$data2['RAM'] = $_POST['ram'];
		$data2['DISK'] = $_POST['disk'];
		$data2['BandWidthBGP'] = $_POST['bgp'];
		$data2['BandWidthIPLC'] = $_POST['iplc'];
		$data2['OS'] = $_POST['os'];
		$data2['HouseName'] = $_POST['house'];
		$data2['Price'] = $this->returnPrice($_POST['cpu'],$_POST['ram'],$_POST['disk'],$_POST['bgp'],$_POST['iplc'],$_POST['num'],$_POST['time']);
		$data2['Time'] = $_POST['time'];
		$data2['Count'] = $_POST['count'];
		$orderVPS->table('Ad_OrderNeed')->add($data2);	
		$time;
		$ret = $VPSPay->table('Ad_OrderVPSBuy')->where('Order_ID='.$_POST['oid'])->find();
		
		//修改vspPay
		if($_POST['time']=='月'){
			$time = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($ret['PayEnd'])));
		}else if($_POST['time']=='半年'){
			$time = date('Y-m-d H:i:s',strtotime('+6 month',strtotime($ret['PayEnd'])));
		}else if($_POST['time']=='年'){
			$time = date('Y-m-d H:i:s',strtotime('+12 month',strtotime($ret['PayEnd'])));
		}
		$is_ok2 = $VPSPay->table('Ad_OrderVPSBuy')->where('Order_ID='.$_POST['oid'])->setField('PayEnd',$time);
		if($is_ok2===false){			
			$this->ajaxReturn('续费云主机失败，请联系管理员。','error',0);
		}else{
			$this->ajaxReturn(1,'success',1);
		}
	}
	//订单
	public function order(){
		if(!empty($_SESSION['user'])){
			if(empty($_GET['type'])){
				$login = M()->table('Ad_Login');
				$ret = $login->where("Name='".$_SESSION['user']."'")->find();
				$order = M();					
				$orderVPS = M();
				$ord_count = count($order->query("select * from `Ad_Order` where CreateTS between '".date('Y-m-d',time())." 00:00:00"."'  and '".date('Y-m-d',time())." 23:59:59"."' "));
				$ord_code = "WEB".date('YmdHis',time()).str_pad($ord_count+1,5,'0',STR_PAD_LEFT);
				//添加订单
				$data['Login_ID'] = $ret['ID'];
				$data['Code'] = $ord_code;
				$data['IsPay'] = 0;
				$data['CreateTS'] = date('Y-m-d H:i:s');
				$data['ContractCode'] = '';
				$data['Type'] = '购买';
				$data['Old_Order_ID'] = '';
				$data['Money'] = $this->returnPrice($_GET['cpu'],$_GET['ram'],$_GET['disk'],$_GET['bgp'],$_GET['iplc'],$_GET['num'],$_GET['time']);
				$is_ok = $order->table('Ad_Order')->add($data);
				
				//添加vps订单
				$data2['Order_ID'] = $is_ok;
				$data2['CPU'] = $_GET['cpu'];
				$data2['RAM'] = $_GET['ram'];
				$data2['DISK'] = $_GET['disk'];
				$data2['BandWidthBGP'] = $_GET['bgp'];
				$data2['BandWidthIPLC'] = $_GET['iplc'];
				
				$price = $this->returnPrice($_GET['cpu'],$_GET['ram'],$_GET['disk'],$_GET['bgp'],$_GET['iplc'],$_GET['num'],$_GET['time']);
				$data2['Price'] = $price;
				
				$data2['OS'] = $_GET['sys'];
				$data2['HouseName'] = $_GET['house'];
				$data2['Time'] = $_GET['time'];
				$data2['Count'] = $_GET['num'];
				$orderVPS->table('Ad_OrderNeed')->add($data2);
				
				$this->assign('id',$is_ok);
				$this->assign('relt',$data2);
			}else{
				$data2['Order_ID'] = $is_ok;
				$data2['CPU'] = $_GET['cpu'];
				$data2['RAM'] = $_GET['ram'];
				$data2['DISK'] = $_GET['disk'];
				$data2['BandWidthBGP'] = $_GET['bgp'];
				$data2['BandWidthIPLC'] = $_GET['iplc'];
				$data2['HouseName'] = $_GET['house'];
				
				$price = $this->returnPrice($_GET['cpu'],$_GET['ram'],$_GET['disk'],$_GET['bgp'],$_GET['iplc'],$_GET['num'],$_GET['time']);
				$data2['Price'] = $price;
				
				$data2['OS'] = $_GET['sys'];
				$data2['Time'] = $_GET['time'];
				$data2['Count'] = $_GET['num'];
				
				$this->assign('id',$_GET['id']);
				$this->assign('relt',$data2);
			}
			$this->assign('balance',$this->returnBalance());
			$this->display();
		}else{
			$this->redirect("/Index/login");
		}
	}
	//注销
	public function logout(){
		unset($_SESSION['user']);
		unset($_SESSION['id']);
		unset($_SESSION['customID']);
		unset($_SESSION['type']);
		unset($_SESSION['audit']);
		unset($_SESSION['name']);
		$this->redirect('Index/login');
	}
	//登陆div
	public function login_div(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){			
			$this->display();
		}else{
			if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['checkcode'])){
				if(md5(trim($_POST['checkcode'])) != $_SESSION['myverify']){
					$this->assign('error', "验证码错误，请输入正确的验证码！");
					$this->assign('mail', $_POST['username']);
					$this->display();
				}else{
					$cnd["Name"] = trim($_POST['username']);
					$cnd["Passwd"] = trim($_POST['password']); 
					$list = M()->table("Ad_Login");
					$rslt = $list->where($cnd)->find();
					$list->table('Ad_Login')->where($cnd)->setField('LastLoginTime',date('Y-m-d H:i:s'));
					if(!empty($rslt)){
						$_SESSION['user'] = $rslt["Name"];
						$_SESSION['customID'] = $rslt["GlobalCustom_ID"];
						$_SESSION['id'] = $rslt["ObjID"];
						$_SESSION['type'] = $rslt["ObjType"];	
						$_SESSION['audit'] = $rslt["Audit"];
						if($rslt["ObjType"]==1){
							$person = M();
							$_SESSION['name'] = $person->table('Ad_Person')->where('ID='.$rslt["ObjID"])->getField('TrueName');
						}else{							
							$company= M();
							$_SESSION['name'] = $person->table('Ad_Company')->where('ID='.$rslt["ObjID"])->getField('CompanyName');
						}															
						$this->display();
					}else {
						$this->assign('error', "用户名或密码错误，请核对后再试！");
						$this->assign('mail', $_POST['username']);
						$this->display();
					}
				}
			}
		}		
	}
	//登陆
	public function login(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){			
			/*
			unset($_SESSION['user']);
			unset($_SESSION['id']);
			unset($_SESSION['customID']);
			unset($_SESSION['type']);
			unset($_SESSION['audit']);
			*/
			$this->display();
		}
		else{
			if(isset($_POST['username']) && isset($_POST['password']) ){//&& isset($_POST['checkcode'])
				/*if(md5(trim($_POST['checkcode'])) != $_SESSION['myverify']){
					$this->assign('error', "验证码错误，请输入正确的验证码！");
					$this->assign('mail', $_POST['username']);
					$this->display();
				}else{*/
					$cnd["Name"] = trim($_POST['username']);
					$cnd["Passwd"] = trim($_POST['password']); //md5()
					//print_r($cnd["mail"]);
					//print_r($cnd["pwd"]);
					//exit;
					$list = M()->table("Ad_Login");
					$rslt = $list->where($cnd)->find();					
					$list->table('Ad_Login')->where($cnd)->setField('LastLoginTime',date('Y-m-d H:i:s'));
					//print_r($list->getLastSQL());exit;
					if(!empty($rslt)){
						$_SESSION['user'] = $rslt["Name"];
						$_SESSION['customID'] = $rslt["GlobalCustom_ID"];
						$_SESSION['id'] = $rslt["ObjID"];
						$_SESSION['type'] = $rslt["ObjType"];
						$_SESSION['audit'] = $rslt["Audit"];
						if($rslt["ObjType"]==1){
							$person = M();
							$_SESSION['name'] = $person->table('Ad_Person')->where('ID='.$rslt["ObjID"])->getField('TrueName');
						}else{							
							$company= M();
							$_SESSION['name'] = $company->table('Ad_Company')->where('ID='.$rslt["ObjID"])->getField('CompanyName');
						}
						//获取vps数量
						$order = M();
						$orderID = $order->table('Ad_Order')->where('Login_ID='.$rslt['ID'])->getField('ID',true);
						$ids = "";
						foreach($orderID as $val){
							$ids .= $val . ',';
						}
						$ids = substr($ids,0,-1);
						
						$Ad_OrderVPSBuy = M();
						$vpsList = $Ad_OrderVPSBuy->query('select * from Ad_OrderVPSBuy ov left join Ad_VPSBuy v on v.ID=ov.VPSBuy_ID where ov.Order_ID in ('.$ids.') ');
						if($vpsList==0){
							header("Location: ".__APP__."/Index/buy");
						}else{
							header("Location: ".__APP__."/Index/account");	
						}
					} else {
						//$this->success("用户名或密码输入错误，请核对后再试！",U("/Index/login"));
						//$this->ajaxReturn( "用户名或密码错误，请核对后再试！",'error',0);
						$this->assign('error', "用户名或密码错误，请核对后再试！");
						$this->assign('mail', $_POST['username']);
						$this->display();
					}
				//}
			}
		}
    }
	//查询代金券是否有效
	public function selectCoupon(){
		if(!empty($_POST['code']) && !empty($_POST['pwd'])){
			$login = M();
			$user = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
			$coupon = M();
			$ret = $coupon->table('Ad_CashCoupon')->where("Code='".$_POST['code']."' and Passwd='".$_POST['pwd']."'")->find();
			if(empty($ret)){
				$this->ajaxReturn('对不起，请核对您的代金券号与密码是否正确。','error',0);
			}
			$this->ajaxReturn($ret,'success',1);
		}
	}
	//使用代金券
	public function updateCoupon(){
		if(!empty($_POST['id'])){
			$coupon = M();			
			$login = M();
			$user = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
			$data = array('Login_ID'=>$user['ID'],'Name'=>$_SESSION['user'],'UseTS'=>date('Y-m-d H:i:s',time()));
			$is_ok = $coupon->table('Ad_CashCoupon')->where('ID='.$_POST['id'])->setField($data);
			if($is_ok===false){
				$this->ajaxReturn('使用代金券充值失败，请联系管理员。','error',0);			
			}else{
				$this->ajaxReturn(number_format($this->returnBalance(),2),'success',1);
			}
		}
	}
	//公司用户信息写入
	public function company(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			if(!empty($_SESSION['userID'])){
				$this->assign('id',$_SESSION['userID']);
				$this->display();
			}else{
				header("Location: ".__APP__."/Index/regist");	
			}
		}else{
			if(isset($_POST['address']) && isset($_POST['farenname']) && isset($_POST['farenphone']) && isset($_POST['beian']) && isset($_POST['linkmanname']) && isset($_POST['linkmanphone']) && isset($_POST['linkmanidnum']) && isset($_POST['linkmanadd'])){
				$Company = M()->table("Ad_Company");			
				$data['Address'] = $_POST['address'];
				$data['LegalPersonName'] = $_POST['farenname'];
				$data['LegalPersonPhone'] = $_POST['farenphone'];
				$data['BeiAn'] = $_POST['beian'];
				$data['LinkmanName'] = $_POST['linkmanname'];
				$data['LinkmanPhone'] = $_POST['linkmanphone'];
				$data['LinkmanIdNum'] = $_POST['linkmanidnum'];
				$data['LinkmanAdd'] = $_POST['linkmanadd'];
				$isok = $Company->where('ID='.$_POST['id'])->save($data);
				if($isok===false){
					$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
				}else{
					$is_ok = $this->send_mail($_SESSION['user'],'睿江云平台 - 邮箱验证','尊敬的'.$_SESSION['user'].'用户，您好，点击 http://ecloud.efly.cc/index.php/Index/activation?mail='.base64_encode($_SESSION['user']).' 链接激活购买，如果不能点击，复制该地址在地址栏回车。');
					if($is_ok=='ok'){
						$this->ajaxReturn("注册用户成功，3秒跳转用户中心页面。",'success',1);
					}else{
						$this->ajaxReturn('激活购买邮件发送失败，请联系管理员。','error',0);
					}
				}
			} else {
				$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
			}
		}
	}
	//个人用户信息写入
	public function person(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			if(!empty($_SESSION['userID'])){
				$this->assign('id',$_SESSION['userID']);
				$this->display();
			}else{
				header("Location: ".__APP__."/Index/regist");	
			}
		}else{
			if(isset($_POST['truename']) && isset($_POST['address']) && isset($_POST['address_']) && isset($_POST['phone']) && isset($_POST['idnum']) && isset($_POST['beian'])){
				$Person = M()->table("Ad_Person");
				$data['TrueName'] = $_POST['truename'];
				$data['IDNum'] = $_POST['idnum'];
				$data['Address'] = $_POST['address'];
				$data['Address_'] = $_POST['address_'];
				$data['Phone'] = $_POST['phone'];
				$data['BeiAn'] = $_POST['beian'];
				$isok = $Person->where('ID='.$_POST['id'])->save($data);
				if($isok===false){
					$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
				}else{
					$_SESSION['name'] = $_POST['truename'];
					$is_ok = $this->send_mail($_SESSION['user'],'睿江云平台 - 邮箱验证','尊敬的'.$_SESSION['user'].'用户，您好，点击 http://ecloud.efly.cc/index.php/Index/activation?mail='.base64_encode($_SESSION['user']).' 链接激活购买，如果不能点击，复制该地址在地址栏回车。');
					if($is_ok=='ok'){
						$this->ajaxReturn("注册用户成功，3秒跳转用户中心页面。",'success',1);
					}else{
						$this->ajaxReturn('激活购买邮件发送失败，请联系管理员。','error',0);
					}
				}
			} else {
				$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
			}
		}
	}
	public function sendMail(){
		$is_ok = $this->send_mail($_SESSION['user'],'睿江云平台 - 邮箱验证','尊敬的'.$_SESSION['user'].'用户，您好，点击 http://ecloud.efly.cc/index.php/Index/activation?mail='.base64_encode($_SESSION['user']).' 链接激活购买，如果不能点击，复制该地址在地址栏回车。');
		if($is_ok=='ok'){
			$this->ajaxReturn(1,'success',1);
		}else{
			$this->ajaxReturn('激活购买邮件失败，请联系管理员。','error',0);
		}
	}
	//检查用户信息是否完善
	public function checkUserInfo(){
		$login = M()->table('Ad_Login');
		$user = $login->where("Name='".$_SESSION['user']."'")->find();
		
		$is_buy = false;
		if($user['ObjType']==1){
			$person = M()->table('Ad_Person');
			$ret = $person->where('ID='.$user['ObjID'])->find();
			if(!empty($ret['TrueName']) && !empty($ret['TrueName'])){
				$is_buy = true;
			}
		}else{
			$company = M()->table('Ad_Company');
			$ret = $company->where('ID='.$user['ObjID'])->find();
			if(!empty($ret['CompanyName']) && !empty($ret['LinkmanIdNum'])){
				$is_buy = true;
			}
		}
		if($is_buy){
			$this->ajaxReturn(1,'success',1);
		}else{
			$this->ajaxReturn(0,'error',0);
		}
	}
	//验证是否能购买
	public function checkAudit(){
		$login = M()->table('Ad_Login');
		$is_buy = $login->where("Name='".$_SESSION['user']."'")->getField('Audit',true);
		$this->ajaxReturn($is_buy,'success',1);
	}
	//激活
	public function activation(){
		header("Content-Type: text/html; charset=utf-8");
		$login = M()->table('Ad_Login');		
		$is_ok = $login->where("Name='".base64_decode($_GET['mail'])."'")->setField('Audit',1);
		if($is_ok===false){			
			echo "<script type='text/javascript'>alert('激活失败，请联系管理员。');window.close();</script>" ;	
		}else{
			$_SESSION['audit'] = 1;
			echo "<script type='text/javascript'>alert('成功激活');window.location.href='http://ecloud.efly.cc';</script>" ;	
		}
	}
	//个人注册
	public function regist(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			$this->display();
		}else{
			if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['checkcode'])){
				if(md5(trim($_POST['checkcode'])) != $_SESSION['myverify']){
					$this->ajaxReturn("验证码错误，请输入正确的验证码！",'error',0);
				}else{
					$Login = M()->table("Ad_Login");
					$is_exist = $Login->where("Name='".$_POST['username']."'")->find();
					if(!empty($is_exist)){
						$this->ajaxReturn("您输入的电子邮箱已注册，<a href='forget.html'>忘记密码？</a>",'error',0);
					}
					$person = M()->table("Ad_Person");
					$obj["Email"] = trim($_POST['username']);
					$ret = $person->data($obj)->add();
					if($ret){
						$data["ObjType"] = 1;
						$data["ObjID"] = $ret;
						$data["GlobalCustom_ID"] = ECS.sprintf('%011d', $ret);
						$data["Name"] = trim($_POST['username']);
						$data["Passwd"] = trim($_POST['password']); //md5()
						$data["RegTime"] = date('Y-m-d H:i:s',time());

						$list = M()->table("Ad_Login");
						$rslt = $list->data($data)->add();
						//print_r($list->getLastSQL());exit;
						if($rslt){
							$_SESSION['user'] = $data["Name"];
							$_SESSION['customID'] = $data["GlobalCustom_ID"];
							$_SESSION['id'] = $data["ObjID"];
							$_SESSION['type'] = $data["ObjType"];
							$_SESSION['audit'] = $data["Audit"];
							$_SESSION['userID'] = $ret;
							
							$data2 = file_get_contents(C('INTERFACE_URL').'event/async_event_push.php?EventName=睿江云用户注册&EventData={"ObjType":'.$data["ObjType"].',"GlobalCustom_ID":"'.$data["GlobalCustom_ID"].'","Name":"'.$data["Name"].'"}');
							
							$this->ajaxReturn($ret,'success',1);
						} else {
							$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
						}
					} else {
						//$this->success("用户名或密码输入错误，请核对后再试！",U("/Index/login"));
						$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
					}
				}
			}
		}
	}
	//企业注册
	public function cregist(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			$this->display();
		}else{
			if(isset($_POST['companyname']) && isset($_POST['password']) && isset($_POST['checkcode'])){
				if(md5(trim($_POST['checkcode'])) != $_SESSION['myverify']){
					$this->ajaxReturn("验证码错误，请输入正确的验证码！",'error', 0);
				}else{
					$Login = M()->table("Ad_Login");
					$is_exist = $Login->where("Name='".$_POST['useremail']."'")->find();
					if(!empty($is_exist)){
						$this->ajaxReturn("您输入的电子邮箱已注册，<a href='forget.html'>忘记密码？</a>",'error', 0);
					}
					$company = M()->table("Ad_Company");
					$obj["CompanyName"] = trim($_POST['companyname']);
					$obj["CompanyCode"] = trim($_POST['companycode']);
					$obj["LinkmanEmail"] = trim($_POST['useremail']);
					$ret = $company->data($obj)->add();
					if($ret){
						$data["ObjType"] = 2;
						$data["ObjID"] = $ret;
						$data["GlobalCustom_ID"] = ECS.sprintf('%011d', $ret);
						$data["Name"] = trim($_POST['useremail']);
						$data["Passwd"] = trim($_POST['password']); //md5()
						$data["RegTime"] = date('Y-m-d H:i:s',time());
						$list = M()->table("Ad_Login");
						$rslt = $list->data($data)->add();
						//print_r($list->getLastSQL());exit;
						if($rslt){
							$_SESSION['user'] = $data["Name"];
							$_SESSION['customID'] = $data["GlobalCustom_ID"];
							$_SESSION['id'] = $data["ObjID"];
							$_SESSION['type'] = $data["ObjType"];
							$_SESSION['audit'] = $data["Audit"];
							$_SESSION['name'] = $_POST['companyname'];
							$_SESSION['userID'] = $ret;
							
							$data2 = file_get_contents(C('INTERFACE_URL').'event/async_event_push.php?EventName=睿江云用户注册&EventData={"ObjType":'.$data["ObjType"].',"GlobalCustom_ID":"'.$data["GlobalCustom_ID"].'","Name":"'.$data["Name"].'"}');
							
							$this->ajaxReturn($ret,'success',1);
						} else {
							$this->ajaxReturn("企业用户注册失败，请稍后再试！",'error',0);
						}
					} else {
						//$this->success("用户名或密码输入错误，请核对后再试！",U("/Index/login"));
						$this->ajaxReturn("企业用户注册失败，请稍后再试！",'error',0);
					}
				}
			}
		}
	}
	//发送随即码
	public function sendCode(){
		if(!empty($_POST['useremail'])){
			$Login = M()->table("Ad_Login");
			$is_exist = $Login->where("Name='".$_POST['useremail']."'")->find();
			if(empty($is_exist)){
				$this->ajaxReturn("您输入的电子邮箱不存在，请重新输入！",'error', 0);
			}
			$num = rand(100000,999999);
			$_SESSION['num'] = $num;
			$is_ok = $this->send_mail($_POST['useremail'],'睿江云平台 - 修改密码','尊敬的'.$_POST['useremail'].'用户，您好，本次修改密码的随机验证码为'.$num);
			if($is_ok=='ok'){
				$this->ajaxReturn('验证码成功发送。','success',1);
			}else{
				$this->ajaxReturn('获取验证码失败，请联系管理员。','error',0);
			}
		}
	}
	//发送邮件
	public function send_mail($add,$title, $content){
        //echo $content;
        $mail = new PHPMailer(); //建立邮件发送类
        $address = $add;//接收邮件地址
        $mail->IsSMTP(); // 使用SMTP方式发送
        $mail->Host = "smtp.126.com"; // 您的企业邮局域名
        $mail->SMTPAuth = true; // 启用SMTP验证功能
        $mail->Username = "eflycloud@126.com"; // 发送方邮件地址(请填写完整的email地址)
        $mail->Password = "wwweflycloud"; //发送放邮件密码
        $mail->Port=25;
        $mail->From = "eflycloud@126.com"; //发送方邮件地址
        $mail->FromName = "睿江云平台";
        $mail->AddAddress("$address", "$address");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
        //$mail->AddReplyTo("", "");
 
        //$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件

       //$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
        $mail->CharSet = "utf-8";
        $mail->Subject = $title; //邮件标题
        $mail->Body = $content; //邮件内容
        //$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
		if(!$mail->Send()){
            return $mail->ErrorInfo;
        }
        return "ok";
    }
	//忘记密码
	public function forget(){
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$this->display();	
		}else{
			if(!empty($_POST['mail']) && !empty($_POST['code'])){
				if(trim($_POST['code']) != $_SESSION['num']){
					$this->ajaxReturn('验证码错误，请输入正确的验证码！', 'error',0);
				}else{
					$this->ajaxReturn(1, 'success',1);
				}
			}
		}
		
	}
	//修改密码
	public function updatepwd(){
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$this->assign('mail', $_GET['mail']);
			$this->display();	
		}else{
			if(!empty($_POST['mail']) && !empty($_POST['pwd'])){
				$login = M()->table('Ad_Login');
				$pwd = trim($_POST['pwd']);
				$ret = $login->where("Name='".$_POST['mail']."'")->find();	
				$is_ok = $login->table('Ad_Login')->where("Name='".$_POST['mail']."'")->setField('Passwd',$pwd);
				if($is_ok===false){
					$this->ajaxReturn('修改密码错误，请联系管理员！', 'error',0);
				}else{
					$_SESSION['user'] = $ret["Name"];
					$_SESSION['customID'] = $ret["GlobalCustom_ID"];
					$_SESSION['id'] = $ret["ObjID"];
					$_SESSION['type'] = $ret["ObjType"];
					$_SESSION['audit'] = $ret["Audit"];
					$this->ajaxReturn(1, 'success',1);
				}
			}
		}
	}
	//发票合同
	public function contract(){
		$con = "content_c1";
		if(!empty($_GET['c'])){
			$con = $_GET['c'];
		}
		$this->assign('con_id', $con);
		$this->display();	
	}
	//备案
	public function beian(){
		$Ad_TubeBureau = M()->table('Ad_TubeBureau');
		$list = $Ad_TubeBureau->where()->select();
		$beian = "content_b1";
		if(!empty($_GET['b'])){
			$beian = $_GET['b'];
		}
		$this->assign("list",$list);
		$this->assign('beian_id', $beian);
		$this->display();	
	}
	//举报常见问题
	public function question(){
		$question = "question_1";
		if(!empty($_GET['q'])){
			$question = $_GET['q'];
		}
		$this->assign('q',$question);
		$this->display();
	}
	//管局要求
	public function tubeBureau(){
		if(!empty($_GET['t'])){
			$TubeBureau = M()->table('Ad_TubeBureau');
			$tubeBureau = $TubeBureau->where('ID='.$_GET['t'])->find();
			$this->assign('tubeBureau',$tubeBureau);
		}
		$this->display();
	}
	//确认价格
	public function returnPrice($cpu,$ram,$disk,$bgp,$iplc,$count,$time){
		$price;
		$CPU[1] = 5;
		$CPU[2] = 110;
		$CPU[4] = 240;
		$CPU[8] = 500;

		$RAM[512] = 20;
		$RAM[1] = 40;
		$RAM[2] = 85;
		$RAM[4] = 110;
		$RAM[8] = 220;
		$RAM[16] = 450;
		$RAM[32] = 950;

		$DISK = 20;

		$BGP[]=array(0,5,20);
		$BGP[]=array(5,10,50);
		$BGP[]=array(10,30,70);
		$BGP[]=array(30,50,75);
		$BGP[]=array(50,100,75);
		$BGP[]=array(100,200,80);

		$IPLC[]=array(0,2,400);
		$IPLC[]=array(2,5,550);
		$IPLC[]=array(5,10,500);
		$IPLC[]=array(10,50,400);
		$IPLC[]=array(50,999999,360);

		$price_disk = (@$disk / 50) * $DISK;
		foreach($BGP as $v){
		    if(@$bgp >= $v[0] && @$bgp <= $v[1]){
				$price_bgp = @$bgp * $v[2];
				break;
		    }
		}
		foreach($IPLC as $v){
		    if(@$iplc >= $v[0] && @$iplc <= $v[1]){
				$price_iplc = @$iplc * $v[2];
				break;
		    }
		}
		switch($time){
			case '月':
				$time = 1;
				break;
			case '半年':
				$time = 5;
				break;
			case '年':
				$time = 10;
				break;
		}
		$price = ($CPU[(int)$cpu] + $RAM[(int)$ram] + $price_disk + $price_bgp + $price_iplc) * $count * $time ;
		return $price;
	}
	
	public function checkPrice(){
	    if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$CPU[1] = 5;
			$CPU[2] = 110;
			$CPU[4] = 240;
			$CPU[8] = 500;
	
			$RAM[512] = 20;
			$RAM[1] = 40;
			$RAM[2] = 85;
			$RAM[4] = 110;
			$RAM[8] = 220;
			$RAM[16] = 450;
			$RAM[32] = 950;
	
			$DISK = 20;
	
			$BGP[]=array(0,5,20);
			$BGP[]=array(5,10,50);
			$BGP[]=array(10,30,70);
			$BGP[]=array(30,50,75);
			$BGP[]=array(50,100,75);
			$BGP[]=array(100,200,80);
	
			$IPLC[]=array(0,2,400);
			$IPLC[]=array(2,5,550);
			$IPLC[]=array(5,10,500);
			$IPLC[]=array(10,50,400);
			$IPLC[]=array(50,999999,360);

			$price_disk=(@$_GET['disk'] / 50) * $DISK;
			foreach($BGP as $v){
				if(@$_GET['bgp'] >= $v[0] && @$_GET['bgp'] <= $v[1]){
				$price_bgp = @$_GET['bgp'] * $v[2];
				break;
				}
			}
			foreach($IPLC as $v){
				if(@$_GET['iplc'] >= $v[0] && @$_GET['iplc'] <= $v[1]){
				$price_iplc = @$_GET['iplc'] * $v[2];
				break;
				}
			}
			$price = $CPU[(int)$_GET['cpu']] + $RAM[(int)$_GET['ram']] + $price_disk + $price_bgp + $price_iplc;
			//$this->ajaxReturn(1, 'success', 1);
			$this->ajaxReturn($price, 'success', 1);
	   	}
	}
}
