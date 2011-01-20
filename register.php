<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

$templater->loadTemplate('register.html');

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
	if($_POST['password'] != $_POST['confirm_password']) {
		$data = array ('error' => 'Passw�rter stimmen nicht �berein', 'redirect' => 'kurse');
		$templater->loadTemplate('register.html')->data($data);
	} else {
		$username = $_POST['username'];
		$password = $_POST['password'];
		if($userHandler->register($username,$password)->isLoggedIn()) {
			header("Location: kurse.php");
			break;
		} else {
			$data = array ('error' => 'Username vergeben');
			$templater->loadTemplate('register.html')->data($data);
		}
	}
}

echo $templater->show();

?>