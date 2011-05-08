<?php
// DB Connector

require("dbQuerySet.php");

class DB {

	private $_host;
	private $_username;
	private $_password;
	private $_querySet;
	public $_connection;
	

	public function open($host, $username, $password) {
		 $this->_username = $username;
		 $this->_password = $password;
		 $this->_host = $host;
		 $this->_connection = mysql_connect($host, $username, $password);

		 return $this;
	}
		
	public function selectDB($dbName) {
		mysql_select_db($dbName, $this->_connection);
		return $this;
	}
	
	public function model($modelName) {
		$this->_querySet = null;
		$this->_querySet = dbQuerySet::selectTable($modelName);
		$this->_querySet->db = $this;
		return $this->_querySet;
	}
	
}

?>