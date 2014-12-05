<?php
require_once('db.php');
require_once('common.php');

if( ! isset($_POST['data']) ) { exit; }

$post_data = json_decode($_POST['data'], TRUE);
if( ! isset($post_data['opt']) ) {
	;exit;
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
	case 'get_pool_list':
		do_get_pool_list($dbobj, $post_data);
		break;
	case 'get_pool_host_list':
		do_get_pool_host_list($dbobj, $post_data);
		break;
	case 'get_pool_vm_list':
		do_get_pool_vm_list($dbobj, $post_data);
		break;
	default:
		exit;
}

function do_get_pool_list($dbobj, $post_data)
{
	/*
		http://61.142.208.98/ecloud_vm_api/webserver/pool.php
		data={"opt":"get_pool_list","type":"all"}
	*/
	if( isset($post_data['type']) ) {
		$type = $post_data['type'];
		if( $type == 'all' ) {
			$query = "select * from `pool` where `status` = 'enable';";
		} else if( $type == 'vm' || $type == 'storage' ) {
			$query = "select * from `pool` where `status` = 'enable' and `type` = '$type';";
		} else {
			exit;
		}
		//print_r($query);
		$result_data = array();
		if( ! ($result = $dbobj->query($query)) ) {
			print($dbobj->error()."\n");
			exit;
		}
		if( ! mysql_num_rows($result) ) {
			print($dbobj->error()."\n");
			exit;
		}
		while( ($row = mysql_fetch_array($result)) ) {
			$result_data[] = array(
								'name' => $row['name'], 
								'type' => $row['type'],
								'desc' => $row['desc']
							);
		}
		mysql_free_result($result);
		return_result(0, $result_data, '');
	}	
}

function do_get_pool_host_list($dbobj, $post_data)
{
	global $global_uwsgi_url;
	/*
		http://61.142.208.98/ecloud_vm_api/webserver/pool.php
		data={"opt":"get_pool_host_list","pool_name":"XEN_ZSHJ_VM_POOL_TEST1"}
	*/
	if( isset($post_data['pool_name']) ) {
		$pool_name = $post_data['pool_name'];
		$query = "select * from `pool` where `status` = 'enable' and `name` = '$pool_name';";
		//print_r($query);
		$result_data = array();
		if( ! ($result = $dbobj->query($query)) ) {
			print($dbobj->error()."\n");
			exit;
		}
		if( ! mysql_num_rows($result) ) {
			print($dbobj->error()."\n");
			exit;
		}
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		$pool_data = json_decode($row['data'], TRUE);
		$post_data = json_encode(array('module' => 'ecloud_pool', 'opt' => 'get_host_list', 'pool_data' => $pool_data));
		$result_data = http_post_data($global_uwsgi_url, $post_data);
		//$result_data = json_decode($result_data, TRUE);
		//print_r($result_data);
		return_result(0, $result_data, '');
	}	
}

function do_get_pool_vm_list($dbobj, $post_data)
{
	global $global_uwsgi_url;
	/*
		http://61.142.208.98/ecloud_vm_api/webserver/pool.php
		data={"opt":"get_pool_vm_list","pool_name":"XEN_ZSHJ_VM_POOL_TEST1"}
	*/
	if( isset($post_data['pool_name']) ) {
		$pool_name = $post_data['pool_name'];
		$query = "select * from `pool` where `status` = 'enable' and `name` = '$pool_name';";
		//print_r($query);
		$result_data = array();
		if( ! ($result = $dbobj->query($query)) ) {
			print($dbobj->error()."\n");
			exit;
		}
		if( ! mysql_num_rows($result) ) {
			print($dbobj->error()."\n");
			exit;
		}
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		$pool_data = json_decode($row['data'], TRUE);
		$post_data = json_encode(array('module' => 'ecloud_pool', 'opt' => 'get_vm_list', 'pool_data' => $pool_data));
		$result_data = http_post_data($global_uwsgi_url, $post_data);
		//$result_data = json_decode($result_data, TRUE);
		//print_r($result_data);
		return_result(0, $result_data, '');
	}	
}

?>
