<?php
include('config.php');
include('lib/loader.php');

$loader = new Loader();

$templater = $loader->load('templater')->loadBaseTemplate('tpl', 'base.html')->loadTemplate('index.html');
$db = $loader->load('db')->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);

$userHandler = $loader->load('userHandler')->setDB($db);

if($userHandler->isLoggedIn()) {
	$templater->showWelcome($userHandler->getCurrentUser());
}

echo $templater->data($db->model('user')->count()->execute()->result)->show();

?>