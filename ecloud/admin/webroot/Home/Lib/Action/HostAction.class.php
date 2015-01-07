<?php

class HostAction extends Action {

	public function host_list(){
		$where = '1 = 1';
		if(!empty($_POST['PoolCode'])){
			$where .= ' and PoolCode = \'' . $_POST['PoolCode'] . '\'';
		}

		$count = M()->query('select count(*) from Host where ' . $where);
        $count = $count[0]['count(*)'];

        $rpp = C('ROW_PER_PAGE');
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        $pager = PublicAction::get_pager(U(__FUNCTION__), $count, $rpp, $page);
        $this->pager = $pager;

		$this->data = M()->table('Host')->where($where)->limit(($pager['pageNow'] - 1) * $rpp, $rpp)->select();
		//筛选条件
		$this->pools = M()->table('Pool')->select();

		$this->display();
	}

	public function get_vps($pass = array()){
		$propertys = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/assignProperty_query.php?CustomName=xen云平台项目&Type=服务器'), true);
		$hosts = M()->table('Host')->select();
		foreach($propertys as $k => $v){
			foreach($hosts as $k1 => $v1){
				if( $v['Code'] === $v1['PropertyCode'] && !in_array($v1['PropertyCode'], $pass) ){
					unset($propertys[$k]);
					break;
				}
			}
		}
		
		return $propertys;
	}

	public function add(){
		if(empty($_POST)){
			$this->pools = M()->table('Pool')->select();
			$this->propertys = $this->get_vps();

			$this->return_url = U('host_list');
			$this->display();
			return;
		}

		$ret = M()->table('Host')->add($_POST);
		if($ret){
			$this->success('添加成功', U('host_list'));
		}else{
			$this->error('添加失败');
		}
	}

	public function edit(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->pools = M()->table('Pool')->select();
			$this->data = M()->table('Host')->where('ID='.$_GET['id'])->find();
			$this->propertys = $this->get_vps(array($this->data['PropertyCode']));

			$this->return_url = U('host_list');
			$this->display();
			return;
		}

		$ret = M()->table('Host')->where('ID='.$_POST['ID'])->save($_POST);
		if($ret !== false){
			$this->success('保存成功', U('host_list'));
		}else{
			$this->error('保存失败');
		}
	}

	public function del(){
		if(empty($_GET['id'])){
			$this->error('调用错误');
		}

		$ret = M()->table('Host')->where('ID='.$_GET['id'])->delete();
		if($ret){
			$this->success('删除成功', U('host_list'));
		}else{
			$this->error('删除失败');
		}
	}


	/** 检查表单数据是否合法和有没有重复 **/
	public function checkHost(){
		$msg = array('ret' => 0, 'msg' => '');

		$ip = ip2long($_GET['ControlIP']);
		if($ip === false){
			$msg = array('ret' => 1, 'msg' => '控制IP非法');
			$this->ajaxReturn($msg);
			return;
		}

		$sip = ip2long($_GET['StorageIP']);
		if($sip === false){
			$msg = array('ret' => 1, 'msg' => '存储IP非法');
			$this->ajaxReturn($msg);
			return;
		}

		//获得主机所在池组信息
		$sql = "
			select g.* from `Group` g
			left join Pool p on p.GroupID = g.ID
			where p.PoolCode = '" . $_GET['PoolCode'] . "'
			limit 1
		";
		$group = M()->query($sql);
		$group = $group[0];

		if($ip < ip2long($group['ControlIPBegin']) || $ip > ip2long($group['ControlIPEnd'])){
			$msg = array('ret' => 1, 'msg' => '控制IP不属于该池组');
			$this->ajaxReturn($msg);
			return;
		}

		if($sip < ip2long($group['StorageIPBegin']) || $sip > ip2long($group['StorageIPEnd'])){
			$msg = array('ret' => 1, 'msg' => '存储IP不属于该池组');
			$this->ajaxReturn($msg);
			return;
		}

		if($_GET['ControlVlan'] < $group['ControlVlanBegin'] || $_GET['ControlVlan'] > $group['ControlVlanEnd']){
			$msg = array('ret' => 1, 'msg' => '控制Vlan不属于该池组');
			$this->ajaxReturn($msg);
			return;
		}

		if($_GET['StorageVlan'] < $group['StorageVlanBegin'] || $_GET['StorageVlan'] > $group['StorageVlanEnd']){
			$msg = array('ret' => 1, 'msg' => '存储Vlan不属于该池组');
			$this->ajaxReturn($msg);
			return;
		}


		$where = 'PoolCode=\'' . $_GET['PoolCode'] . '\' and ControlIP=\'' . $_GET['ControlIP'] . '\'';
		if(!empty($_GET['ID'])){
			$where .= ' and ID != ' . $_GET['ID'];
		}
		$ret = M()->table('Host')->where($where)->select();
		if(count($ret) > 0){
			$msg = array('ret' => 1, 'msg' => '控制IP已被使用');
			$this->ajaxReturn($msg);
			return;
		}

		$where = 'PoolCode=\'' . $_GET['PoolCode'] . '\' and StorageIP=\'' . $_GET['StorageIP'] . '\'';
		if(!empty($_GET['ID'])){
			$where .= ' and ID != ' . $_GET['ID'];
		}
		$ret = M()->table('Host')->where($where)->select();
		if(count($ret) > 0){
			$msg = array('ret' => 1, 'msg' => '存储IP已被使用');
			$this->ajaxReturn($msg);
			return;
		}

		$this->ajaxReturn($msg);
	}

}
