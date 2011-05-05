<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

$templater->loadTemplate('kurse.html');

if($userHandler->login()->isLoggedIn()) {
	$templater->showWelcome($userHandler->getCurrentUser());
} else {
	$data = array ('error' => 'Du bist nicht eingeloggt<br\>', 'redirect' => 'kurse');
	$templater->loadTemplate('login.html')->data($data);
}

if(isset($_GET['delete_course'])) {
	if($userHandler->isAdmin()) {
		$db->model('kurse')
			->delete()
			->where('id', (int)$_GET['delete_course'])
			->execute();
	}
	header("Location: kurse.php");
	exit();
}


if(isset($_POST['kursname'])) {
	if($userHandler->isAdmin()) {
		$db->model('kurse')
			->insert(array(
				'kursname' => $_POST['kursname'],
				'semester' => $_POST['semester']
			))
			->execute();
	}
	header("Location: kurse.php");
	exit();
}


$kurse = $db->model('kurse')->select('*')->orderBy('semester')->execute()->result;

if(count($kurse) == 0) {
	$kurse_uebersicht = "Keine Kurse vorhanden";
} else {
		$kurse_uebersicht = '<table style="width:700px;"><tr><td style="width:30px;">Sem.</td><td style="width:300px;">Kursname</td><td style="width:100px;"></td><td width="*"></td></tr>';
		
		while (list(, $kurs) = each($kurse)) {
			$attachment_count = $db->model('attachment')->count()->where('kursId', (int)$kurs['id'])->execute()->result;
			$kurs_uebersicht = '<tr>
									<td style="text-align:right;">' .  $kurs['semester'] . '</td>
									<td>' . $kurs['kursname'] . '</td>
									<td style="vertical-align:middle;">' . $attachment_count . '
										<a href="kurse_view.php?view='. $kurs['id'] . '"><img src="images/note.png" style="vertical-align:top;"></a>
										<a href="kurse_view.php?add='. $kurs['id'] . '"><img src="images/note_add.png" style="vertical-align:top;"></a>
									</td>';
									
			$kurs_uebersicht .= $userHandler->isAdmin() ? '<td><a href="kurse.php?delete_course=' . $kurs['id'] . '">  Delete</a></td>' : '<td></td>';
			
			$kurs_uebersicht .= '</tr>';				
			$kurse_uebersicht .= $kurs_uebersicht;
		}
		
		$kurse_uebersicht .= '</table>';

}	

if($userHandler->isAdmin()) {
	$templater->attachTemplate('kurse_add.html');
}

$templater->data(array("kurs_uebersicht" => $kurse_uebersicht));

echo $templater->show();

?>