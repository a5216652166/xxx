<?php
// 本类由系统自动生成，仅供测试用途
class PublicAction extends Action {
	
	public static function get_pager($url, $count, $rpp, $page){
        $total_page = ceil($count / $rpp);
        if($page < 1){
            $page = 1;
        }else if($page > $total_page){
            $page = $total_page;
        }

		$pager['url'] = $url;
        $pager['count'] = $count;
        $pager['pageNow'] = $page;
        $pager['pageTotal'] = $total_page;
        $pager['first'] = 1;
        $pager['prev'] = ($page <= 1 ? 1 : $page - 1);
        $pager['next'] = ($page >= $total_page ? $total_page : $page + 1);
        $pager['last'] = $total_page;

        return $pager;
	}

    public function send_get(){
        echo file_get_contents($_GET['url']);
    }

    public function send_post(){
        $a = $_GET['data'];

        $opt = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $a
            )
        );

        $ctx = stream_context_create($opt);

        $ret = file_get_contents($url, false, $ctx);

        echo json_decode($ret, true);
    }

}