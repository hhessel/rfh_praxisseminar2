<?php

class dbQuerySet {
	public $cmd;
	public $tblName;
	public $fieldValues;
	public $whereValues;
	public $insertValues;
	public $updateValues;
	public $fullQuery;
	public $result;
	public $res;
	public $db;

	public function __construct() {
	
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
	
	public function insert($values) {
		$this->cmd = 'insert into';
		$this->insertValues = $values;
		return $this;
	}
	
	public function update($values) {
		$this->cmd = 'update';
		$this->updateValues = $values;
		return $this;
	}
	
	public function delete() {
		$this->cmd = 'delete from';
		return $this;
	}
	
	public function where($field, $value) {
		$this->whereValues[$field] = $value;
		return $this;
	}
	
	public function count() {
		$this->cmd = 'count';
		return $this;
	}
	
	public function execute() {
		$this->prepareQuery();
		$this->result = array();
		$this->res = mysql_query($this->fullQuery, $this->db->_connection) or die(mysql_error());
		
		switch($this->cmd) {
			case 'select':
				$i = 0;
				while ($row = mysql_fetch_array($this->res, MYSQL_ASSOC)) {
					$this->result[$i] = $row;
					$i++;
				}
				break;
			case 'count':
				$row = mysql_fetch_array($this->res);
				$this->result = $row[0];
				break;
		}
	
		return $this;
	}
	
	private function prepareQuery() {
		$cmd = $this->cmd;
		$values = $this->ms_escape_string($this->fieldValues);
		$table = $this->tblName;
		$where = $this->whereValues;		
		
		switch($cmd) {
		
			case 'select':
				$query = $cmd . ' ';
				if(is_array($values)) {
					$query .= implode(',' , $values);
				} else {
					$query .= $values;
				}
				$query .= ' from ' . $table . ' ';
				break;
				
			case 'count':
				$query = 'SELECT COUNT(*) as count from ' . $table . ' ';
				break;
				
			case 'insert into':
				$query = $cmd . ' ' . $table . ' ';
				$fields = '(';
				$values = '(';
				while (list($field, $value) = each($this->insertValues )) {
					$fields .= $field . ',';
					$values .= sprintf("'%s'", $value) . ',';
				}
				$fields = substr($fields,0,-1) . ')';
				$values = substr($values,0,-1) . ')';
				$query .= $fields . ' VALUES ' . $values; 
				break;
				
			case 'update':
				$query = $cmd . ' ' . $table . ' SET ';
				while (list($field, $value) = each($this->updateValues )) {
					$query .= $field . '=' . sprintf("'%s'", $value) . ',';
				}
				$query = substr($query,0,-1);
				break;
				
			case 'delete from':
				$query = $cmd . ' ' . $table;
				break;
		}
						
		if($where) {
			$query .= ' where';
			$where = $this->ms_escape_string($where);
			while (list($key, $value) = each($where)) {
					$query .= sprintf(" %s = '%s' AND", $key, $value);
				}		
				$query = substr($query,0,-3);
		}
		
		$query .= ';';
		// echo $query;
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

?>
