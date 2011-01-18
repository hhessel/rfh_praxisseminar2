<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetupForTpl('kurse.html');

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

if($userHandler->isLoggedIn()) {
	$templater->loadTemplate('kurse.html');
	$templater->showWelcome($userHandler->getCurrentUser());
} else {
	$data = array ('error' => 'Du bist nicht eingeloggt<br\>', 'redirect' => 'kurse');
	$templater->loadTemplate('login.html')->data($data);
}

$kurse = $db->model('kurse')->select('*')->execute()->result;

if(count($kurse) == 0) {
	$kurs_uebersicht = "Keine Kurse vorhanden";
} else {
	while (list(, $kurs) = each($kurse)) {
		$kurs_uebersicht .= $kurs['kursname'];
	}
}

$templater->data(array("kurs_uebersicht" => $kurs_uebersicht));

echo $templater->show();

?>