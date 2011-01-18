<?php
include('config.php');
include('lib/loader.php');

$loader = new Loader();

$templater = $loader->load('templater')->loadBaseTemplate('tpl', 'base.html')->loadTemplate('index.html');
$db = $loader->load('db')->open($mysql_host, $mysql_user, $mysql_pw)->selectDB($mysql_db);

echo $templater->insertDataIntoTemplate($db->model('user')->select('*')->where('id', '1')->execute()->result)->show();

?>