<?php


class userHandler {
	private $db;
	private $loggedIn = false;
	private $currentUser;
	private $salt = "X134cS45!§3";
	private $cookieSet;
	
	public function setDB($db) {
		$this->db = $db;
		return $this;
	}

	public function login($username = "", $password = "", $saltedPassword = true) {
	
		if($username && $saltedPassword) {
			$password = $this->md5Hash($password);
		} else if(!$username) {
		
			if($this->getCookieSet()->isCookieSetValid()) {
				$username = $this->cookieSet['username'];
				$password = $this->cookieSet['password'];
			}
			
		}
		
		$currentUser = $this->db
			->model('user')->select('*')
			->where('username',$username)
			->where('password',$password)->
			execute()->result[0];

		if(count($currentUser) > 0) {
			$this->createCookieSet($username, $password);
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
	
	public function register($username, $password, $firstname, $lastname) {
		$password = $this->md5Hash($password);
		
		$count = $this->db
			->model('user')
			->count()
			->where('username',$username)
			->execute();
			
		if($count->result > 0) {
			return $this;
		}
		
		$this->db->model('user')->insert(
			array(
				'username' => $username, 
				'password' => $password,
				'firstname' => $firstname,
				'lastname' => $lastname
			))->execute();
			
		$this->login($username, $password, false);
			
		return $this;
	}
	
	public function isLoggedIn() {
		return $this->loggedIn;
	}
		
	public function getCurrentUser() {
		return $this->currentUser;		
	}
	
	public function isAdmin() {
		return ($this->currentUser['admin'] == 1) ? true : false;
	}
	
	private function createCookieSet($username, $password) {
		setcookie("username", $username, time()+3600); 
		setcookie("password", $password, time()+3600); 
		$this->cookieSet = array('username' => $username, 'password' => $password);
		return $this;
	}
	
	private function isCookieSetValid() {
		return (isset($this->cookieSet)) ? true : false;
	}
	
	private function getCookieSet () {
		$this->cookieSet = array('username' => $_COOKIE['username'], 'password' => $_COOKIE['password']);
		return $this;
	}
	
	private function md5Hash($value) {
		return md5($value . $this->salt);
	}
	
}

?>