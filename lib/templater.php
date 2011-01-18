<?php 

class Templater {
	private $_content;
	private $_baseDir;
	
	public function loadBaseTemplate($templateBaseDir, $templateFileName) {
		$this->_content = file_get_contents($templateBaseDir . '/' . $templateFileName);
		$this->_baseDir = $templateBaseDir;
		return $this;
	}
	
	public function loadTemplate($templateFileName) {
		$templateContent = file_get_contents($this->_baseDir . '/' . $templateFileName);
		
		$searchpattern = "/%%(base_content)%%/si";
		$this->_content = preg_replace($searchpattern, $templateContent, $this->_content);
		return $this;
	}
	
	public function data($valuearray) {
		if(is_array($valuearray)) {
			foreach($valuearray as $key => $value) {
				$searchpattern = "/%%(".strtoupper($key).")%%/si";

				// Gefundene Platzhalter mit Werten aus $wertearray ersetzen
				$this->_content = preg_replace($searchpattern, $value, $this->_content);
			}
			// Nicht ersetzte Platzhalter aus Template entfernen
			$this->_content = preg_replace("/((%%)(.+?)(%%))/si", '', $this->_content);
		}
		return $this;
	}
	
	public function show() {
		return $this->_content;
	}
}

?>