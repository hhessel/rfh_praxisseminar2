<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

$templater->loadTemplate('register.html');

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
	if($_POST['password'] != $_POST['confirm_password']) {
		$data = array ('error' => 'Passwörter stimmen nicht überein', 'redirect' => 'kurse');
		$templater->data($data);
	} else {
		if($userHandler->register($_POST['username'],$_POST['password'], $_POST['firstname'], $_POST['lastname'])->isLoggedIn()) {
			header("Location: kurse.php");
			break;
		} else {
			$data = array ('error' => 'Username vergeben');
			$templater->data($data);
		}
	}
}

echo $templater->show();

?>