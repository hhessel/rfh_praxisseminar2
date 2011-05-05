<?php

// Simple UserHandler Class
// by Henrik P. Hessel

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

	// logs the user in 
	public function login($username = "", $password = "", $saltedPassword = true) {
	
		if($username && $saltedPassword) {
			$password = $this->md5Hash($password);
		} else if(!$username) {
			if($this->getCookieSet()->isCookieSetValid()) {
				$username = $this->cookieSet['username'];
				$password = $this->cookieSet['password'];
			}	
		}
		
		$queryUser = $this->db
			->model('user')->select('*')
			->where('username',$username)
			->where('password',$password)->
			execute();
			
		if(count($queryUser->result) > 0) {
			$this->createCookieSet($username, $password);
			$this->loggedIn = true;
			$this->currentUser = $queryUser->result[0];
		} else {
			$this->loggedIn = false;
			$this->currentUser = null;
		}
		
		return $this;
	}
	
	// logs the user out and removes the cookies
	public function logout() {
		setcookie('username','',time()-3600);
		setcookie('password','',time()-3600);
		$this->loggedIn = false;
	}
	
	// register and login 
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
	
	// return boolean if the user is logged in
	public function isLoggedIn() {
		return $this->loggedIn;
	}
		
	// return currentUser
	public function getCurrentUser() {
		return $this->currentUser;		
	}
	
	// returns boolean if the current user is an admin
	public function isAdmin() {
		return ($this->currentUser['isAdmin']) ? true : false;
	}
	
	// create Cookies for currentUser
	private function createCookieSet($username, $password) {
		setcookie("username", $username, time()+3600); 
		setcookie("password", $password, time()+3600); 
		$this->cookieSet = array('username' => $username, 'password' => $password);
		return $this;
	}
	
	// return boolean is cookies are set
	private function isCookieSetValid() {
		return (isset($this->cookieSet)) ? true : false;
	}
	
	private function getCookieSet () {
		$this->cookieSet = (array_key_exists('username', $_COOKIE)) ? array('username' => $_COOKIE['username'], 'password' => $_COOKIE['password']) : null;
		return $this;
	}
	
	// return salted md5 hash
	private function md5Hash($value) {
		return md5($value . $this->salt);
	}
	
}

?>