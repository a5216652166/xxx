<?php
// 本类由系统自动生成，仅供测试用途
class ServicesAction extends Action {
	//查询我的服务器
	public function index(){
		if(isset($_SESSION['user'])){
			$list = M()->table("Order");
			$cnd["Custom_ID"] = $_SESSION['customID'];
			$rslt = $list->where($cnd)->getField("ContractCode",true);
			//print_r($list->getlastsql());
			if(!empty($rslt)){
				$str = join(",", $rslt);
				//print_r($str);exit;
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_by_contract&Contract_Code=".$str."&Dev_Type=服务器");
				$devList = json_decode($data,true);
				foreach($devList as $key => $val){
					$devList[$key]['MyCode'] = substr($val['Code'],0,8);
				}
//				print_r($devList);exit;
				$this->assign('devList', $devList);
				$this->assign('devListListCount', count($devList));
			}
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//我的云主机
	public function vps(){
		if(isset($_SESSION['user'])){			
			$list = M()->table("Order");
			$cnd["Custom_ID"] = $_SESSION['customID'];
			$rslt = $list->where($cnd)->getField("ContractCode",true);			
			if(!empty($rslt)){
				$str = join(",", $rslt);
				$str = trim($str,",'");
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_by_contract&Contract_Code=".$str."&Dev_Type=vps");
				$vpsList = json_decode($data,true);
				foreach($vpsList as $key => $val){
					$vpsList[$key]['MyCode'] = substr($val['Code'],0,8);
				}
//				print_r($devList);exit;
				$this->assign('vpsList', $vpsList);
				$this->assign('vpsListListCount', count($vpsList));
			}
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//操作虚拟机
	public function operationVM(){
		$data = file_get_contents(C('OPERATION_VM')."/?opt=setvmpower&id=".$_POST['code'].'&state='.$_POST['opt']);
		$ret = json_decode($data,true);
		$this->ajaxReturn($ret['ret'],'success',1);
	}
	//查询我的ip
	public function ip(){
		if(isset($_SESSION['user'])){
			$list = M()->table("Order");
			$cnd["Custom_ID"] = $_SESSION['customID'];
			$rslt = $list->where($cnd)->getField("ContractCode",true);
			if(!empty($rslt)){
				$str = join(",", $rslt);
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/ip_query.php?opt=get_by_contract&Contract_Code=".$str);
				$ipList = json_decode($data,true);
				$this->assign('ipList', $ipList);
				$this->assign('ipListCount', count($ipList));
			}
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//查询我的cdn
	public function cdn(){
		if(isset($_SESSION['user'])){
			$list = M()->table("Order");
			$cnd["Custom_ID"] = $_SESSION['customID'];
			$rslt = $list->where($cnd)->getField("ContractCode",true);
			if(!empty($rslt)){
				$str = join(",", $rslt);
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/cdn_query.php?opt=get_by_contract&Contract_Code=".$str);
				$cdnList = json_decode($data,true);
				$this->assign('cdnList', $cdnList);
				$this->assign('cdnListCount', count($cdnList));
			}
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//查询我的带宽
	public function bandwidth(){
		if(isset($_SESSION['user'])){
			$list = M()->table("Order");
			$cnd["Custom_ID"] = $_SESSION['customID'];
			$rslt = $list->where($cnd)->getField("ContractCode",true);
			if(!empty($rslt)){
				$str = join(",", $rslt);
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/interface_query.php?opt=get_by_contract&Contract_Code=".$str);
				$bandwidthList = json_decode($data,true);
				$this->assign('bandwidthList', $bandwidthList);
				$this->assign('bwListCount', count($bandwidthList));
			}
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//查询我的机柜&机位
	public function cabinet(){
		if(isset($_SESSION['user'])){
			$list = M()->table("Order");
			$cnd["Custom_ID"] = $_SESSION['customID'];
			$rslt = $list->where($cnd)->getField("ContractCode",true);
			if(!empty($rslt)){
				$str = join(",", $rslt);
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/cabinet_query.php?opt=get_by_contract&Contract_Code=".$str);
				$cabinetList = json_decode($data,true);
				$this->assign('cabinetList', $cabinetList);
				$this->assign('cabinetListCount', count($cabinetList));
			}
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//查询我的dns
	public function dns(){
		if(isset($_SESSION['user'])){
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//流量查看
	public function traffic(){
		if(isset($_SESSION['user'])){
			$data = file_get_contents($_GET['url']);
			$this->assign('traffic', $data);
			$this->assign('spid', $_GET['spid']);
			$this->assign('oid', $_GET['oid']);
			$this->assign('name', $_GET['name']);
			$this->assign('interface', $_GET['interface']);
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//根据日期查询流量
	public function selTraffic(){
		if(isset($_SESSION['user'])){
			if(!empty($_POST['spid']) && !empty($_POST['oid']) && !empty($_POST['begin']) && !empty($_POST['end'])){
				$data = file_get_contents(C('INTERFACE_URL')."/idc_flow/traffic_query.php?StockProperty_ID=".$_POST['spid']."&Snmp_Oid=".$_POST['oid']."&Start=".$_POST['begin']."&End=".$_POST['end']);
				$this->ajaxReturn($data,'success',1);
			}
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//我的订单
	public function order(){
		if(isset($_SESSION['user'])){
			$order = M();			//IS_Pay='NO' and 
			$ordList = $order->query("select * from `Order` where Custom_ID='".$_SESSION['customID']."' order by ID desc ");
			//用户的订单
			$arr;			
			foreach($ordList as $key => $val){
				$arr[$key] = $this->getOrderList($val['ID']);
			}
			$this->assign('orderList', $arr);
			$this->display();
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	
	//用户订单
	public function getOrderList($id){
		//设备
		$devData = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free&ID=".$this->getDevID($id));
		$devList = json_decode($devData,true);
		//云主机
		$vpsData = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_vps&ID=".$this->getDevID($id));
		$vpsList = json_decode($vpsData,true);
		//IP
		$ip = M('Source_IP');
		$ipList = $ip->query('select si.*,p.Name as ProName,o.Count as Count from Source_IP si left join `Product` p on p.ID=si.Pro_ID  left join `Order_IP` o on o.IP_ID=si.ID where o.Order_ID='.$id.' and si.ID in ('.$this->getIpID($id).')');
		//带宽
		$bandWidth = M('Source_BandWidth');
		$bandWidthList = $bandWidth->query('select sb.*,p.Name as ProName,o.Count as Count from Source_BandWidth sb left join `Product` p on p.ID=sb.Pro_ID left join `Order_BandWidth` o on o.BandWidth_ID=sb.ID where  o.Order_ID='.$id.' and sb.ID in ('.$this->getBandwidthID($id).')');
		//带宽扩展
		$bandWidthExt = M('Source_BandWidthExt');
		$bandWidthExtList = $bandWidthExt->query('select sb.*,p.Name as ProName,o.Count as Count from Source_BandWidthExt sb left join `Product` p on p.ID=sb.Pro_ID left join `Order_BandWidthExt` o on o.BandWidthExt_ID=sb.ID where o.Order_ID='.$id.' and sb.ID in ('.$this->getBandwidthExtID($id).')');	
		//机柜
		$cabinet = M('Source_Cabinet');
		$cabinetList = $cabinet->query('select sc.*,p.Name as ProName,o.Count as Count from Source_Cabinet sc left join `Product` p on p.ID=sc.Pro_ID left join `Order_Cabinet` o on o.Cabinet_ID=sc.ID where o.Order_ID='.$id.' and sc.ID in ('.$this->getCabinetID($id).')');
		
		$orderList;
		//修改价格
		$data = file_get_contents(C('INTERFACE_URL')."/ess/ess_bill.php?Order_ID=".$id); 
		$dataList = json_decode($data,true);
		foreach($vpsList as $key=>$value){
			$orderList['vps'][$key] = $value;			
			$orderList['vps'][$key]['Price'] = $dataList['vps'][$value['ID']][0]['money'];
		}
		foreach($devList as $key=>$value){
			$orderList['dev'][$key] = $value;			
			$orderList['dev'][$key]['Price'] = $dataList['dev'][$value['ID']][0]['money'];
		}
		foreach($ipList as $key=>$value){
			$orderList['ip'][$key] = $value;
			$orderList['ip'][$key]['Price'] = $dataList['ip'][$value['ID']][0]['money'];
		}
		foreach($bandWidthList as $key=>$value){
			$orderList['bandwidth'][$key] = $value;
			$orderList['bandwidth'][$key]['Price'] = $dataList['bandwidth'][$value['ID']][0]['money'];
		}
		foreach($cabinetList as $key=>$value){
			$orderList['cabinet'][$key] = $value;
			$orderList['cabinet'][$key]['Price'] = $dataList['cabinet'][$value['ID']][0]['money'];
		}
		foreach($bandWidthExtList as $key=>$value){
			$orderList['bandwidthExt'][$key] = $value;
			//$orderList['bandwidthExt'][$key]['Price'] = $dataList['bandwidthExt'][$value['ID']][0]['money'];
		}
		$order = M();
		$tem = $order->table('Order')->where("ID=".$id)->find();
		
		$orderList['Ord_ID'] = $id;
		$orderList['Status'] = $tem['Status'];
		$orderList['DoReq'] = $tem['DoReq'];
		$orderList['IS_Pay'] = $tem['IS_Pay'];
		$orderList['Code'] = $tem['Code'];
		
		//工单信息
		$worklist = file_get_contents(C('INTERFACE_URL')."/ibss/wlist_query.php?opt=get_by_contract&Contract_Code='".$tem['ContractCode']."'"); 
		$worklist = json_decode($worklist,true);
		$orderList['worklist'] = $worklist;
		$orderList['DoRsp'] = $worklist[0]['statusDesc'];
		return $orderList;
	}
	//获取用户云主机的ID
	public function getVpsID($id){
		$order_dev = M('Order_Dev');
		$orderList = $order_dev->query("select * from `Order_Dev` where Order_ID=".$id);
		$str="";
		if(!empty($orderList)){
			for($i=0;$i<count($orderList);$i++){
				if($i!=count($orderList)-1){
					$str .= $orderList[$i]['Dev_ID'] . ",";
				}else{
					$str .= $orderList[$i]['Dev_ID'];
				}
			}	
		}else{
			$str = "-1";
		}
		return $str;
	}
	//获取用户设备的ID
	public function getDevID($id){
		$order_dev = M('Order_Dev');
		$orderList = $order_dev->query("select * from `Order_Dev` where Order_ID=".$id);
		$str="";
		if(!empty($orderList)){
			for($i=0;$i<count($orderList);$i++){
				if($i!=count($orderList)-1){
					$str .= $orderList[$i]['Dev_ID'] . ",";
				}else{
					$str .= $orderList[$i]['Dev_ID'];
				}
			}	
		}else{
			$str = "-1";
		}
		return $str;
	}
	//获取用户的带宽的ID
	public function getBandwidthID($id){
		$order_bandwidth = M('Order_BandWidth');
		$orderList = $order_bandwidth->query("select * from `Order_BandWidth` where Order_ID=".$id);
		$str="";
		if(!empty($orderList)){
			for($i=0;$i<count($orderList);$i++){
				if($i!=count($orderList)-1){
					$str .= $orderList[$i]['BandWidth_ID'] . ",";
				}else{
					$str .= $orderList[$i]['BandWidth_ID'];
				}
			}
		}else{
			$str = "-1";
		}
		return $str;
	}
	//获取用户的BandwidthExt的ID
	public function getBandwidthExtID($id){
		$order_bandwidthExt = M('Order_BandWidthExt');
		$orderList = $order_bandwidthExt->query("select * from `Order_BandWidthExt` where Order_ID=".$id);
		$str="";
		if(!empty($orderList)){
			for($i=0;$i<count($orderList);$i++){
				if($i!=count($orderList)-1){
					$str .= $orderList[$i]['BandWidthExt_ID'] . ",";
				}else{
					$str .= $orderList[$i]['BandWidthExt_ID'];
				}
			}
		}else{
			$str = "-1";
		}
		return $str;
	}
	//获取用户的IP的ID
	public function getIpID($id){
		$order_ip = M('Order_IP');
		$orderList = $order_ip->query("select * from `Order_IP` where Order_ID=".$id);		
		$str="";
		if(!empty($orderList)){
			for($i=0;$i<count($orderList);$i++){
				if($i!=count($orderList)-1){
					$str .= $orderList[$i]['IP_ID'] . ",";
				}else{
					$str .= $orderList[$i]['IP_ID'];
				}
			}
		}else{
			$str = "-1";
		}
		return $str;
	}
	//获取用户的机柜的ID
	public function getCabinetID($id){
		$order_cabinet = M('Order_Cabinet');
		$orderList = $order_cabinet->query("select * from `Order_Cabinet` where Order_ID=".$id);
		$str="";
		if(!empty($orderList)){
			for($i=0;$i<count($orderList);$i++){
				if($i!=count($orderList)-1){
					$str .= $orderList[$i]['Cabinet_ID'] . ",";
				}else{
					$str .= $orderList[$i]['Cabinet_ID'];
				}
			}
		}else{
			$str = "-1";
		}
		return $str;
	}
	//用户基本资料
	public function userInfo(){
		if(isset($_SESSION['user'])){
			$Login = M()->table('Login');
			$ret = $Login->where("Name='".$_SESSION['user']."'")->find();			
			$this->assign('login', $ret);
			if($_SESSION['type']==1){
				//个人			
				$Person = M()->table('Person');
				$userInfo = $Person->where('ID='.$_SESSION['id'])->find();
				$this->assign('userInfo', $userInfo);
			}else{
				//企业	
				$Company = M()->table('Company');
				$userInfo = $Company->where('ID='.$_SESSION['id'])->find();
				$this->assign('userInfo', $userInfo);
			}
			$this->display();	
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	//修改用户资料
	public function saveUserInfo(){
		if(isset($_SESSION['user'])){
			if($_SESSION['type']==1){
				
				$data['TrueName'] = $_POST['TrueName'];
				$data['IDNum'] = $_POST['IDNum'];
				$data['Address'] = $_POST['Address'];
				$data['Address_'] = $_POST['Address_'];
				$data['BeiAn'] = $_POST['BeiAn'];
				$data['Email'] = $_POST['Email'];
				$data['Phone'] = $_POST['Phone'];
				
				if(!empty($_POST['BillDay'])){
					$data['BillDay'] = $_POST['BillDay'];
				}
				
				$Person = M()->table('Person');
				$is_ok = $Person->where('id='.$_POST['ID'])->save($data);
				if($is_ok===false){
					$this->ajaxReturn('用户资料修改失败，请联系管理员','error',0);
				}else{
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
				$data['LinkmanEmail'] = $_POST['LinkmanEmail'];
				$data['LinkmanIdNum'] = $_POST['LinkmanIdNum'];
				$data['LinkmanAdd'] = $_POST['LinkmanAdd'];
				
				if(!empty($_POST['BillDay'])){
					$data['BillDay'] = $_POST['BillDay'];
				}
				
				$Company = M()->table('Company');
				$is_ok = $Company->where('id='.$_POST['ID'])->save($data);
				if($is_ok===false){
					$this->ajaxReturn('用户资料修改失败，请联系管理员','error',0);
				}else{
					$this->ajaxReturn('用户资料修改成功','success',1);
				}
			}
		}else{
			header("Location: ".__APP__."/Index/login");
		}
	}
	
	
}
