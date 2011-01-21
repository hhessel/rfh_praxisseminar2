<?php

include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$templater = $loader->templater;
$userHandler = $loader->userHandler;

$userHandler->logout();

$templater->loadTemplate('login.html');

echo $templater->show();

?>