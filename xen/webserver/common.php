<?php

function return_result($ret, $result, $error)
{
    $res = array('ret' => $ret, 'result' => $result, 'error' => $error);
    print(json_encode($res));
}

function http_post_data($url, $post_data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}

?>
