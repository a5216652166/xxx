<?php

class GroupAction extends Action {

	public function group_list(){
		$count = M()->query('select count(*) from `Group` ');
        $count = $count[0]['count(*)'];

        $rpp = C('ROW_PER_PAGE');
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        $pager = PublicAction::get_pager(U(__FUNCTION__), $count, $rpp, $page);
        $this->pager = $pager;

		$data = M()->table('Group')->limit(($pager['pageNow'] - 1) * $rpp, $rpp)->select();
		foreach($data as $k => $v){
			$data[$k]['IPS'] = M()->table('GroupIP')->where('GroupID='.$v['ID'])->select();
		}

		$this->data = $data;
		$this->display();
	}

	public function add(){
		if(empty($_POST)){
			$this->house = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/house_query.php'), true);

			$this->return_url = U('group_list');
			$this->display();
			return;
		}

		$ret = M()->table('Group')->add($_POST);
		if($ret){
			$this->success('添加成功', U('group_list'));
		}else{
			$this->error('添加失败');
		}
	}

	public function edit(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->house = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/house_query.php'), true);
			$this->data = M()->table('Group')->where('ID='.$_GET['id'])->find();

			$this->return_url = U('group_list');
			$this->display();
			return;
		}

		$ret = M()->table('Group')->where('ID='.$_POST['ID'])->save($_POST);
		if($ret !== false){
			$this->success('保存成功', U('group_list'));
		}else{
			$this->error('保存失败');
		}
	}

	public function del(){
		if(empty($_GET['id'])){
			$this->error('调用错误');
		}

		$ret = M()->table('GroupIP')->where('GroupID='.$_GET['id'])->delete();
		$ret = M()->table('Group')->where('ID='.$_GET['id'])->delete();
		if($ret){
			$this->success('删除成功', U('group_list'));
		}else{
			$this->error('删除失败');
		}
	}

	public function get_assign_ips($pass = array()){
		$ips = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/assignIp_query.php?CustomName=xen云平台项目'), true);
		$gips = M()->table('GroupIP')->select();
		foreach($ips as $k => $v){
			foreach($gips as $k1 => $v1){
				if($v['Begin'] === $v1['PublicIPBegin'] && $v['End'] === $v1['PublicIPEnd'] ){
					//判断是否在pass中
					$inPass = false;
					foreach($pass as $v2){
						if($v['Begin'] === $v2['PublicIPBegin'] && $v['End'] === $v2['PublicIPEnd'] ){
							$inPass = true;
							break;
						}
					}

					if(false === $inPass){
						unset($ips[$k]);
						unset($gips[$k1]);
					}
					
					break;
				}
			}
		}

		//print_r($ips);
		return $ips;
	}

	public function add_group_ip(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->data = M()->table('Group')->where('ID='.$_GET['id'])->find();
			$this->ips = $this->get_assign_ips();

			$this->display();
			return;
		}

		$ips = split(";", $_POST['IP']);
		unset($_POST['IP']);
		$_POST['PublicIPBegin'] = $ips[0];
		$_POST['PublicIPEnd'] = $ips[1];

		$ret = M()->table('GroupIP')->add($_POST);
		if($ret !== false){
			$msg = array('status'=> 0,'msg'=>'添加成功');
		}else{
			$msg = array('status'=> 1,'msg'=>'添加失败');
		}

		$this->ajaxReturn($msg);
	}

	public function edit_group_ip(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$ip = M()->table('GroupIP')->where('ID='.$_GET['id'])->find();
			$group = M()->table('Group')->where('ID='.$ip['GroupID'])->find();
			$ip['GroupName'] = $group['Name'];
			$this->ips = $this->get_assign_ips(array(array('PublicIPBegin'=>$ip['PublicIPBegin'], 'PublicIPEnd'=>$ip['PublicIPEnd'])));

			$this->data = $ip;
			$this->display();
			return;
		}

		$ips = split(';', $_POST['IP']);
		unset($_POST['IP']);
		$_POST['PublicIPBegin'] = $ips[0];
		$_POST['PublicIPEnd'] = $ips[1];

		$ret = M()->table('GroupIP')->where('ID='.$_POST['ID'])->save($_POST);
		if($ret !== false){
			$msg = array('status'=> 0,'msg'=>'修改成功');
		}else{
			$msg = array('status'=> 1,'msg'=>'修改失败');
		}

		$this->ajaxReturn($msg);
	}

	public function del_group_ip(){
		if(empty($_GET['id'])){
			$this->ajaxReturn('调用错误');
		}

		$ret = M()->table('GroupIP')->where('ID='.$_GET['id'])->delete();
		if($ret){
			$this->ajaxReturn('删除成功');
		}else{
			$this->ajaxReturn('删除失败');
		}
	}


	public function get_group_by_pool_code(){
		$sql = "
			select g.* from `Group` g
			left join Pool p on p.GroupID = g.ID
			where p.PoolCode = '" . $_GET['PoolCode'] . "'
			limit 1
		";

		$ret = M()->query($sql);
		$ret = $ret[0];

		$sql = "
			select ControlIP, StorageIP from `Host` where PoolCode = '" . $_GET['PoolCode'] . "'
		";
		$ips = M()->query($sql);
		//整理IP数据
		foreach($ips as $v){
			$ip[] = ip2long($v['ControlIP']);
			$sip[] = ip2long($v['StorageIP']);
		}

		//去掉已经使用的控制IP
		$begin = ip2long($ret['ControlIPBegin']);
		$end = ip2long($ret['ControlIPEnd']);
		while($begin <= $end && in_array($begin, $ip)){
			$begin++;
		}
		$ret['TheIP'] = $begin === $end ? '全部控制IP已经被使用' : long2ip($begin);

		//去掉已经使用的存储IP
		$begin = ip2long($ret['StorageIPBegin']);
		$end = ip2long($ret['StorageIPEnd']);
		while($begin <= $end && in_array($begin, $sip)){
			$begin++;
		}
		$ret['TheSIP'] = $begin === $end ? '全部控制IP已经被使用' : long2ip($begin);

		$this->ajaxReturn($ret);
	}

}
