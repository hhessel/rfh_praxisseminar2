<?php 

// Simple Template Class
// by Henrik P. Hessel


class Templater {
	private $_content;
	private $_templateContent; 
	private $_baseDir;
	
	// Loads the Base Template into Cache
	public function loadBaseTemplate($templateBaseDir, $templateFileName) {
		$this->_content = file_get_contents($templateBaseDir . '/' . $templateFileName);
		$this->_baseDir = $templateBaseDir;
		return $this;
	}
	
	
	// Loads the Template into Cache
	public function loadTemplate($templateFileName) {
		$this->_templateContent = file_get_contents($this->_baseDir . '/' . $templateFileName);
		return $this;
	}
	
	// Attaching other template to existing template in cache
	public function attachTemplate($templateFileName) {
		$this->_templateContent .= file_get_contents($this->_baseDir . '/' . $templateFileName);
		return $this;
	}
	
	// Replace placeholder (Format %%placeholder%%) with values in array
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
	
	// Return return template cache
	public function show() {
		$searchpattern = "/%%(base_content)%%/si";
		$this->_content = preg_replace($searchpattern, $this->_templateContent, $this->_content);
		
		$this->removeEmptyTags();
		return $this->_content;
	} 
	
	// displays a welcome message for the current user
	public function showWelcome($currentUser) {
		$welcome = 'Hallo ' . $currentUser['firstname'] . ' ' . $currentUser['lastname'] . ' <a href="logout.php">Logout</a>';
		$data = array('welcome_message' => $welcome);
		$this->data($data);
	}
	
	// strips placerholder that didn't get used
	private function removeEmptyTags() {
		$this->_content = preg_replace('~\%%\s*(.+?)\s*\%%~', '', $this->_content);
	}
}

?>