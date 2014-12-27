<?php
class BaseAction extends Action {
	
	/*public $user;
	public function _initialize(){					
		$user = $this->saveCurrentUserSession();
		if (empty($user)) {
			$actionName = strtolower(ACTION_NAME);
		if (!in_array($actionName, array("login", "checklogin"))) {
			header("Location: http://192.168.85.166/ecloud/index.php");
		exit;
		} 
	}
	$this->user = $user;
	}
	function saveCurrentUserSession() {
		return isset($_SESSION['user']) ? $_SESSION['user'] :null;
	}*/
}
