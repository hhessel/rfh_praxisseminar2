<?php

include('lib/loader.php');

$loader = Loader::loadBasicSetupForTpl('kurse.html');

$db = $loader->db;
$templater = $loader->templater;
$userHandler = $loader->userHandler;


$userHandler->logout();

$templater->loadTemplate('index.html');

echo $templater->show();

?>