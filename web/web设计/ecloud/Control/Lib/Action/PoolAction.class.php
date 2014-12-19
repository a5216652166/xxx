<?php

class PoolAction extends Action {

	public function pool_list(){
		$count = M()->query('select count(*) from Pool');
        $count = $count[0]['count(*)'];

        $rpp = C('ROW_PER_PAGE');
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        $pager = PublicAction::get_pager(U(__FUNCTION__), $count, $rpp, $page);
        $this->pager = $pager;

		$this->data = M()->table('Pool')->limit(($pager['pageNow'] - 1) * $rpp, $rpp)->select();

		$this->display();
	}

	public function add(){
		if(empty($_POST)){
			$this->house = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/house_query.php'), true);

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

			$this->house = json_decode(file_get_contents('http://api.efly.cc/ibss/standard/house_query.php'), true);
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

}
