<?php

require_once("../config.php");
require_once("DataBase.php");
if (!isset($_GET['type'])) { die(); }
$type = $_GET['type'];
switch ($type) {
	case 'question':
		$stmt = DataBase::query()->prepare("SELECT `ID`,`text`,`id_father_question`  FROM `questions` LIMIT 5");
		break;
	case 'user':
		$stmt = DataBase::query()->prepare("SELECT `ID`, `id_vk`, `level`  FROM `user`");
		break;
	case 'attachment':
		$stmt = DataBase::query()->prepare("SELECT *  FROM `attachments`");
		break;
	case 'answer':
		$stmt = DataBase::query()->prepare("SELECT `ID`,`id_question`, `text`, `id_next_question`   FROM `answers`");
		break;
}
$stmt->execute();
echo json_encode($stmt->fetchAll());
die();