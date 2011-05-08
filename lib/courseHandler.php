<?php


class CourseHandler {
	public $db;
	public $model;
	public $userHandler;
	public $statelessCache = array();
	
	public function __construct($db, $userHandler) {
		$this->db = $db;
		$this->model = $this->db->model('kurse');
		$this->userHandler = $userHandler;
	}
	
	public function getAllCourses() {
		return $this->model->select()->orderBy('semester')->execute()->result;
	}
	
	public function getCourseById($courseId) {
		if (!array_key_exists($courseId, $this->statelessCache)) {
			$this->statelessCache[$courseId] = $this->model->select()->where('id', $courseId)->execute()->result[0];
		}
		return $this->statelessCache[$courseId];
	}
	
	public function generateCourseNews() {
		$latest_attachments = "";
		$latest = $this->db->model('attachment')->select()->limit(5)->execute()->result;
		if(count($latest) == 0) {
			$latest_attachments = "Keine Neuigkeiten vorhanden"; 
		} else {
			while (list(, $attachment) = each($latest)) {
				$course = $this->getCourseById($attachment["kursId"]);
				$user = $this->userHandler->getUserById($attachment['fromUser']);
				$latest_attachments .= '<b>' . $user['firstname'] . ' ' . $user['lastname'] . '</b> hat eine Notiz für den Kurs <b>' . $course['kursname'] . '</b> hochgeladen. <br/>';
			}
		}
		
		return $latest_attachments;
	}
	
	public function deleteCourse($courseId) {
		if($this->userHandler->isAdmin()) {
			$this->model
				->delete()
				->where('id', $courseId)
				->execute();
		}
	}
	
	public function insertCourse($kursname, $semester) {
		if($this->userHandler->isAdmin()) {
			$this->model
				->insert(array(
					'kursname' => $kursname,
					'semester' => $semester
				))
				->execute();
			}
	}
}

?>