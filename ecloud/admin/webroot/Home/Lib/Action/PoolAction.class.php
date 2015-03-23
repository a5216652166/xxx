<?php

class PoolAction extends Action {

	public function pool_list(){
		$sql = '
        	select p.*, g.Name from Pool p 
        	left join `Group` g on g.ID = p.GroupID
        ';

		$count = M()->query('select count(*) from (' . $sql . ') t');
        $count = $count[0]['count(*)'];

        $rpp = C('ROW_PER_PAGE');
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        $pager = PublicAction::get_pager(U(__FUNCTION__), $count, $rpp, $page);
        $this->pager = $pager;

		$sql .= ' limit ' . ($pager['pageNow'] - 1) * $rpp . ', ' . $rpp;
		$data = M()->query($sql);

		foreach($data as $k => $v){
			$data[$k]['IPS'] = M()->table('PoolIP')->where('PoolID='.$v['ID'])->select();
		}

		$this->data = $data;
		$this->display();
	}

	public function add(){
		if(empty($_POST)){
			$this->groups = M()->table('Group')->select();

			$this->return_url = U('pool_list');
			$this->display();
			return;
		}

		$ret = M()->table('Pool')->add($_POST);
		if($ret){
			$this->success('添加成功', U('pool_list'));
		}else{
			$this->error('添加失败');
		}
	}

	public function edit(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->groups = M()->table('Group')->select();
			$this->data = M()->table('Pool')->where('ID='.$_GET['id'])->find();
			
			$this->return_url = U('pool_list');
			$this->display();
			return;
		}

		$ret = M()->table('Pool')->where('ID='.$_POST['ID'])->save($_POST);
		if($ret !== false){
			$this->success('保存成功', U('pool_list'));
		}else{
			$this->error('保存失败');
		}
	}

	public function del(){
		if(empty($_GET['id'])){
			$this->error('调用错误');
		}

		$ret = M()->table('Pool')->where('ID='.$_GET['id'])->delete();
		if($ret){
			$this->success('删除成功', U('pool_list'));
		}else{
			$this->error('删除失败');
		}
	}

	public function checkCode(){
		$data = array();

		$ret = M()->table('Pool')->where('PoolCode=\'' . $_GET['PoolCode'] . '\'')->select();
		
		if(count($ret) > 0){
			$data['msg'] = '池编号重复';
		}

		$this->ajaxReturn($data);
	}

	public function get_pool(){
		$ret = M()->table('Pool')->where('PoolCode=\'' . $_GET['PoolCode'] . '\'')->find();

		$this->ajaxReturn($ret);
	}


	public function get_assign_ips($pass = array()){
		$ips = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/assignIp_query.php?CustomName=xen云平台项目'), true);
		$gips = M()->table('PoolIP')->select();
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

	public function add_pool_ip(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->data = M()->table('Pool')->where('ID='.$_GET['id'])->find();
			$this->ips = $this->get_assign_ips();

			$this->display();
			return;
		}

		$ips = split(";", $_POST['IP']);
		unset($_POST['IP']);
		$_POST['PublicIPBegin'] = $ips[0];
		$_POST['PublicIPEnd'] = $ips[1];

		$ret = M()->table('PoolIP')->add($_POST);
		if($ret !== false){
			$msg = array('status'=> 0,'msg'=>'添加成功');
		}else{
			$msg = array('status'=> 1,'msg'=>'添加失败');
		}

		$this->ajaxReturn($msg);
	}

	public function edit_pool_ip(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$ip = M()->table('PoolIP')->where('ID='.$_GET['id'])->find();
			$pool = M()->table('Pool')->where('ID='.$ip['PoolID'])->find();
			$ip['PoolCode'] = $pool['PoolCode'];
			$this->ips = $this->get_assign_ips(array(array('PublicIPBegin'=>$ip['PublicIPBegin'], 'PublicIPEnd'=>$ip['PublicIPEnd'])));

			$this->data = $ip;
			$this->display();
			return;
		}

		$ips = split(';', $_POST['IP']);
		unset($_POST['IP']);
		$_POST['PublicIPBegin'] = $ips[0];
		$_POST['PublicIPEnd'] = $ips[1];

		$ret = M()->table('PoolIP')->where('ID='.$_POST['ID'])->save($_POST);
		if($ret !== false){
			$msg = array('status'=> 0,'msg'=>'修改成功');
		}else{
			$msg = array('status'=> 1,'msg'=>'修改失败');
		}

		$this->ajaxReturn($msg);
	}

	public function del_pool_ip(){
		if(empty($_GET['id'])){
			$this->ajaxReturn('调用错误');
		}

		$ret = M()->table('PoolIP')->where('ID='.$_GET['id'])->delete();
		if($ret){
			$this->ajaxReturn('删除成功');
		}else{
			$this->ajaxReturn('删除失败');
		}
	}

}
