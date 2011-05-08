<?php


class CourseHandler {
	public $db;
	public $userHandler;
	
	public function __construct($db, $userHandler) {
		$this->db = $db;
		$this->userHandler = $userHandler;
	}
	
	public function getAllCourses() {
		return $this->db->model('kurse')->select('*')->orderBy('semester')->execute()->result;
	}
	
	public function deleteCourse($courseId) {
		if($this->userHandler->isAdmin()) {
			$this->db->model('kurse')
				->delete()
				->where('id', $courseId)
				->execute();
		}
	}
	
	public function insertCourse($kursname, $semester) {
		if($this->userHandler->isAdmin()) {
			$this->db->model('kurse')
				->insert(array(
					'kursname' => $kursname,
					'semester' => $semester
				))
				->execute();
			}
	}
}

?>