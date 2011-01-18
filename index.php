<?php
include('config.php');
include('lib/loader.php');

$loader = new Loader();

$templater = $loader->load('templater')->loadBaseTemplate('tpl', 'base.html')->loadTemplate('index.html');
$db = $loader->load('db')->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);

echo $templater->data($db->model('user')->count()->execute()->result)->show();

?>