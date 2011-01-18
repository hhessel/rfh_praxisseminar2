<?php

// DB Connector

class dbQuerySet {
	public $cmd;
	public $tblName;
	public $fieldValues;
	public $whereValues;
	public $fullQuery;
	public $result;
	public $db;

	public function __construct() {
        //$this->fieldValues = new Array();
		//$this->whereValues = new Array();
    }
	
	 public static function selectTable($modelName) {
        $instance = new self();
        $instance->tblName = $modelName;
        return $instance;
    }

	public function select($values) {
		$this->cmd = 'select';
		$this->fieldValues = $values;
		return $this;
	}
	
	public function where($field, $value) {
		$this->whereValues[$field] = $value;
		return $this;
	}
	
	public function execute() {
		$this->prepareQuery();
		$result = mysql_query($this->fullQuery, $this->db->_connection) or die(mysql_error());
		if(strtolower($this->cmd) == 'select') {
			$this->result = mysql_fetch_array($result);
		}
		return $this;
	}
	

	private function prepareQuery() {
		$cmd = strtolower($this->cmd);
		$values = $this->ms_escape_string($this->fieldValues);
		$table = $this->tblName;
		$where = $this->whereValues;		
		
		$query = $cmd . ' ';
		switch($cmd) {
			case "select":
				if(is_array($values)) {
					$query .= implode(',' , $values);
				} else {
					$query .= $values;
				}
				$query .= ' from ' . $table . ' ';

				if($where) {
					$query .= ' where';
					$where = $this->ms_escape_string($where);
					while (list($key, $value) = each($where)) {
						$query .= ' ' . $key . ' = ' . $value;
					}

				}				
				$query .= ';';
				break;
		}
		
		echo $query;
		$this->fullQuery = $query;
		return $this;
	}
	
	private function ms_escape_string($data) {
        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
        $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
    }
	

}

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
		// $field_names = array_keys( mysql_fetch_array( mysql_query( $query, $$this->_connection), MYSQL_ASSOC)); 
		$this->_querySet = dbQuerySet::selectTable($modelName);
		$this->_querySet->db = $this;
		return $this->_querySet;
	}
	

}