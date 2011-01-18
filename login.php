<?php

include('config.php');
include('lib/loader.php');

$loader = new Loader();

$templater = $loader->load('templater')->loadBaseTemplate('tpl', 'base.html');
$db = $loader->load('db')->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);

$userHandler = $loader->load('userHandler')->setDB($db);


if(isset($_POST['username']) && isset($_POST['username'])) {
	if($userHandler->login($_POST['username'], $_POST['password'])->isLoggedIn()) {
		switch($_POST['redirect']) {
			case 'kurse':	
				$templater->loadTemplate('kurse.html');
			default:
				$templater->loadTemplate('index.html');
		}
	} else {
		$data = array ('error' => 'Du bist nicht eingeloggt', 'redirect' => 'kurse');
		$templater->loadTemplate('login.html')->data($data);
	}
}

echo $templater->show();

?>