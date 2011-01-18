<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetupForTpl('index.html');

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;

if($userHandler->isLoggedIn()) {
	$templater->showWelcome($userHandler->getCurrentUser());
}

echo $templater->show();

?>