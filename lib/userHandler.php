<?php


class userHandler {
	private $db;
	private $loggedIn = false;
	private $salt = "X134cS45!§3";
	
	public function setDB($db) {
		$this->db = $db;
		return $this;
	}

	
	public function login($username, $password) {
		$potentials = $this->db->model('user')->count()->
			where('username',$username)->
			where('password',$password)->
			execute()->result;
		
		if($potentials > 0) {
			setcookie("username", $username, time()+3600); 
			setcookie("password", $password, time()+3600); 
			$this->loggedIn = true;
		} else {
			$this->loggedIn = false;
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
		if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
			$this->login($_COOKIE['username'], $_COOKIE['password']);
		}
		return $this->loggedIn;
	}
	
	private function md5($value) {
		return md5($value . $this->salt);
	}
}

?>