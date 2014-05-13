<?php
$db = array(
	'connectionString' => 'mysql:host=HOSTNAME;dbname=DBNAME',
	'emulatePrepare' => true,
	'username' => 'root',
	'password' => 'root',
	'charset' => 'utf8',
	'tablePrefix' => '',
	);

$params = array(
	// this is used in contact page
	'adminEmail'=>'webmaster@example.com',
	'salt'=>'$2a$10$'
	);
?>