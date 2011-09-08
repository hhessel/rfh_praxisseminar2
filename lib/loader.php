<?php

// Simple Instance Generater Class
// by Henrik P. Hessel

function __autoload($class_name) {
	require_once 'lib/' . $class_name . '.php';
}

class loader {
	public $templater;
	public $db;
	public $userHandler;
	public $courseHandler;

	public static function loadBasicSetup() {
		include('config.php');
		$loader = new self();
		$loader->templater = new templater();
		$loader->templater= $loader->templater->loadBaseTemplate('tpl', 'base.html');
		
		$loader->db = new db();
		$loader->db = $loader->db->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);
		
		$loader->userHandler = new userHandler();
		$loader->userHandler = $loader->userHandler->setDB($loader->db);
		
		$loader->courseHandler = new courseHandler($loader->db, $loader->userHandler);

		return $loader;
	}
	
}

?>