<?php
include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$templater = $loader->templater;
$userHandler = $loader->userHandler;

if($userHandler->needsSetup()) { header("Location: setup.php"); }

$templater->loadTemplate('index.html');

if($userHandler->login()->isLoggedIn()) {
	$templater->showWelcome($userHandler->getCurrentUser());
}

echo $templater->show();

?>