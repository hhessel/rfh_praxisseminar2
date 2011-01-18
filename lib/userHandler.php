<?php

class userHandler {
	private $_db;
	
	public function setDB($db) {
		$this->db = $db;
	}
	
	public function login($username, $password) {
		$query = new dbQuerySet('SELECT', 'User', array('username' , 'password'));
		$query->where = array('username='+$username, 'password='+$password);
		$execute = $this->db->execute($this->db->prepareQuery($query));
	}
	
	public function logout() {

	}
	
	public function register($username, $password) {
	
	}
	
	public function isLoggedIn() {
	
	}
}

?>