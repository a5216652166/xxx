<?php
// 本类由系统自动生成，仅供测试用途
class CloudMonitorAction extends BaseAction {
	
    public function index(){
		$this->display();
    }

    public function idc_server3(){
    	$this->source = 'all';
    	$this->type = array('ip','iface','cpu','ram','disk');

    	$this->display();
    }

	public function idc_server(){
		/*print_r($_GET['source']);
		print_r($_GET['type']);*/

		//参数
		if(!empty($_GET['source'])){
			$this->source = $_GET['source'];
			if($_GET['source'] == 'idc' || $_GET['source'] == 'all'){
				$source['idc'] = true;				
			}if($_GET['source'] == 'cloud' || $_GET['source'] == 'all'){
				$source['cloud'] = true;
			}
		}
		if(!empty($_GET['type'])){
			$this->type = $_GET['type'];
		}

		$this->display();
		return;

		//1.获取idc托管的设备, 从IBSS
		if($source['idc']){
			$userName = '睿江研发Ecloud项目';
			$userName = '比特捷';
			$propertys = file_get_contents('http://api.efly.cc/ibss/standard/assignProperty_query.php?CustomName=' . $shortName);
			foreach ($propertys as $k => $v) {
				//$ip = file_get_contents('http://api.efly.cc/ibss/standard/assignIp_query.php?PropertyCode=' . $v['Code']);
				$data[] = $v['Code'];
			}
		}

		//2.查云主机IP, 从ecloud_online
		if($source['cloud']){
			//$_SESSION['user'];
			/*$userName = '703696673@qq.com';

			$sql = '
				select vb.* from Ad_VPSBuy vb
				left join Ad_OrderVPSBuy ovb on ovb.VPSBuy_ID = vb.ID
				left join Ad_Order o on o.ID = ovb.Order_ID
				left join Ad_Login l on l.ID = o.Login_ID
				where l.Name = \'' . $userName . '\' 
			';
			$propertys = M()->query($sql);
			foreach ($propertys as $k => $v) {
				$data[] = $v['VMCode'];
			}
			*/
		}

		$this->data = $data;
		$this->display();
	}

	public function cloud_server(){
		if(!empty($_GET['type'])){
			$this->type = $_GET['type'];
		}

		$this->display();
		return;

		//2.查云主机IP, 从ecloud_online
		//$_SESSION['user'];
		/*$userName = '703696673@qq.com';

		$sql = '
			select vb.* from Ad_VPSBuy vb
			left join Ad_OrderVPSBuy ovb on ovb.VPSBuy_ID = vb.ID
			left join Ad_Order o on o.ID = ovb.Order_ID
			left join Ad_Login l on l.ID = o.Login_ID
			where l.Name = \'' . $userName . '\' 
		';
		$propertys = M()->query($sql);
		foreach ($propertys as $k => $v) {
			$data[] = $v['VMCode'];
		}
		*/

		$this->data = $data;
		$this->display();
	}

	public function get_data(){
		//for test
		$ws = 'http://120.31.133.107:8080/pxipweb/services/DateQuery?wsdl';
		$client = new SoapClient($ws);

		$iterfaces = array('fscnc');
		$ips = array('119.145.139.18');

		$args[] = $iterfaces;
		$args[] = date('Y-m-d H:i:s', strtotime('-1 day'));
		$args[] = date("Y-m-d H:i:s");
		$args[] = $ips;

		$result = $client->__call("query_ips_fivemin", $args);

		$result = json_decode($result, true);

		//轮询ip
		foreach ($result as $k => $v) {
			//轮询流量数据
			foreach($v['data'] as $k1 => $v1){
				$data[$v['ip']][] = array(
					'keytime' => $v1['keytime'],
					'inflow' => $v1['inflow'],
					'outflow' => $v1['outflow']
				);
			}
		}

		echo json_encode($data);
	}

	public function get_last_5_data(){
		//for test
		$ws = 'http://120.31.133.107:8080/pxipweb/services/DateQuery?wsdl';
		$client = new SoapClient($ws);

		$iterfaces = array('fscnc');
		$ips = array('119.145.139.18');

		$args[] = $iterfaces;
		$args[] = date('Y-m-d H:i:s', strtotime('-1 day'));
		$args[] = date("Y-m-d H:i:s");
		$args[] = $ips;

		$result = $client->__call("query_ips_fivemin", $args);

		$result = json_decode($result, true);

		//轮询ip
		foreach ($result as $k => $v) {
			//轮询流量数据
			$ip = $v['data'][count($v['data']) - 1];
			$data = array(
				'keytime' => $ip['keytime'],
				'inflow' => $ip['inflow'],
				'outflow' => $ip['outflow']
			);
		}

		echo json_encode($data);
	}

	public function ip(){
		$this->display();
	}
	
}
