<?php
require_once('db.php');

if( ! isset($_POST['data']) ) { exit; }

$post_data = json_decode($_POST['data'], TRUE);
if( ! isset($post_data['opt']) ) {
	exit;
}
if( isset($post_data['data']) ) {
	$post_data['data'] = json_decode($post_data['data'], TRUE);
}
//print_r($post_data);

$dbobj = new DBObj;
if( ! $dbobj->conn() ) {
	print($dbobj->error()."\n");
	exit;
}

$dbobj->query("set names utf8;");
$dbobj->select_db('ecloud_vm_api');

switch( $post_data['opt'] ) {
	case 'submit':
		do_submit_task($dbobj, $post_data);
		break;
	case 'get':
		do_get_task($dbobj, $post_data);
		break;
	default:
		exit;
}

function do_submit_task($dbobj, $post_data)
{
	$task_data = $post_data['data'];
	$task_json_data = json_encode($task_data);
	$query = "insert into `task`(`user`, `type`, `sub_type`, `submit_time`, `data`, `status`, `start_time`, `finish_time`, `ret`, `result`, `error`) 
			values('$post_data[user]', '$post_data[type]', '$post_data[sub_type]', now(), '$task_json_data', 'init', '', '', 0, '', '');";
	print($query);
	//$dbobj->query($query);
}

function do_get_task($dbobj, $post_data)
{
	if( isset($post_data['totype']) && isset($post_data['todo']) ) {
		$totype = $post_data['totype'];
		$todo = $post_data['todo'];
		$query = "select * from `task` where `totype` = '$totype' and `todo` = '$todo';";
		//print_r($query);
		if( ! ($result = $dbobj->query($query)) ) {
			print($dbobj->error()."\n");
			exit;
		}
		if( ! mysql_num_rows($result) ) {
			print($dbobj->error()."\n");
			exit;
		}
		while( ($row = mysql_fetch_array($result)) ) {
			print($row['data']);
		}
		mysql_free_result($result);
	}	
}

function return_result($ret, $result, $error)
{
	$res = array('ret' => $ret, 'result' => $result, 'error' => $error);
	print($res);
}
?>
