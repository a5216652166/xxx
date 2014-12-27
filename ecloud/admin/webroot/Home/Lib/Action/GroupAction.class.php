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

	public function add_group_ip(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->data = M()->table('Group')->where('ID='.$_GET['id'])->find();
			$this->ips = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/ip_query.php?opt=get_cloud'), true);

			/*排除已经添加的
			foreach($this->ips as $k => $v){
				foreach($this->ipsnow as $v1){
					if($v['Begin'] === $v1['IPBegin'] && $v['End'] === $v1['IPEnd']){
						unset($this->ips[$k]);
					}
				}
			}*/

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
			$ips = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/ip_query.php?opt=get_cloud'), true);

			$this->ips = $ips;
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

}
