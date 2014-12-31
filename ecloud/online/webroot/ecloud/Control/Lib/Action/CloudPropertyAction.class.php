<?php
// 本类由系统自动生成，仅供测试用途
class CloudPropertyAction extends BaseAction {
	
    public function index(){
		$this->display();
    }
	
	public function vps(){
		$login = M();
		$ret = $login->table('Ad_Login')->where("Name='".$_SESSION['user']."'")->find();
		$order = M();
		//$ordlist = $order->query('select * from `Ad_Order` o left join `Ad_OrderNeed` ov on o.ID=ov.Order_ID where o.Login_ID='.$ret['ID'].' order by o.ID desc');
		//控制台
		//$rslt = $order->table('Ad_Order')->where('Login_ID='.$ret['ID'])->select();
//			$cou = 0;
		
		$orderID = $order->table('Ad_Order')->where('Login_ID='.$ret['ID'])->getField('ID',true);
		$ids = "";
		foreach($orderID as $val){
			$ids .= $val . ',';
		}
		$ids = substr($ids,0,-1);
		$page = 1;
		$where = "";
		if(!empty($_GET['p'])){
			$page = $_GET['p'];
		}
		if(!empty($_GET['code'])){
			$where .= " and v.PropertyCode like '%".$_GET['code']."%' ";
			$this->assign('code',$_GET['code']);
		}
		$Ad_OrderVPSBuy = M();
		$vpsList = $Ad_OrderVPSBuy->query("select * from Ad_OrderVPSBuy ov left join Ad_VPSBuy v on v.ID=ov.VPSBuy_ID where ov.Order_ID in (".$ids.") ".$where." order by v.ID desc limit ". ($page-1) * 10 .",10 ");

		$count = $Ad_OrderVPSBuy->query("select count(*) as count from Ad_OrderVPSBuy ov left join Ad_VPSBuy v on v.ID=ov.VPSBuy_ID where ov.Order_ID in (".$ids.") ".$where." order by v.ID desc ");
		if($count[0]['count']<=10){
			$pageCount = 1;
		}else{
			if($count[0]['count'] % 10 == 0){
				$pageCount = $count[0]['count'] / 10;
			}else{
				$pageCount = ($count[0]['count'] - $count[0]['count'] % 10) / 10 + 1;
			}
		}
		foreach($vpsList as $key => $value){
			if(strtotime(date('Y-m-d H:i:s'))>strtotime($value['PayEnd'])){
				$vpsList[$key]['is_expire'] = "yes";								
			}else{
				$vpsList[$key]['is_expire'] = "no";
			}
			$url[$value['PropertyCode']] = C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$value['PropertyCode']."&Opt=Get";	
			$url2[$value['PropertyCode']] = C('INTERFACE_URL')."/ecloud_admin/GetVMVNCUrl.php?VMCode=".$value['PropertyCode'];	
			$vpsList[$key]['Code'] = $value['PropertyCode'];
		}
		//获取状态
		foreach($url as $k => $v){
			$rs[$k] = popen('curl -s -m 5 "'.$v.'"','r');
		}
		foreach($rs as $k => $v){
			foreach($vpsList as $kk => $value){
				if($k == $value['PropertyCode']){
					$data2 = fread($v,2096);
					$tem = json_decode($data2,true);
					$tem2 = json_decode($tem['result'],true);
					$vpsList[$kk]['vmStatus'] = $tem2['PowerState'];
				}
			}
		}
		//获取IP地址和端口
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
		
		$this->assign('vpsList',$vpsList);
		//根据状态查询
		$cou = 0;
		if(!empty($_GET['status'])){
			foreach($vpsList as $kk => $value){
				if($value['vmStatus'] == $_GET['status']){
					$vpsLists[$cou] = $value;
					$cou ++;
				}				
			}			
			$this->assign('vpsList',$vpsLists);			
			$this->assign('status',$_GET['status']);
		}
		$this->assign('page',$page);
		$this->assign('pageCount',$pageCount);
		$this->assign('sum',$count[0]['count']);	
		$this->assign('count',count($vpsList));	
		$this->assign('login',$ret);
		$this->assign('today',date("Y-m-d"));
		$this->display();
    }
	public function vps_info(){
		if($_GET['id']){		
		
			$Ad_OrderVPSBuy = M();			
			$vps = $Ad_OrderVPSBuy->query("select * from Ad_OrderVPSBuy ov left join Ad_VPSBuy v on v.ID=ov.VPSBuy_ID where v.ID=%d",$_GET['id']);
			$vps[0]['PayStart'] = $vps[0]['PayStart'];
			$vps[0]['PayEnd'] = $vps[0]['PayEnd'];
						
			$Ad_OrderNeed = M();
			$order = $Ad_OrderNeed->query('select * from Ad_Order a left join Ad_OrderNeed b on a.ID=b.Order_ID where a.ID='.$vps[0]['Order_ID']);
			$vps[0]['CPU'] = $order[0]['CPU'];
			$vps[0]['RAM'] = $order[0]['RAM'];
			$vps[0]['DISK'] = $order[0]['DISK'];
			$vps[0]['OS'] = $order[0]['OS'];
			$vps[0]['Time'] = $order[0]['Time'];
			$vps[0]['HouseName'] = $order[0]['HouseName'];
			
			//获取状态
			$data = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$vps[0]['PropertyCode']."&Opt=Get");
			$ret = json_decode($data,true);
			$tem = json_decode($ret['result'],true);
			$vps[0]['Status'] = $tem['PowerState'];
			
			$this->assign('vps',$vps[0]);
			$this->display();	
		}
	}
	//批量操作虚拟机
	public function batchOperationVM(){
		if(!empty($_POST['code']) && !empty($_POST['opt'])){
			$arr = explode(',',$_POST['code']);
			
			foreach($arr as $val){
				$data = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$val."&Opt=Set&PowerState=".$_POST['opt']);
				$ret = json_decode($data,true);
				if($ret['ret']!=0){
					$this->ajaxReturn('云主机操作失败，请联系管理员。','error',0);
				}
			}
			$this->ajaxReturn(1,'success',1);
		}
	}
	//操作虚拟机
	public function operationVM(){
		if(!empty($_POST['code']) && !empty($_POST['opt'])){
			$data = file_get_contents(C('INTERFACE_URL')."/ecloud_admin/VMPowerState.php?VMCode=".$_POST['code']."&Opt=Set&PowerState=".$_POST['opt']);
			$ret = json_decode($data,true);
			if($ret['ret']!=0){
				$this->ajaxReturn('云主机操作失败，请联系管理员。','error',0);
			}
			$this->ajaxReturn($ret['ret'],'success',1);
		}
	}	
	
}