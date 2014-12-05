<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
		$this->display();
    }
	public function verify(){
		//导入Image类库
		import("ORG.Util.Image");
		Image::buildImageVerify(4,1,"png",60,30,"myverify"); //(length,mode,type,width,height,verifyName)
	}
	//举报
	public function report(){
		$this->display();	
	}
	//常见问题
	public function question(){
		if(!empty($_GET['q'])){
			$this->assign('q',$_GET['q']);
			$this->display();		
		}
	}
	//公司用户信息写入
	public function company(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			$this->assign('id',$_GET['id']);
			$this->display();
		}else{
			/*var address = $("#address").val();
			var farenname  = $("#farenname").val();
			var farenphone  = $("#farenphone").val();
			var beian  = $("#beian").val();
			var linkmanname  = $("#linkmanname").val();
			var linkmanphone  = $("#linkmanphone").val();
			var linkmanmail  = $("#linkmanmail").val();
			var linkmanidnum  = $("#linkmanidnum").val();
			var linkmanadd  = $("#linkmanadd").val();*/
			
			if(isset($_POST['address']) && isset($_POST['farenname']) && isset($_POST['farenphone']) && isset($_POST['beian']) && isset($_POST['linkmanname']) && isset($_POST['linkmanphone']) && isset($_POST['linkmanidnum']) && isset($_POST['linkmanadd'])){
				$Company = M()->table("Company");
				
				$data['Address'] = $_POST['address'];
				$data['LegalPersonName'] = $_POST['farenname'];
				$data['LegalPersonPhone'] = $_POST['farenphone'];
				$data['BeiAn'] = $_POST['beian'];
				$data['LinkmanName'] = $_POST['linkmanname'];
				$data['LinkmanPhone'] = $_POST['linkmanphone'];
				$data['LinkmanIdNum'] = $_POST['linkmanidnum'];
				$data['LinkmanAdd'] = $_POST['linkmanadd'];
				
				$isok = $Company->where('id='.$_POST['id'])->save($data);
				
				if($isok){
					
					$this->ajaxReturn("注册用户成功，3秒跳转用户中心页面。",'success',1);
				}else{
					$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);		
				}
			} else {
				$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
			}
		}
	}
	//个人用户信息写入
	public function person(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			$this->assign('id',$_GET['id']);
			$this->display();
		}else{
			if(isset($_POST['truename']) && isset($_POST['address']) && isset($_POST['address_']) && isset($_POST['phone']) && isset($_POST['idnum']) && isset($_POST['beian'])){
				$Person = M()->table("Person");
				$data['TrueName'] = $_POST['truename'];
				$data['IDNum'] = $_POST['idnum'];
				$data['Address'] = $_POST['address'];
				$data['Address_'] = $_POST['address_'];
				$data['Phone'] = $_POST['phone'];
				$data['BeiAn'] = $_POST['beian'];
				
				$isok = $Person->where('id='.$_POST['id'])->save($data);
				if($isok){
					$this->ajaxReturn("注册用户成功，3秒跳转用户中心页面。",'success',1);
				}else{
					$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);		
				}
			} else {
				$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
			}
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
					$Login = M()->table("Login");
					$is_exist = $Login->where("Name='".$_POST['username']."'")->find();
					if(!empty($is_exist)){
						$this->ajaxReturn("您输入的电子邮箱已注册，请重新输入！",'error',0);
					}
					
					$person = M()->table("Person");
					
					$obj["Email"] = trim($_POST['username']);
					$ret = $person->data($obj)->add();
					if($ret){
						$data["ObjType"] = 1;
						$data["ObjID"] = $ret;
						$data["GlobalCustom_ID"] = ESS.sprintf('%011d', $ret);
						$data["Name"] = trim($_POST['username']);
						$data["Passwd"] = trim($_POST['password']); //md5()
						$data["RegTime"] = date('Y-m-d H:i:s',time());
						$list = M()->table("Login");
						$rslt = $list->data($data)->add();
						
						//print_r($list->getLastSQL());exit;
						if($rslt){
							
							$_SESSION['user'] = $data["Name"];
							$_SESSION['customID'] = $data["GlobalCustom_ID"];
							$_SESSION['id'] = $data["ObjID"];
							$_SESSION['type'] = $data["ObjType"];
							$_SESSION['audit'] = $data["Audit"];
							
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
					$Login = M()->table("Login");
					$is_exist = $Login->where("Name='".$_POST['useremail']."'")->find();
					if(!empty($is_exist)){
						$this->ajaxReturn("您输入的电子邮箱已注册，请重新输入！",'error', 0);
					}
					
					$company = M()->table("Company");
					
					$obj["CompanyName"] = trim($_POST['companyname']);
					$obj["CompanyCode"] = trim($_POST['companycode']);
					$obj["LinkmanEmail"] = trim($_POST['useremail']);
					$ret = $company->data($obj)->add();
					if($ret){
						$data["ObjType"] = 2;
						$data["ObjID"] = $ret;
						$data["GlobalCustom_ID"] = ESS.sprintf('%011d', $ret);
						$data["Name"] = trim($_POST['useremail']);
						$data["Passwd"] = trim($_POST['password']); //md5()
						$data["RegTime"] = date('Y-m-d H:i:s',time());
						$list = M()->table("Login");
						$rslt = $list->data($data)->add();
						//print_r($list->getLastSQL());exit;
						if($rslt){
							$_SESSION['user'] = $data["Name"];
							$_SESSION['customID'] = $data["GlobalCustom_ID"];
							$_SESSION['id'] = $data["ObjID"];
							$_SESSION['type'] = $data["ObjType"];
							$_SESSION['audit'] = $data["Audit"];
							
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
	//登陆
	public function login(){
		if($_SERVER['REQUEST_METHOD' ] === 'GET'){
			unset($_SESSION['user']);
			unset($_SESSION['id']);
			unset($_SESSION['customID']);
			unset($_SESSION['type']);
			unset($_SESSION['audit']);
			$this->display();
		}
		else{
			//var_dump($_POST["checkcode"]);var_dump($_SESSION["myverify"]);die;
			if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['checkcode'])){
				if(md5(trim($_POST['checkcode'])) != $_SESSION['myverify']){
					$this->assign('error', "验证码错误，请输入正确的验证码！");
					$this->assign('mail', $_POST['username']);
					$this->display();
				}else{
					$cnd["Name"] = trim($_POST['username']);
					$cnd["Passwd"] = trim($_POST['password']); //md5()
					//print_r($cnd["mail"]);
					//print_r($cnd["pwd"]);
					//exit;
					$list = M()->table("Login");
					$rslt = $list->where($cnd)->find();
					//print_r($list->getLastSQL());exit;
					if(!empty($rslt)){
						$_SESSION['user'] = $rslt["Name"];
						$_SESSION['customID'] = $rslt["GlobalCustom_ID"];
						$_SESSION['id'] = $rslt["ObjID"];
						$_SESSION['type'] = $rslt["ObjType"];
						$_SESSION['audit'] = $rslt["Audit"];
						header("Location: ".__APP__."/Services/order/");
					} else {
						//$this->success("用户名或密码输入错误，请核对后再试！",U("/Index/login"));
						$this->assign('error', "用户名或密码错误，请核对后再试！");
						$this->assign('mail', $_POST['username']);
						$this->display();
					}
				}
			}
		}
    }
	//备案介绍
	public function beian(){
		if(!empty($_GET['b'])){
			$TubeBureau = M()->table('Tube_Bureau');
			$list = $TubeBureau->select();
			$this->assign('list',$list);
			$this->assign('beian_id', $_GET['b']);
		}
		$this->display();
	}
	//备案介绍
	public function tubeBureau(){
		if(!empty($_GET['t'])){
			$TubeBureau = M()->table('Tube_Bureau');
			$tubeBureau = $TubeBureau->where('ID='.$_GET['t'])->find();
			$this->assign('tubeBureau',$tubeBureau);
		}
		$this->display();
	}
    //产品页面
	public function product(){
		if(!empty($_GET['p'])){
			$product = M()->table('Product');
			$p = $product->where('id='.$_GET['p'])->find();
			$this->assign('product', $p);
			
			if($_GET['p'] == 6){ //ip资源
				$sip = M()->table('Source_IP');
				$list_ip = $sip->select();
				$type = $sip->query('select distinct(`Type`) from `Source_IP` ');
				
				$this->assign('netList',$type);
				$this->assign('List',$list_ip);
				$this->assign('type',"ip");
				//查询购物车是否有未付款的订单
				$orderList = $this->getCartList();
				$this->assign('ord_devList', $orderList['dev']);
				$this->assign('ord_vpsList', $orderList['vps']);
				$this->assign('ord_ipList', $orderList['ip']);
				$this->assign('ord_bandwidthList', $orderList['bandwidth']);
				$this->assign('ord_bandwidthExtList', $orderList['bandwidthExt']);
				$this->assign('ord_cabinetList', $orderList['cabinet']);
				$this->assign('orderCount', count($orderList['dev'])+count($orderList['vps'])+count($orderList['ip'])+count($orderList['bandwidth'])+count($orderList['bandwidthExt'])+count($orderList['cabinet']));
				
			}
			if($_GET['p'] == 3){ //网络带宽
				$sbandwidth = M()->table('Source_BandWidth');
				$list_band = $sbandwidth->select();
				$this->assign('List',$list_band);
				
				$listext = M()->table('Source_BandWidthExt');
				$list_band_ext = $listext->select();
				$type = $sbandwidth->query('select distinct(`Type`) from `Source_BandWidth` ');
				
				$this->assign('netList',$type);
				$this->assign('List_ext',$list_band_ext);				
				$this->assign('type',"bandwidth");
				//查询购物车是否有未付款的订单
				$orderList = $this->getCartList();		
				$this->assign('ord_devList', $orderList['dev']);
				$this->assign('ord_vpsList', $orderList['vps']);
				$this->assign('ord_ipList', $orderList['ip']);
				$this->assign('ord_bandwidthList', $orderList['bandwidth']);
				$this->assign('ord_bandwidthExtList', $orderList['bandwidthExt']);
				$this->assign('ord_cabinetList', $orderList['cabinet']);
				$this->assign('orderCount', count($orderList['dev'])+count($orderList['vps'])+count($orderList['ip'])+count($orderList['bandwidth'])+count($orderList['bandwidthExt'])+count($orderList['cabinet']));
			}
			if($_GET['p'] == 8){ //服务器托管
				$scabinet = M()->table('Source_Cabinet');
				$list_ip = $scabinet->select();
				$type = $scabinet->query('select distinct(`Location`) from `Source_Cabinet` ');
				$this->assign('houstList',$type);
				$this->assign('List',$list_ip);
				$this->assign('type',"dev_hold");
				//查询购物车是否有未付款的订单
				$orderList = $this->getCartList();		
				$this->assign('ord_devList', $orderList['dev']);
				$this->assign('ord_vpsList', $orderList['vps']);
				$this->assign('ord_ipList', $orderList['ip']);
				$this->assign('ord_bandwidthList', $orderList['bandwidth']);
				$this->assign('ord_bandwidthExtList', $orderList['bandwidthExt']);
				$this->assign('ord_cabinetList', $orderList['cabinet']);
				$this->assign('orderCount', count($orderList['dev'])+count($orderList['vps'])+count($orderList['ip'])+count($orderList['bandwidth'])+count($orderList['bandwidthExt'])+count($orderList['cabinet']));
			}
			if($_GET['p'] == 9){ //服务器租赁
				//空闲设备
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free");
				$obj = json_decode($data,true);				
				//机房
				$data1 = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_house");
				$houseList = json_decode($data1,true);
				//品牌
				$data2 = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_brand");
				$brandList = json_decode($data2,true);
				
				$this->assign('brandList',$brandList);
				$this->assign('houseList',$houseList);
				$this->assign('type',"device");
				$this->assign('List',$obj);
				//查询购物车是否有未付款的订单
				$orderList = $this->getCartList();		
				$this->assign('ord_devList', $orderList['dev']);
				$this->assign('ord_vpsList', $orderList['vps']);
				$this->assign('ord_ipList', $orderList['ip']);
				$this->assign('ord_bandwidthList', $orderList['bandwidth']);
				$this->assign('ord_bandwidthExtList', $orderList['bandwidthExt']);
				$this->assign('ord_cabinetList', $orderList['cabinet']);
				$this->assign('orderCount', count($orderList['dev'])+count($orderList['vps'])+count($orderList['ip'])+count($orderList['bandwidth'])+count($orderList['bandwidthExt'])+count($orderList['cabinet']));
			}
			if($_GET['p'] == 7){ //云服务器
				//空闲设备
				$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_vps");
				$obj = json_decode($data,true);				
				//机房
				$data1 = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_house");
				$houseList = json_decode($data1,true);
				//品牌
				$data2 = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_brand");
				$brandList = json_decode($data2,true);
				
				$this->assign('brandList',$brandList);
				$this->assign('houseList',$houseList);
				$this->assign('type',"vps");
				$this->assign('List',$obj);
				//查询购物车是否有未付款的订单
				$orderList = $this->getCartList();		
				$this->assign('ord_devList', $orderList['dev']);
				$this->assign('ord_vpsList', $orderList['vps']);
				$this->assign('ord_ipList', $orderList['ip']);
				$this->assign('ord_bandwidthList', $orderList['bandwidth']);
				$this->assign('ord_bandwidthExtList', $orderList['bandwidthExt']);
				$this->assign('ord_cabinetList', $orderList['cabinet']);
				$this->assign('orderCount', count($orderList['dev'])+count($orderList['vps'])+count($orderList['ip'])+count($orderList['bandwidth'])+count($orderList['bandwidthExt'])+count($orderList['cabinet']));
			}
			
			
		}
		$this->display();
	}
	//根据条件排序查询IP
	public function findIp(){
		$Source_IP = M('Source_IP');
		$sql = "select si.*,p.Name as Pro_Name  from `Source_IP` si left join `Product` p on p.ID=si.Pro_ID where 1=1 ";
		if(!empty($_POST['network'])){
			$sql .= " and Type='".$_POST['network']."' ";
		}
		if(!empty($_POST['price'])){
			if(strpos($_POST['price'],"-")!=-1){
				$arr = explode("-",$_POST['price']);
				$sql .= " and Price BETWEEN ".$arr[0]." AND ".$arr[1]." ";
			}else{
				$sql .= " and Price>=".$_POST['price']." ";
			}
		}
		if(!empty($_POST['order'])){
			$sql .= $_POST['order'];
		}
		$ipList = $Source_IP->query($sql);
		$this->ajaxReturn($ipList,'success',1);
	}
	//按条件和排序查找空闲设备
	public function findServices(){
		$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free&Brand=".$_POST['brand']."&Price=".$_POST['price']."&House=".$_POST['house']."&Order=".$_POST['order']."&Type=".$_POST['type']);
		$devList = json_decode($data,true);		
		$this->ajaxReturn($devList,'success',1);
	}
	//按条件和排序查找空闲设备
	public function findVps(){
		$data = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_vps&Brand=".$_POST['brand']."&Price=".$_POST['price']."&House=".$_POST['house']."&Order=".$_POST['order']."&Type=".$_POST['type']);
		$vpsList = json_decode($data,true);		
		$this->ajaxReturn($vpsList,'success',1);
	}
	//根据条件排序查询带宽
	public function findBandWidth(){
		$Source_BandWidth = M('Source_BandWidth');
		$Source_BandwidthExt = M('Source_BandwidthExt');
		$sql = "select sb.*,p.Name as Pro_Name  from `Source_BandWidth` sb left join `Product` p on p.ID=sb.Pro_ID where 1=1 ";
		$sql2 = "select sb.*,p.Name as Pro_Name  from `Source_BandwidthExt` sb left join `Product` p on p.ID=sb.Pro_ID where 1=1 ";
		
		if(!empty($_POST['network'])){
			$sql .= " and Type='".$_POST['network']."' ";
			$sql2 .= " and Type='".$_POST['network']."' ";
		}
		if(!empty($_POST['price'])){
			if(strpos($_POST['price'],"-")!=-1){
				$arr = explode("-",$_POST['price']);
				$sql .= " and Price BETWEEN ".$arr[0]." AND ".$arr[1]." ";
				$sq2 .= " and Price BETWEEN ".$arr[0]." AND ".$arr[1]." ";
			}else{
				$sql .= " and Price>=".$_POST['price']." ";
				$sql2 .= " and Price>=".$_POST['price']." ";
			}
		}
		if(!empty($_POST['order'])){
			$sql .= $_POST['order'];
			$sql2 .= $_POST['order'];
		}
		$arr;
		$bandWidthList = $Source_BandWidth->query($sql);
		$bandWidthExtList = $Source_BandwidthExt->query($sql2);
		
		foreach($bandWidthList as $key=>$value){
			$arr['bandWidth'][$key] = $value;
		}
		foreach($bandWidthExtList as $key=>$value){
			$arr['bandWidthExt'][$key] = $value;
		}
		$this->ajaxReturn($arr,'success',1);
	}
	//根据条件排序查询机柜
	public function findCabinet(){
		$Source_Cabinet = M('Source_Cabinet');
		$sql = "select sc.*,p.Name as Pro_Name  from `Source_Cabinet` sc left join `Product` p on p.ID=sc.Pro_ID where 1=1 ";
		if(!empty($_POST['house'])){
			$sql .= " and Location='".$_POST['house']."' ";
		}
		if(!empty($_POST['price'])){
			if(strpos($_POST['price'],"-")!=-1){
				$arr = explode("-",$_POST['price']);
				$sql .= " and Price BETWEEN ".$arr[0]." AND ".$arr[1]." ";
			}else{
				$sql .= " and Price>=".$_POST['price'] ." ";
			}
		}
		if(!empty($_POST['order'])){
			$sql .= $_POST['order'];
		}
		$cabinetList = $Source_Cabinet->query($sql);
		$this->ajaxReturn($cabinetList,'success',1);
	}
	//云服务器
	public function vps(){
		$this->display();
	}
	//设备
	public function device(){
		$this->display();
	}
	//ip内容页
	public function ip(){
		$this->display();
	}
	//机柜内容页
	public function cabinet(){
		$this->display();
	}
	//带宽
	public function bandwidth(){
		$this->display();
	}
	//登陆div
	public function login_div(){
		if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['checkcode'])){
			if(md5(trim($_POST['checkcode'])) != $_SESSION['myverify']){
					$this->ajaxReturn('验证码错误，请输入正确的验证码！','error',0);
				}else{
					$cnd["Name"] = trim($_POST['username']);
					$cnd["Passwd"] = trim($_POST['password']); 
					$list = M()->table("Login");
					$rslt = $list->where($cnd)->find();
					if(!empty($rslt)){
						$_SESSION['user'] = $rslt["Name"];
						$_SESSION['customID'] = $rslt["GlobalCustom_ID"];
						$_SESSION['id'] = $rslt["ObjID"];
						$_SESSION['type'] = $rslt["ObjType"];	
						$_SESSION['audit'] = $rslt["Audit"];					
						$this->ajaxReturn(1,'success',1);
					} else {
						$this->ajaxReturn("用户名或密码错误，请核对后再试！",'error',0);
					}
				}
		}else{
			$this->display();
		}
	}
	//注册div
	public function regist_div(){
		if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['checkcode'])){
			if(md5(trim($_POST['checkcode'])) != $_SESSION['myverify']){
					$this->ajaxReturn("验证码错误，请输入正确的验证码！",'error',0);
				}else{
					$is_exist = M()->table("Login")->where("Name='".$_POST['username']."'")->find();
					if(!empty($is_exist)){
						$this->ajaxReturn("您输入的电子邮箱已注册，请重新输入！",'error',0);
					}
					
					$person = M()->table("Person");
					
					$obj["Email"] = trim($_POST['username']);
					$ret = $person->data($obj)->add();
					if($ret){
						$data["ObjType"] = 1;
						$data["ObjID"] = $ret;
						$data["GlobalCustom_ID"] = ESS.sprintf('%011d', $ret);
						$data["Name"] = trim($_POST['username']);
						$data["Passwd"] = trim($_POST['password']); //md5()
						$data["RegTime"] = date('Y-m-d H:i:s',time());
						$list = M()->table("Login");
						$rslt = $list->data($data)->add();
						if($rslt){
							
							$_SESSION['user'] = $data["Name"];
							$_SESSION['customID'] = $data["GlobalCustom_ID"];
							$_SESSION['id'] = $data["ObjID"];
							$_SESSION['type'] = $data["ObjType"];
							
							$this->ajaxReturn(1,'success',1);
						} else {
							$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
						}
					} else {
						$this->ajaxReturn("注册用户失败，请稍后再试！",'error',0);
					}
				}
		}else{
			$this->display();
		}
	}
	//购物车的商品
	public function getCartList(){
		//设备
		$devData = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free&ID=".$this->getDevID());
		$devList = json_decode($devData,true);
		//云服务器
		$vpsData = file_get_contents(C('INTERFACE_URL')."/ibss/dev_query.php?opt=get_free_vps&ID=".$this->getVpsID());
		$vpsList = json_decode($vpsData,true);
		
		$order = M('Order');
		$tem =  $order->query("select * from `Order` where IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'");
		//IPid
		$ip = M('Source_IP');
		$ipList = $ip->query('select si.*,p.Name as ProName,o.Count as Count from Source_IP si left join `Product` p on p.ID=si.Pro_ID  left join `Order_IP` o on o.IP_ID=si.ID where o.Order_ID='.$tem[0]['ID'].' and si.ID in ('.$this->getIpID().')');
		//带宽id
		$bandWidth = M('Source_BandWidth');
		$bandWidthList = $bandWidth->query('select sb.*,p.Name as ProName,o.Count as Count from Source_BandWidth sb left join `Product` p on p.ID=sb.Pro_ID left join `Order_BandWidth` o on o.BandWidth_ID=sb.ID where  o.Order_ID='.$tem[0]['ID'].' and sb.ID in ('.$this->getBandwidthID().')');
		//带宽Extid 
		$bandWidthExt = M('Source_BandWidthExt');
		$bandWidthExtList = $bandWidthExt->query('select sb.*,p.Name as ProName,o.Count as Count from Source_BandWidthExt sb left join `Product` p on p.ID=sb.Pro_ID left join `Order_BandWidthExt` o on o.BandWidthExt_ID=sb.ID where o.Order_ID='.$tem[0]['ID'].' and sb.ID in ('.$this->getBandwidthExtID().')');	
		//机柜id
		$cabinet = M('Source_Cabinet');
		$cabinetList = $cabinet->query('select sc.*,p.Name as ProName,o.Count as Count from Source_Cabinet sc left join `Product` p on p.ID=sc.Pro_ID left join `Order_Cabinet` o on o.Cabinet_ID=sc.ID where o.Order_ID='.$tem[0]['ID'].' and sc.ID in ('.$this->getCabinetID().')');
		
		$orderList;
		//修改价格
		$data = file_get_contents(C('INTERFACE_URL')."/ess/ess_bill.php?Order_ID=".$tem[0]['ID']); 
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
		$orderList['Ord_ID'] = $tem[0]['ID'];
		$orderList['IS_Pay'] = $tem[0]['IS_Pay'];
		$orderList['Status'] = $tem[0]['Status'];
		$orderList['DoReq'] = $tem[0]['DoReq'];
		$orderList['Code'] = $tem[0]['Code'];
		return $orderList;
	}
	//获取未付款的设备的ID
	public function getDevID(){
		$order = M('Order');
		$order_dev = M('Order_Dev');
		$tem = $order->query("select * from `Order` where IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'");
		$orderList = $order_dev->query("select * from `Order_Dev` where Order_ID=".$tem[0]['ID']);
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
	//获取未付款的云服务器的ID
	public function getVpsID(){
		$order = M('Order');
		$order_dev = M('Order_Dev');
		$tem = $order->query("select * from `Order` where IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'");
		$orderList = $order_dev->query("select * from `Order_Dev` where Order_ID=".$tem[0]['ID']);
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
	//获取未付款的带宽的ID
	public function getBandwidthID(){
		$order = M('Order');
		$order_bandwidth = M('Order_BandWidth');
		$tem = $order->query("select * from `Order` where IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'");
		$orderList = $order_bandwidth->query("select * from `Order_BandWidth` where Order_ID=".$tem[0]['ID']);
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
	//获取未付款的BandwidthExt的ID
	public function getBandwidthExtID(){
		$order = M('Order');
		$order_bandwidthExt = M('Order_BandWidthExt');
		$tem = $order->query("select * from `Order` where IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'");
		$orderList = $order_bandwidthExt->query("select * from `Order_BandWidthExt` where Order_ID=".$tem[0]['ID']);
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
	//获取未付款的IP的ID
	public function getIpID(){
		$order = M('Order');
		$order_ip = M('Order_IP');
		$tem = $order->query("select * from `Order` where IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'");
		$orderList = $order_ip->query("select * from `Order_IP` where Order_ID=".$tem[0]['ID']);
		
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
	//获取未付款的机柜的ID
	public function getCabinetID(){
		$order = M('Order');
		$order_cabinet = M('Order_Cabinet');
		$tem = $order->query("select * from `Order` where IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'");
		$orderList = $order_cabinet->query("select * from `Order_Cabinet` where Order_ID=".$tem[0]['ID']);
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
	//总价
	public function getTotalPrice(){
		$sum = 0.00;
		$orderList = $this->getCartList();
		foreach($orderList as $key=>$value){
			/*if($key=='ip'){
				foreach($value as $key=>$val){
					$sum += $val['Price'] * $val['Count'];
				}		
			}else if($key=='cabinet'){
				foreach($value as $key=>$val){
					$sum += $val['Price'] * $val['Count'];
				}	
			}else{*/
				foreach($value as $key=>$val){
					$sum += $val['Price'];
				}	
			//}
		}		
		$_SESSION['totalPrice'] = $sum;
		$price = number_format($_SESSION['totalPrice'], 2);
		$this->ajaxReturn($price,'success',1);
	}
	
	//获取账单日
	public function getBillDay(){
		$bill_day=0;
		//如果账单日为0就取系统当前时间
		if($_SESSION['type']==1){
			$Person = M();
			$p = $Person->table('Person')->where('ID='.$_SESSION['id'])->find();
			if($p['BillDay']==0){
				$bill_day = date('j',time());
			}else{
				$bill_day = $p['BillDay'];
			}
		}else{				
			$Company = M();
			$c = $Company->table('Company')->where('ID='.$_SESSION['id'])->find();
			if($c['BillDay']==0){
				$bill_day = date('j',time());
			}else{
				$bill_day = $p['BillDay'];
			}
		}	
		return $bill_day;
	}
	
	//添加商品到购物车
	public function addCart(){
		if(!empty($_SESSION['user'])){
			$order = M('Order')->table('Order');
			$orderList = $order->where("IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'")->find();
			$ord_count = count($order->query("select * from `Order` where TS between '".date('Y-m-d',time())." 00:00:00"."'  and '".date('Y-m-d',time())." 23:59:59"."' "));
			
			$bill_day = $this->getBillDay();
			$ord_code = "WEB".date('YmdHis',time()).str_pad($ord_count+1,5,'0',STR_PAD_LEFT);
			
			
			//订单添加设备、云服务器
			if($_POST['type']=='dev' || $_POST['type']=='vps'){			
				$order_Dev = M('Order_Dev');			
				$is_exist = $order_Dev->table('Order_Dev')->where('Dev_ID='.$_POST['id'].' and Order_ID='.$orderList['ID'])->find();
				
				if(!empty($orderList)){
					if(!empty($is_exist)){
						$this->ajaxReturn('该设备已经添加，无需重复添加。','error',0);	
					}else{
						$data['Dev_ID'] = $_POST['id'];					
						$data['Order_ID'] = $orderList['ID'];
						$data['AutoPay'] = 'NO';
						$data['Nice'] = 0;		
						$data['PayType'] = '';							
						$data['StartTime'] = date('Y-m-d H:i:s',time());
						$data['EndTime'] = '';							
						$data['BillDay'] = $bill_day;
						$order_Dev->table('Order_Dev')->add($data);
						
						$orderList = $this->getCartList();
						$this->ajaxReturn($orderList,'success',1);
					}
				}else{
					//"WEB".date('YmdHis',time()).rand(001,999)
					$data['Custom_ID'] = $_SESSION['customID'];			
					$data['Code'] = $ord_code;								
					$data['IS_Pay'] = 'NO';								
					$data['Status'] = '未支付';							
					$data['DoReq'] = '';							
					$data['DoRsp'] = '';							
					$data['ContractCode'] = '';						
					$is_ok = $order->table('Order')->add($data);
					if($is_ok){
						$data2['Dev_ID'] = $_POST['id'];				
						$data2['Order_ID'] = $is_ok;					
						$data2['AutoPay'] = 'NO';						
						$data2['Nice'] = 0;								
						$data2['PayType'] = '';							
						$data2['StartTime'] = date('Y-m-d H:i:s',time());					
						$data2['EndTime'] = '';							
						$data2['BillDay'] = $bill_day;							
						$order_Dev->table('Order_Dev')->add($data2);
								
						$orderList = $this->getCartList();	
						$this->ajaxReturn($orderList,'success',1);
					}else{
						$this->ajaxReturn('添加失败，联系管理员','error',0);
					}
				}
			}
			//订单添加IP
			if($_POST['type']=='ip'){			
				$order_IP = M('Order_IP');			
				$is_exist = $order_IP->table('Order_IP')->where('IP_ID='.$_POST['id'].' and Order_ID='.$orderList['ID'])->find();
				
				if(!empty($orderList)){
					if(!empty($is_exist)){
						//重复的
						$order_IP->table('Order_IP')->where('ID='.$is_exist['ID'])->setField('Count',$is_exist['Count']+1);
						$orderList = $this->getCartList();
						$this->ajaxReturn($orderList,'success',1);
					}else{
						$data['IP_ID'] = $_POST['id'];					
						$data['Order_ID'] = $orderList['ID'];			
						$data['Count'] = 1;								
						$data['AutoPay'] = 'NO';						
						$data['Nice'] = 0;								
						$data['PayType'] = '';							
						$data['StartTime'] = date('Y-m-d H:i:s',time());						
						$data['EndTime'] = '';							
						$data['BillDay'] = $bill_day;							
						$order_IP->table('Order_IP')->add($data);
												
						$orderList = $this->getCartList();
						$this->ajaxReturn($orderList,'success',1);
					}
				}else{
					$data['Custom_ID'] = $_SESSION['customID'];			
					$data['Code'] = $ord_code;
					$data['IS_Pay'] = 'NO';								
					$data['Status'] = '未支付';							
					$data['DoReq'] = '';							
					$data['DoRsp'] = '';							
					$data['ContractCode'] = '';						
					$is_ok = $order->table('Order')->add($data);
					if($is_ok){
						$data2['IP_ID'] = $_POST['id'];					
						$data2['Order_ID'] = $is_ok;					
						$data2['Count'] = 1;							
						$data2['AutoPay'] = 'NO';						
						$data2['Nice'] = 0;								
						$data2['PayType'] = '';							
						$data2['StartTime'] = date('Y-m-d H:i:s',time());			
						$data2['EndTime'] = '';							
						$data2['BillDay'] = $bill_day;							
						$order_IP->table('Order_IP')->add($data2);
									
						$orderList = $this->getCartList();	
						$this->ajaxReturn($orderList,'success',1);
					}else{
						$this->ajaxReturn('添加失败，联系管理员','error',0);
					}
				}
			}
			//订单添加带宽
			if($_POST['type']=='bandwidth'){			
				$order_BandWidth = M('Order_BandWidth');			
				$is_exist = $order_BandWidth->table('Order_BandWidth')->where('BandWidth_ID='.$_POST['id'].' and Order_ID='.$orderList['ID'])->find();
				
				if(!empty($orderList)){
					if(!empty($is_exist)){
						$this->ajaxReturn('该带宽已经添加，无需重复添加。','error',0);	
					}else{
						$data['BandWidth_ID'] = $_POST['id'];			
						$data['Order_ID'] = $orderList['ID'];			
						$data['Count'] = 1;								
						$data['AutoPay'] = 'NO';						
						$data['Nice'] = 0;								
						$data['PayType'] = '';							
						$data['StartTime'] = date('Y-m-d H:i:s',time());	
						$data['EndTime'] = '';							
						$data['BillDay'] = $bill_day;							
						$order_BandWidth->table('Order_BandWidth')->add($data);
						
						$orderList = $this->getCartList();
						$this->ajaxReturn($orderList,'success',1);
					}
				}else{
					$data['Custom_ID'] = $_SESSION['customID'];			
					$data['Code'] = $ord_code;								
					$data['IS_Pay'] = 'NO';							
					$data['Status'] = '未支付';							
					$data['DoReq'] = '';							
					$data['DoRsp'] = '';							
					$data['ContractCode'] = '';						
					$is_ok = $order->table('Order')->add($data);
					if($is_ok){
						$data2['BandWidth_ID'] = $_POST['id'];			
						$data2['Order_ID'] = $is_ok;					
						$data2['Count'] = 1;							
						$data2['AutoPay'] = 'NO';						
						$data2['Nice'] = 0;								
						$data2['PayType'] = '';							
						$data2['StartTime'] = date('Y-m-d H:i:s',time());						
						$data2['EndTime'] = '';							
						$data2['BillDay'] = $bill_day;							
						$order_BandWidth->table('Order_BandWidth')->add($data2);
									
						$orderList = $this->getCartList();	
						$this->ajaxReturn($orderList,'success',1);
					}else{
						$this->ajaxReturn('添加失败，联系管理员','error',0);
					}
				}
			}
			//订单添加带宽Ext
			if($_POST['type']=='bandwidthExt'){			
				$order_BandWidthExt = M('Order_BandWidthExt');			
				$is_exist = $order_BandWidthExt->table('Order_BandWidthExt')->where('BandWidthExt_ID='.$_POST['id'].' and Order_ID='.$orderList['ID'])->find();
				
				if(!empty($orderList)){
					if(!empty($is_exist)){
						$this->ajaxReturn('该带宽已经添加，无需重复添加。','error',0);
					}else{
						$data['BandWidthExt_ID'] = $_POST['id'];			
						$data['Order_ID'] = $orderList['ID'];			
						$data['Count'] = 1;								
						$data['AutoPay'] = 'NO';						
						$data['Nice'] = 0;								
						$data['PayType'] = '';							
						$data['StartTime'] = date('Y-m-d H:i:s',time());						
						$data['EndTime'] = '';							
						$data['BillDay'] = $bill_day;							
						$order_BandWidthExt->table('Order_BandWidthExt')->add($data);
						
						$orderList = $this->getCartList();
						$this->ajaxReturn($orderList,'success',1);
					}
				}else{
					$data['Custom_ID'] = $_SESSION['customID'];			
					$data['Code'] = $ord_code;								
					$data['IS_Pay'] = 'NO';								
					$data['Status'] = '未支付';							
					$data['DoReq'] = '';							
					$data['DoRsp'] = '';							
					$data['ContractCode'] = '';						
					$is_ok = $order->table('Order')->add($data);
					if($is_ok){
						$data2['BandWidthExt_ID'] = $_POST['id'];			
						$data2['Order_ID'] = $is_ok;					
						$data2['Count'] = 1;							
						$data2['AutoPay'] = 'NO';						
						$data2['Nice'] = 0;								
						$data2['PayType'] = '';							
						$data2['StartTime'] = date('Y-m-d H:i:s',time());						
						$data2['EndTime'] = '';							
						$data2['BillDay'] = $bill_day;							
						$order_BandWidthExts->table('Order_BandWidthExt')->add($data2);
									
						$orderList = $this->getCartList();	
						$this->ajaxReturn($orderList,'success',1);
					}else{
						$this->ajaxReturn('添加失败，联系管理员','error',0);
					}
				}
			}
			//订单添加机柜
			if($_POST['type']=='cabinet'){			
				$order_Cabinet = M('Order_Cabinet');			
				$is_exist = $order_Cabinet->table('Order_Cabinet')->where('Cabinet_ID='.$_POST['id'].' and Order_ID='.$orderList['ID'])->find();
				
				if(!empty($orderList)){
					if(!empty($is_exist)){
						//重复的
						$order_Cabinet->table('Order_Cabinet')->where('ID='.$is_exist['ID'])->setField('Count',$is_exist['Count']+1);
						$orderList = $this->getCartList();
						$this->ajaxReturn($orderList,'success',1);
					}else{
						$data['Cabinet_ID'] = $_POST['id'];				
						$data['Order_ID'] = $orderList['ID'];			
						$data['Count'] = 1;								
						$data['AutoPay'] = 'NO';						
						$data['Nice'] = 0;								
						$data['PayType'] = '';							
						$data['StartTime'] = date('Y-m-d H:i:s',time());						
						$data['EndTime'] = '';							
						$data['BillDay'] = $bill_day;							
						$order_Cabinet->table('Order_Cabinet')->add($data);
						
						$orderList = $this->getCartList();
						$this->ajaxReturn($orderList,'success',1);
					}
				}else{
					$data['Custom_ID'] = $_SESSION['customID'];			
					$data['Code'] = $ord_code;								
					$data['IS_Pay'] = 'NO';								
					$data['Status'] = '未支付';							
					$data['DoReq'] = '';							
					$data['DoRsp'] = '';							
					$data['ContractCode'] = '';						
					$is_ok = $order->table('Order')->add($data);
					if($is_ok){
						$data2['Cabinet_ID'] = $_POST['id'];			
						$data2['Order_ID'] = $is_ok;					
						$data2['Count'] = 1;							
						$data2['AutoPay'] = 'NO';						
						$data2['Nice'] = 0;								
						$data2['PayType'] = '';							
						$data2['StartTime'] = date('Y-m-d H:i:s',time());						
						$data2['EndTime'] = '';							
						$data2['BillDay'] = $bill_day;							
						$order_Cabinet->table('Order_Cabinet')->add($data2);
									
						$orderList = $this->getCartList();	
						$this->ajaxReturn($orderList,'success',1);
					}else{
						$this->ajaxReturn('添加失败，联系管理员','error',0);
					}
				}
			}
		}else{
			$this->redirect("/Index/login");
			//header("Location: ".__APP__."/Index/login");
		}
	}
	//从购物车删除
	public function deleteCart(){
		if(!empty($_POST['id']) && !empty($_POST['type'])){
			if($_POST['type']=='vps'){
				$Order_Dev = M('Order_Dev');
				$Order_Dev->table('Order_Dev')->where('Dev_ID='.$_POST['id'])->delete();
			}
			if($_POST['type']=='dev'){
				$Order_Dev = M('Order_Dev');
				$Order_Dev->table('Order_Dev')->where('Dev_ID='.$_POST['id'])->delete();
			}
			if($_POST['type']=='ip'){
				$Order_IP = M('Order_IP');
				$Order_IP->table('Order_IP')->where('IP_ID='.$_POST['id'])->delete();
			}
			if($_POST['type']=='bandwidth'){
				$Order_BandWidth = M('Order_BandWidth');
				$Order_BandWidth->table('Order_BandWidth')->where('BandWidth_ID='.$_POST['id'])->delete();
			}
			if($_POST['type']=='bandwidthExt'){
				$Order_BandWidthExt = M('Order_BandWidthExt');
				$Order_BandWidthExt->table('Order_BandWidthExt')->where('BandWidthExt_ID='.$_POST['id'])->delete();
			}
			if($_POST['type']=='cabinet'){
				$Order_Cabinet = M('Order_Cabinet');
				$Order_Cabinet->table('Order_Cabinet')->where('Cabinet_ID='.$_POST['id'])->delete();
			}
			$orderList = $this->getCartList();
			if(empty($orderList['dev']) && empty($orderList['vps']) && empty($orderList['ip']) && empty($orderList['bandwidth']) && empty($orderList['bandwidthExt']) && empty($orderList['cabinet'])){
				$Order = M('Order');
				$Order->table('Order')->where("Custom_ID='".$_SESSION['customID']."'")->delete();
			}
			$this->ajaxReturn($orderList,'success',1);
		}else{
			$this->ajaxReturn('删除商品失败，请联系管理员。','error',0);
		}
	}
	//购物车结算
	public function order(){
		if(!empty($_SESSION['user'])){
			//购物车的商品
			$orderList = $this->getCartList();
			$this->assign('ord_id', $orderList['Ord_ID']);
			$this->assign('ord_devList', $orderList['dev']);
			$this->assign('ord_vpsList', $orderList['vps']);
			$this->assign('ord_ipList', $orderList['ip']);
			$this->assign('ord_bandwidthList', $orderList['bandwidth']);
			$this->assign('ord_bandwidthExtList', $orderList['bandwidthExt']);
			$this->assign('ord_cabinetList', $orderList['cabinet']);
			$this->assign('orderCount', count($orderList['dev'])+count($orderList['vps'])+count($orderList['ip'])+count($orderList['bandwidth'])+count($orderList['bandwidthExt'])+count($orderList['cabinet']));
			
			$data = file_get_contents(C('INTERFACE_URL')."/ess/ess_bill.php?Order_ID=".$orderList['Ord_ID']);
			//$obj = json_decode($data,true);
			//时长
			$this->assign('time_obj', $data);
			$this->display();	
		}else{
			$this->redirect("/Index/login");
		}
	}
	//操作购物车加1 减1
	public function operationCart(){
		if(!empty($_POST['id']) && !empty($_POST['type']) && !empty($_POST['opt'])){
			if($_POST['type']=='ip'){
				$order_IP = M('Order_IP');				
				$is_exist = $order_IP->table('Order_IP')->where('IP_ID='.$_POST['id'])->find();
				if($_POST['opt']=='plus'){
					$is_ok = $order_IP->table('Order_IP')->where('IP_ID='.$_POST['id'])->setField('Count',$is_exist['Count']+1);
				}else{
					if($is_exist['Count']==1){
						$this->ajaxReturn('','error',0); //该商品的数量已经为1，不能继续删减。
					}else{
						$is_ok = $order_IP->table('Order_IP')->where('IP_ID='.$_POST['id'])->setField('Count',$is_exist['Count']-1);
					}
				}
				if($is_ok){
					$orderList = $this->getCartList();	
					$this->ajaxReturn($orderList,'success',1);
				}else{
					$this->ajaxReturn('修改商品失败，请联系管理员。','error',0);
				}
			}
			if($_POST['type']=='cabinet'){
				$order_Cabinet = M('Order_Cabinet');
				$is_exist = $order_Cabinet->table('Order_Cabinet')->where('Cabinet_ID='.$_POST['id'])->find();
				if($_POST['opt']=='plus'){
					$is_ok = $order_Cabinet->table('Order_Cabinet')->where('Cabinet_ID='.$_POST['id'])->setField('Count',$is_exist['Count']+1);
				}else{
					if($is_exist['Count']==1){
						$this->ajaxReturn('该商品的数量已经为1，不能继续删减。','error',0);
					}else{
						$is_ok = $order_Cabinet->table('Order_Cabinet')->where('Cabinet_ID='.$_POST['id'])->setField('Count',$is_exist['Count']-1);
					}
				}
				if($is_ok){				
					$orderList = $this->getCartList();	
					$this->ajaxReturn($orderList,'success',1);
				}else{
					$this->ajaxReturn('修改商品失败，请联系管理员。','error',0);
				}
			}
			
		}	
	}
	//修改数量
	public function updateCount(){
		if(!empty($_POST['id']) && !empty($_POST['type']) && !empty($_POST['val'])){
			if($_POST['type']=='ip'){
				$order_IP = M('Order_IP');	
				$is_ok = $order_IP->table('Order_IP')->where('IP_ID='.$_POST['id'])->setField('Count',$_POST['val']);
				if($is_ok){
					$orderList = $this->getCartList();	
					$this->ajaxReturn($orderList,'success',1);
				}else{
					$this->ajaxReturn('修改商品失败，请联系管理员。','error',0);
				}
			}
			if($_POST['type']=='cabinet'){
				$order_Cabinet = M('Order_Cabinet');				
				$is_ok = $order_Cabinet->table('Order_Cabinet')->where('Cabinet_ID='.$_POST['id'])->setField('Count',$_POST['val']);
				if($is_ok){
					$orderList = $this->getCartList();	
					$this->ajaxReturn($orderList,'success',1);
				}else{
					$this->ajaxReturn('修改商品失败，请联系管理员。','error',0);
				}
			}
		}
	}
	//去付款
	public function payment(){
		$Order = M();
		$tem = $Order->table('Order')->where("IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'")->find();
		
		$str = $this->getDesc();
		$this->assign('url', C('INTERFACE_URL')."/alipay/order_pay.php");
		$this->assign('DepartCode', "ESS");
		$this->assign('OrderCode', $tem['Code']);
				
		$name = urlencode("IDC产品和服务");
		$name = str_replace('%C2%80','',$name);
		$this->assign('OrderName', urldecode($name));
		
		$this->assign('OrderDesc', $str);
		$this->assign('OrderMoney', $_SESSION['totalPrice']);
		
		$this->display();
	}
	//获取订单各资源描述信息
	public function getDesc(){
		$str="";
		$orderList = $this->getCartList();
		if(!empty($orderList['dev'])){
			foreach($orderList['dev'] as $val){
				$str .= "品牌：".$val['Brand'] . ",CPU：".$val['CPU'].",内存：".$val['Ram']."硬盘：".$val['Disk'].",数量：1台; <br/>";
			}
		}
		if(!empty($orderList['vps'])){
			foreach($orderList['vps'] as $val){
				$str .= "品牌：".$val['Brand'] . ",CPU：".$val['CPU'].",内存：".$val['Ram']."硬盘：".$val['Disk'].",数量：1台; <br/>";
			}
		}
		if(!empty($orderList['ip'])){
			foreach($orderList['ip'] as $val){
				$str .= $val['Name'] . ",数量：".$val['Count']." 个; <br/>";
			}
		}
		if(!empty($orderList['bandwidth'])){
			foreach($orderList['bandwidth'] as $val){
				$str .= $val['Name'] . ";  <br/>";
			}
		}
		if(!empty($orderList['bandwidthExt'])){
			foreach($orderList['bandwidthExt'] as $val){
				$str .= $val['Name'] . ";  <br/>";
			}
		}
		if(!empty($orderList['cabinet'])){
			foreach($orderList['cabinet'] as $val){
				$str .= $val['Name'] . ",数量：".$val['Count']." 个;<br/> ";
			}
		}
		return $str;
	}
	//修改施工要求
	public function updateDoReq(){
		$Order = M();
		$ord = $Order->table('Order')->where("IS_Pay='NO' and Custom_ID='".$_SESSION['customID']."'")->find();
		$is_ok = $Order->table('Order')->where('ID='.$_POST['id'])->setField('DoReq',$_POST['doReq']);
		
		//修改endtime
		if(!empty($_POST['slide_str'])){
			$data = explode(',',$_POST['slide_str']);
			foreach($data as $val){
				$tem = explode('_',$val);
//				$tem[0]：类型 $tem[1]：类型id $tem[2]：结束时间
				switch($tem[0]){
					case 'ip':
						$order_ip = M();
						$arr = array('StartTime'=>date('Y-m-d H:i:s',time()),'EndTime'=>$tem[2]);
						$order_ip->table('Order_IP')->where('Order_ID='.$_POST['id'].' and IP_ID='.$tem[1])->setField($arr);
						break;
					case 'bandwidth':
						$order_bandwidth = M();
						$arr = array('StartTime'=>date('Y-m-d H:i:s',time()),'EndTime'=>$tem[2]);
						$order_bandwidth->table('Order_BandWidth')->where('Order_ID='.$_POST['id'].' and BandWidth_ID='.$tem[1])->setField($arr);
						break;
					case 'bandwidthExt':
						$order_bandwidthExt = M();
						$arr = array('StartTime'=>date('Y-m-d H:i:s',time()),'EndTime'=>$tem[2]);
						$order_bandwidthExt->table('Order_BandWidthExt')->where('Order_ID='.$_POST['id'].' and BandWidthExt_ID='.$tem[1])->setField($arr);
						break;
					case 'cabinet':
						$order_cabinet = M();
						$arr = array('StartTime'=>date('Y-m-d H:i:s',time()),'EndTime'=>$tem[2]);
						$order_cabinet->table('Order_Cabinet')->where('Order_ID='.$_POST['id'].' and Cabinet_ID='.$tem[1])->setField($arr);
						break;
				}
			}
		}
		if($is_ok===false){			
			$this->ajaxReturn('修改施工要求失败，请联系管理员。','error',0);
		}else{
			$this->ajaxReturn($ord['Code'],'success',1);
		}
		
	}
	//修改总价
	public function updateSumPrice(){
		$_SESSION['totalPrice'] = $_POST['sum'];
		$price = number_format($_SESSION['totalPrice'], 2);
		$this->ajaxReturn($price,'success',1);
	}
	//返回订单状态
	public function finishPayment(){
		$data = file_get_contents(C('INTERFACE_URL')."/alipay/order_query.php?OrderCode=".$_POST['code']."&DepartCode=ESS");
		$obj = json_decode($data,true);
		$this->ajaxReturn($obj[0]['Status'],'success',1);
	}
	//新增合同和工单
	public function insert(){
		if(!empty($_POST['id'])){
			$Order = M();
			$tem = $Order->table('Order')->where('ID='.$_POST['id'])->find();
			//描述
			$str = $this->getDesc();
			$billDay = $this->getBillDay();
			// post 请求方案
			include_once('HttpClient.class.php');
			
			$params = array('opt'=>'insert','data'=>json_encode(array('type'=>'业务新增','sellType'=>'IDC','content'=>'<p style="color:red"><h3>客户施工要求</h3>：<br/>'.$tem['DoReq'].';</p>'.$str,'stockId'=>'NULL','cabinet'=>'','ucount'=>'NULL','ucount2'=>'NULL','ucount4'=>'NULL','cmoney'=>$_SESSION['totalPrice'],'payType'=>'年付','beginDate'=>date('Y-m-d'),'endDate'=>date('Y-m-d'),'customId'=>$_SESSION['customID'],'about'=>$tem['DoReq'].';'.$str,'cpayDate'=>$billDay,'prePay'=>'1')));
			$pageContents = HttpClient::quickPost(C('INTERFACE_URL').'/ibss/contract.php', $params);
			$ret = json_decode($pageContents,true);
			
			$params2 = array('opt'=>'insert','data'=>json_encode(array('type'=>'新业务上架单','businessType'=>'IDC业务','ywtype'=>'租用','contractCode'=>$ret['error'],'doReq'=>$tem['DoReq'].';'.$str,'stockHouseId'=>'8','doTime'=>date('Y-m-d')))); 			
			$pageContents2 = HttpClient::quickPost(C('INTERFACE_URL').'/ibss/worklist.php', $params2);
			//修改订单状态
			$pageContents2 = json_decode($pageContents2,true);
			if($pageContents2['ret']==0){
				$data = array('IS_Pay'=>'YES','Status'=>'处理中','ContractCode'=>$ret['error']);
				$Order->table('Order')->where('ID='.$_POST['id'])->setField($data);
				$this->ajaxReturn($obj[0]['Status'],'success',1);
			}else{
				$this->ajaxReturn('订单状态修改失败，请联系管理员。','error',0);
			}
		}else{
			$this->ajaxReturn('新增合同/工单失败，请联系管理员。','error',0);
		}
	}
}