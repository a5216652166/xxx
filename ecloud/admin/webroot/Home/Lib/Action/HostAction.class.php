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

	public function get_vps(){
		$pool = M()->table('Pool')->where('PoolCode=\''.$_GET['PoolCode'].'\'')->find();

		$url = 'http://api.efly.cc/ibss/standard/property_query.php?opt=get_vps&House=' . $pool['StockHouseName'];
		echo file_get_contents($url);
	}

	public function add(){
		if(empty($_POST)){
			$this->pools = M()->table('Pool')->select();

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


	/** 检查控制IP是否合法和有没有重复 **/
	public function checkIP(){
		$msg = array('ret' => 0, 'msg' => '');

		$ip = ip2long($_GET['IP']);
		if($ip === false){
			$msg = array('ret' => 1, 'msg' => '控制IP非法');
			$this->ajaxReturn($msg);
			return;
		}

		$ret = M()->table('Pool')->where('PoolCode=\'' . $_GET['PoolCode'] . '\'')->find();
		if($ip < ip2long($ret['ControlIPBegin']) || $ip > ip2long($ret['ControlIPEnd'])){
			$msg = array('ret' => 1, 'msg' => '控制IP不属于该池');
			$this->ajaxReturn($msg);
			return;
		}

		$where = 'PoolCode=\'' . $_GET['PoolCode'] . '\' and ControlIP=\'' . $_GET['IP'] . '\'';
		if(!empty($_GET['ID'])){
			$where .= ' and ID != ' . $_GET['ID'];
		}

		$ret = M()->table('Host')->where($where)->select();
		if(count($ret) > 0){
			$msg = array('ret' => 1, 'msg' => '控制IP已被使用');
			$this->ajaxReturn($msg);
			return;
		}

		$this->ajaxReturn($msg);
	}

}
