<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

if($userHandler->login()->isLoggedIn()) {
	$templater->loadTemplate('kurse.html');
	$templater->showWelcome($userHandler->getCurrentUser());
} else {
	$data = array ('error' => 'Du bist nicht eingeloggt<br\>', 'redirect' => 'kurse');
	$templater->loadTemplate('login.html')->data($data);
}

$kurse = $db->model('kurse')->select('*')->orderBy('semester')->execute()->result;

if(count($kurse) == 0) {
	$kurs_uebersicht = "Keine Kurse vorhanden";
} else {
		$kurs_uebersicht = '<table style="width:500px;"><tr><td style="width:30px;">Sem.</td><td style="width:250px;">Kursname</td><td style="width:100px;"></td><td width="*"></td></tr>';
		
		while (list(, $kurs) = each($kurse)) {
			$attachment_count = $db->model('attachment')->count()->where('id', $kurs['id'])->execute()->result;
			$kurs_uebersicht .= '<tr>
									<td style="text-align:right;">' .  $kurs['semester'] . '</td>
									<td>' . $kurs['kursname'] . '</td>
									<td style="vertical-align:middle;">' . $attachment_count . ' <img src="images/note.png" style="vertical-align:top;"></td>
									<td></td>
								</tr>';
		}
		
		$kurs_uebersicht .= '</table>';
		
}	

$templater->data(array("kurs_uebersicht" => $kurs_uebersicht));

echo $templater->show();

?>