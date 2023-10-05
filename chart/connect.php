<?php
	define("DB_HOSTNAME","localhost");
	define("DB_DATABASE","dev");
	define("DB_USERNAME","root");
	define("DB_PASSWORD","lbcmsuccess");
	
	$mysqli=new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD);
	$mysqli->select_db(DB_DATABASE);
	$mysqli->query("SET NAMES utf8;");
?>