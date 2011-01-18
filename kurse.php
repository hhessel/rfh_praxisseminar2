<?php

include('config.php');
include('lib/loader.php');

$loader = new Loader();

$db = $loader->load('db');
$templater = $loader->load('templater');
$userHandler = $loader->load('userHandler');
$userHandler->setDB($db);

$templater->loadBaseTemplate('tpl', 'base.html');
$templater->loadTemplate('tpl', 'kurse.html');

$db->open($mysql_host, $mysql_user, $mysql_pw);
$db->selectDB($mysql_db);

$userHandler->login('test', 'test');

/*
$execute = $db->execute($db->prepareQuery('SELECT', 'User', '*'));
$templater->insertDataIntoTemplate($execute->result);
*/

echo $templater->outputTemplate();


?>