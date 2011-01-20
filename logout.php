<?php

include('lib/loader.php');

$loader = Loader::loadBasicSetup();

$templater = $loader->templater;
$userHandler = $loader->userHandler;

$userHandler->logout();

$templater->loadTemplate('index.html');

echo $templater->show();

?>