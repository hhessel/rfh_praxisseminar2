<?php


class userHandler {
	private $db;
	private $loggedIn = false;
	private $currentUser;
	private $salt = "X134cS45!§3";
	
	public function setDB($db) {
		$this->db = $db;
		return $this;
	}

	public function login($username, $password) {
		$currentUser = $this->db->model('user')->select('*')->
			where('username',$username)->
			where('password',$password)->
			execute()->result;
		
		if($currentUser > 0) {
			setcookie("username", $username, time()+3600); 
			setcookie("password", $password, time()+3600); 
			$this->loggedIn = true;
			$this->currentUser = $currentUser;
		} else {
			$this->loggedIn = false;
			$this->currentUSer = null;
		}
		return $this;
	}
	
	public function logout() {
		setcookie("username");
		setcookie("password");
		$this->loggedIn = false;
	}
	
	public function register($username, $password) {
	
	}
	
	public function isLoggedIn() {
		if(!$this->loggedIn) {
			if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
				$this->login($_COOKIE['username'], $_COOKIE['password']);
			}
		}
		return $this->loggedIn;
	}
	
	public function getCurrentUser() {
		return $this->currentUser;		
	}
	
	
	private function md5($value) {
		return md5($value . $this->salt);
	}
	
}

?>