<?php
$config=Array();

$local=(strstr($_SERVER["HTTP_HOST"],'localhost') ? 'localhost' : 'server');

/* DATA BASE */
$config['DB']=Array(
	'localhost'=>Array(
		'user'=>'root',
		'pass'=>'',
		'db'=>'players'
	),
	'server'=>Array(
		'user'=>'root',
		'pass'=>'',
		'db'=>'players'
	)
);
$config['DB']=$config['DB'][$local];
?>