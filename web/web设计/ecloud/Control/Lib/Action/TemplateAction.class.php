<?php

class TemplateAction extends Action {

	public function tem_list(){
		$count = M()->query('select count(*) from Template');
        $count = $count[0]['count(*)'];

        $rpp = C('ROW_PER_PAGE');
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        $pager = PublicAction::get_pager(U(__FUNCTION__), $count, $rpp, $page);
        $this->pager = $pager;

		$this->data = M()->table('Template')->limit(($pager['pageNow'] - 1) * $rpp, $rpp)->select();
		$this->display();
	}

	public function add(){
		if(empty($_POST)){

			$this->return_url = U('tem_list');
			$this->display();
			return;
		}

		$ret = M()->table('Template')->add($_POST);
		if($ret){
			$this->success('添加成功', U('tem_list'));
		}else{
			$this->error('添加失败');
		}
	}

	public function edit(){
		if(empty($_POST)){
			if(empty($_GET['id'])){
				$this->error('调用错误');
			}

			$this->data = M()->table('Template')->where('ID='.$_GET['id'])->find();

			$this->return_url = U('tem_list');
			$this->display();
			return;
		}

		$ret = M()->table('Template')->where('ID='.$_POST['ID'])->save($_POST);
		if($ret !== false){
			$this->success('保存成功', U('tem_list'));
		}else{
			$this->error('保存失败');
		}
	}

	public function del(){
		if(empty($_GET['id'])){
			$this->error('调用错误');
		}

		$ret = M()->table('Template')->where('ID='.$_GET['id'])->delete();
		if($ret){
			$this->success('删除成功', U('tem_list'));
		}else{
			$this->error('删除失败');
		}
	}

}
