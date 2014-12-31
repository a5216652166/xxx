<?php

class DB {

	var $host;
	var $username;
	var $password;
	var $dbname;
	
	var $conn;

	function DB($host='ibss.efly.cc', $username='root', $password='rjkj@2009#8', $dbname='gift'){
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->dbname = $dbname;

		return $this;
	}

	function openConn(){
		$this->conn = mysql_connect($this->host,$this->username,$this->password) or die(mysql_error());

		mysql_query('set names utf8');
		mysql_select_db($this->dbname);

		return $this->conn;
	}

	function closeConn(){
		if($this->conn){
			mysql_close($this->conn);
		}
	}

	function query($sql){
		$data = array();
		
		$rs = mysql_query($sql, $this->conn);
		while($a = mysql_fetch_array($rs, MYSQL_ASSOC)){
			$data[] = $a;
		}

		return $data;
	}

	function execute($sql){
		return mysql_query($sql, $this->conn);
	}
	function http_post($url, $data){
		//$data=array();
		$data = array('data' => json_encode($data));
		$data_url = http_build_query ($data);
		$data_len = strlen ($data_url);

		return array('content'=>file_get_contents ($url, false, stream_context_create (array ('http'=>array ('method'=>'POST'
                 , 'header'=>"Connection: close\r\nContent-Length: $data_len\r\n"
                 , 'content'=>$data_url
                 ))))
                , 'headers'=>$http_response_header
                );
	}
}

?>

