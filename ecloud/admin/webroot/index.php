<?php
	session_start();

	//登录或操作超时验证
	/*$wholePath = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'index.php') + strlen('index.php') + 1);
	//print_r($wholePath);exit;

	if($_SERVER['PATH_INFO'] !== '/Index/login'){
		if(empty($_SESSION['user'])){
			$_SESSION['last_action'] = time();
			header("Location: " . $wholePath . "Index/login");
			exit;
		}

		if(time() - $_SESSION['last_action'] > 2){
			unset($_SESSION['user']);
			$_SESSION['last_action'] = time();

			echo "
			<script type='text/javascript'>
				alert('操作超时，请重新登录');
				window.top.location.reload();
			</script>
			";

			exit;
		}
	}

	$_SESSION['last_action'] = time();*/

	//开启调试模式
	define('APP_DEBUG', true);
    //定义项目名称
    define('APP_NAME', 'Home');
    //定义项目路径
    define('APP_PATH', './Home/');
    //加载框架入文件
    require './ThinkPHP/ThinkPHP.php';

?>
