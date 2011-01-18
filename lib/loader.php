<?php

class loader {

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
	
}

?>