<?php

class Exporter {

	private $db;

	public function __construct($dblink) {
		$this->db = $dblink;
	}

	public function exportCoursesToXML() {
	
		$imp = new DOMImplementation;
		$dtd = $imp->createDocumentType('kurse', '', 'http://dl.dropbox.com/u/357576/saves/dtd/course.dtd');
		$doc = $imp->createDocument('', '', $dtd);
		
		$doc->formatOutput = true;
		$doc->encoding = "utf-8";
		$doc->version = "1.0";
		
		$pi = $doc->createProcessingInstruction("xml-stylesheet","type=\"text/css\" href=\"http://dl.dropbox.com/u/357576/saves/dtd/course.css\"");
		$doc->appendChild($pi);
	
		$r = $doc->createElement("kurse");
		$doc->appendChild($r);
		
		$courses = $this->db->model('kurse')->select()->execute()->result;
		
		foreach($courses as $course) {
			$k = $doc->createElement("kurs");
			
			$course_id = $doc->createElement("kursId");
			$course_id->appendChild($doc->createTextNode($course['id']));
			
			$k->appendChild($course_id); 
			
			$course_name = $doc->createElement("kursname");
			$course_name->appendChild($doc->createTextNode($course['kursname']));
			
			$k->appendChild($course_name); 
			
			$semester = $doc->createElement("semester");
			$semester->appendChild($doc->createTextNode($course['semester']));
			
			$k->appendChild($semester); 
			
			$r->appendChild($k);
		}
		
		return $doc->saveXML();
	}
	
	public function exportCoursesToJSON() {
		return json_encode(array("kurse"=>$courses = $this->db->model('kurse')->select()->execute()->result));
	}
	
}

?>