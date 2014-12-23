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

	public function set_group_ip(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->data = M()->table('Group')->where('ID='.$_GET['id'])->find();
			$this->ipsnow = M()->table('GroupIP')->where('GroupID='.$_GET['id'])->select();
			$this->ips = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/ip_query.php?opt=get_cloud'), true);

			/*排除已经添加的
			foreach($this->ips as $k => $v){
				foreach($this->ipsnow as $v1){
					if($v['Begin'] === $v1['IPBegin'] && $v['End'] === $v1['IPEnd']){
						unset($this->ips[$k]);
					}
				}
			}*/

			$this->return_url = U('group_list');
			$this->display();
			return;
		}


		//$_POST['IPS']中去掉已添加的，并删除$ipOld中不存在于$_POST['IPS']的
		//print_r($_POST['IPS']);
		$ipOld = M()->table('GroupIP')->where('GroupID='.$_POST['ID'])->select();
		foreach($ipOld as $v){
			$in = false;
			foreach ($_POST['IPS'] as $k1 => $v1) {
				if($v['IPBegin'] . ';' . $v['IPEnd'] === $v1){
					$in = true;
					unset($_POST['IPS'][$k1]);
				}
			}

			if($in == false){
				M()->table('GroupIP')->where('ID='.$v['ID'])->delete();
			}
		}

		//现在$_POST['IPS']中已经是不重复的，直接添加
		//print_r($_POST['IPS']);exit;
		$success = true;
		foreach($_POST['IPS'] as $v){
			$ips = split(";", $v);
			$ret = M()->table('GroupIP')->add(array(
				'GroupID' => $_POST['ID'],
				'IPBegin' => $ips[0],
				'IPEnd' => $ips[1]
			));
			if($ret === false){
				$success = false;
			}
		}

		if($success){
			$this->success('修改IP成功', U('group_list'));
		}else{
			$this->success('一个或多个IP修改失败', U('set_group_ip?ID='.$_POST['ID']));
		}
	}

}
