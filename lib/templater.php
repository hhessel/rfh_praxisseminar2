<?php 

class Templater {
	private $_content;
	private $_templateContent; 
	private $_baseDir;
	
	public function loadBaseTemplate($templateBaseDir, $templateFileName) {
		$this->_content = file_get_contents($templateBaseDir . '/' . $templateFileName);
		$this->_baseDir = $templateBaseDir;
		return $this;
	}
	
	public function loadTemplate($templateFileName) {
		$this->_templateContent = file_get_contents($this->_baseDir . '/' . $templateFileName);
		return $this;
	}
	
	public function attachTemplate($templateFileName) {
		$this->_templateContent .= file_get_contents($this->_baseDir . '/' . $templateFileName);
		return $this;
	}
	
	
	public function data($valuearray) {
		if(is_array($valuearray)) {
			foreach($valuearray as $key => $value) {
				$searchpattern = "/%%(".strtoupper($key).")%%/si";

				// Gefundene Platzhalter mit Werten aus $wertearray ersetzen
				$this->_content = preg_replace($searchpattern, $value, $this->_content);
				$this->_templateContent = preg_replace($searchpattern, $value, $this->_templateContent);
			}		
		}
		return $this;
	}
	
	public function show() {
		$searchpattern = "/%%(base_content)%%/si";
		$this->_content = preg_replace($searchpattern, $this->_templateContent, $this->_content);
		
		$this->removeEmptyTags();
		return $this->_content;
	} 
	
	public function showWelcome($currentUser) {
		$welcome = 'Hallo ' . $currentUser['firstname'] . ' ' . $currentUser['lastname'] . ' <a href="logout.php">Logout</a>';
		$data = array('welcome_message' => $welcome);
		$this->data($data);
	}
	
	private function removeEmptyTags() {
		$this->_content = preg_replace('~\%%\s*(.+?)\s*\%%~', '', $this->_content);
	}
}

?>