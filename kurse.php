<?php

include('config.php');
include('lib/loader.php');

$loader = new Loader();

$templater = $loader->load('templater')->loadBaseTemplate('tpl', 'base.html');
$db = $loader->load('db')->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);

$userHandler = $loader->load('userHandler')->setDB($db);

if($userHandler->isLoggedIn()) {
	$templater->loadTemplate('kurse.html');
	$templater->showWelcome($userHandler->getCurrentUser());
} else {
	$data = array ('error' => 'Du bist nicht eingeloggt<br\>', 'redirect' => 'kurse');
	$templater->loadTemplate('login.html')->data($data);
}

echo $templater->show();

?>