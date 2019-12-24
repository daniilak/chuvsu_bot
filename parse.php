<?php
ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);
ini_set('max_execution_time', '600');
require_once('config.php');

// $stmt = DataBase::query()->prepare(
// 	"SELECT *  
// 	FROM `additional_values` 
// 	WHERE `id_field` = '19'"); 

$stmt = DataBase::query()->prepare(
	"SELECT *  
	FROM `additional_values` 
	WHERE `id_field` = '36'"); 


$stmt->execute();

foreach ($stmt->fetchAll() as $u) {
	echo $u['id_user']."<br>".PHP_EOL;
}




