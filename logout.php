<?php

include('config.php');
include('lib/loader.php');

$loader = new Loader();

$templater = $loader->load('templater')->loadBaseTemplate('tpl', 'base.html');
$db = $loader->load('db')->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);

$userHandler = $loader->load('userHandler')->setDB($db);

$userHandler->logout();

$templater->loadTemplate('index.html');

echo $templater->show();

?>