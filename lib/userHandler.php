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

	public function login($username = "", $password = "", $saltedPassword = true) {
		if($username && $saltedPassword) {
			$password = $this->md5Hash($password);
		} else if(!$username) {
			if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
				$username = $_COOKIE['username'];
				$password = $_COOKIE['password'];
			}
		}
		
		$currentUser = $this->db->model('user')->select('*')->
			where('username',$username)->
			where('password',$password)->
			execute()->result[0];

		if(count($currentUser) > 0) {
			setcookie("username", $username, time()+3600); 
			setcookie("password", $password, time()+3600); 
			$this->loggedIn = true;
			$this->currentUser = $currentUser;
		} else {
			$this->loggedIn = false;
			$this->currentUser = null;
		}
		return $this;
	}
	
	public function logout() {
		setcookie('username','',time()-3600);
		setcookie('password','',time()-3600);
		$this->loggedIn = false;
	}
	
	public function register($username, $password) {
		$password = $this->md5Hash($password);
		
		$exists = $this->db->model('user')->select('*')->
			where('username',$username)->
			execute()->result;
			
		if(count($exists) > 0) {
			return $this;
		}
		
		$this->db->model('user')->insert(
			array(
				'username' => $username, 
				'password' => $this->md5Hash($password)
			))->execute();
			
		$this->login($username, $password);
			
		return $this;
	}
	
	public function isLoggedIn() {
		return $this->loggedIn;
	}
	
	public function getCurrentUser() {
		return $this->currentUser;		
	}
	
	
	private function md5Hash($value) {
		return md5($value . $this->salt);
	}
	
}

?>