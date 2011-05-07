<?php

// Simple SQL Abstraction Class
// by Henrik P. Hessel

class dbQuerySet {
	public $cmd;
	public $tblName;
	public $fieldValues;
	public $whereValues;
	public $orderBy; 
	public $ascDesc; 
	public $insertValues;
	public $updateValues;
	public $fullQuery;
	public $result;
	public $res;
	public $db;

	// Select Table for Query
	public static function selectTable($modelName) {
		$instance = new self();
		$instance->tblName = $modelName;
		return $instance;
	}

	// Set SELECT Command and corresponding values
	public function select($values = '*') {
		$this->cmd = 'select';
		$this->fieldValues = $values;
		return $this;
	}
	
	// Set INSERT INTO Command and corresponding values
	public function insert($values) {
		$this->cmd = 'insert into';
		$this->insertValues = $values;
		return $this;
	}
	
	// Set WHERE Command and corresponding values
	public function update($values) {
		$this->cmd = 'update';
		$this->updateValues = $values;
		return $this;
	}
	
	// Set DELETE Command and corresponding values
	public function delete() {
		$this->cmd = 'delete from';
		return $this;
	}
	
	// Filter Query by Where
	public function where($field, $value) {
		$this->whereValues[$field] = $value;
		return $this;
	}
	
	// OrderBy corresponding field
	public function orderby($field, $order = 'ASC') {
		$this->orderBy = $field;
		$this->ascDesc = $order;
		return $this;
	}
	
	// Set Query to count values
	public function count() {
		$this->cmd = 'count';
		return $this;
	}
	
	// Builds the Query,executes it, and loads the result into an array
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
	
	// Builds QueryString
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
			while (list($key, $value) = each($where)) {
					$value = $this->ms_escape_string($value);
					(is_string($value)) ? $query .= sprintf(" %s = '%s' AND", $key, $value) : 	$query .= sprintf(" %s = %s AND", $key, $value);
				}		
				$query = substr($query,0,-3);
		}
		
		if($this->orderBy) {
			$query .= ' ORDER BY ' . $this->orderBy  . ' ' . $this->ascDesc;
		}	
		
		$query .= ';';
		// echo $query;
		$this->fullQuery = $query;
		return $this;
	}
	
	// removes invalid characters in query and values
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
