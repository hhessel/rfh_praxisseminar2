<?php

class loader {
	public $templater;
	public $db;
	public $userHandler;

	public function load($module) {
		if($module == 'templater') {
			include('templater.php');
			return new Templater();
		} else if ($module == 'db') {
			include('db.php');
			return new DB();
		} else if ($module == 'userHandler') {
			include('userHandler.php');
			return new userHandler();
		}
	}
	
	public static function loadBasicSetup() {
		include('config.php');
		$loader = new self();
		$loader->templater = $loader->load('templater')->loadBaseTemplate('tpl', 'base.html');
		$loader->db = $loader->load('db')->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);
		$loader->userHandler = $loader->load('userHandler')->setDB($loader->db);
		return $loader;
	}
}

?>