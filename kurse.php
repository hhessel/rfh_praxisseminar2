<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

$courseHandler = $loader->courseHandler;

$templater->loadTemplate('kurse.html');

if($userHandler->login()->isLoggedIn()) {
	$templater->showWelcome($userHandler->getCurrentUser());
} else {
	$data = array ('error' => 'Du bist nicht eingeloggt<br\>', 'redirect' => 'kurse');
	$templater->loadTemplate('login.html')->data($data);
}

if(isset($_GET['export'])) {
	$exporter = new Exporter($db);
	if($_GET['export'] == "xml") {
		header("Content-Type: text/xml");
		header('Content-Disposition: filename="kurse.xml"');
		echo $exporter->exportCoursesToXML();
		exit();
	} else {
		header("Content-Type: text/javascript");
		header('Content-Disposition: filename="kurse.txt"');
		echo $exporter->exportCoursesToJSON();
		exit();
	}

}

if(isset($_GET['delete_course'])) {
	$courseHandler->deleteCourse((int)$_GET['delete_course']);
	if(!isset($_GET['ajax']))  {
		header("Location: kurse.php");
	}
	exit();
}


if(isset($_POST['kursname'])) {
	$courseHandler->insertCourse($_POST['kursname'], $_POST['semester']);
	if(!isset($_GET['ajax']))  {
		header("Location: kurse.php");
	}
	exit();
}


$kurse = $courseHandler->getAllCourses();

if(count($kurse) == 0) {
	$kurse_uebersicht = "Keine Kurse vorhanden";
} else {
		$kurse_uebersicht = '<table style="width:700px;"><tr><td style="width:30px;">Sem.</td><td style="width:300px;">Kursname</td><td style="width:100px;"></td><td width="*"></td></tr>';
		
		while (list(, $kurs) = each($kurse)) {
			$attachment_count = $db->model('attachment')->count()->where('kursId', (int)$kurs['id'])->execute()->result;
			$kurs_uebersicht = '<tr id="tr_' .$kurs['id'] .'">
									<td style="text-align:right;">' .  $kurs['semester'] . '</td>
									<td>' . $kurs['kursname'] . '</td>
									<td style="vertical-align:middle;">' . $attachment_count . '
										<a href="kurse_view.php?view='. $kurs['id'] . '"><img src="images/note.png" style="vertical-align:top;"></a>
										<a href="kurse_view.php?add='. $kurs['id'] . '"><img src="images/note_add.png" style="vertical-align:top;"></a>
									</td>';
									
			$kurs_uebersicht .= $userHandler->isAdmin() ? '<td><a href="#" onclick="deleteCourse(' . $kurs['id'] . ')">Delete</a></td>' : '<td></td>';
			
			$kurs_uebersicht .= '</tr>';				
			$kurse_uebersicht .= $kurs_uebersicht;
		}
		
		$kurse_uebersicht .= '</table>';
		$kurse_uebersicht .= '<br/> Export to <a href="kurse.php?export=xml">XML</a> / <a href="kurse.php?export=json">JSON</a>';
}	

if($userHandler->isAdmin()) {
	$templater->attachTemplate('kurse_add.html');
}

$templater->data(array("kurs_uebersicht" => $kurse_uebersicht));

echo $templater->show();

?>