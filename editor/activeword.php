<?php
require_once ('../obj/ActiveWord.php');
require_once ('session.php');
require_once ('../config.php');
require_once ('../obj/TemplateEngine.php');
require_once ('../obj/AdditionalFields.php');
$activeWord = new ActiveWord();
$template = new TemplateEngine('activeword.tpl');

if (isset($_POST['listWordDelete'])) { 
	$activeWord->delete($_POST['listWordDelete']);
	echo json_encode([msg => 'ok']);
	die();
}

if (isset($_POST['name']) && isset($_POST['allq'])) { 
	if ($activeWord->find($_POST['name'])) {
		echo json_encode([msg => 'Слово уже есть']);
		die();
	}
	$activeWord->add($_POST['name'], $_POST['allq']);
	echo json_encode([msg => 'ok']);
	die();
}


$tableWord = '';
foreach ($activeWord->getAll() as $q) {
	$q['text_q'] = str_replace(PHP_EOL, "<br>", $q['text_q']);
	$tableWord .= '<tr><td>'.$q['ID'].'</td><td>'.$q['text'].'</td><td>'.$q['text_q'].'</td>
	<td><a href="#" class="btn btn-danger" id="delete" data-id="'.$q['ID'].'" role="button">Удалить</a></td>
	<td><a href="./index.php?id='.$q['id_question'].'" class="btn btn-info" role="button">Настройка вопроса</a></td>
	</tr>';
}

$allQ = '';
foreach ($activeWord->getAllQuestion() as $q) {
	$allQ .= '<option value="'.$q['ID'].'">'.mb_strimwidth($q['text'], 0, 70, "...").'</option>';
}
	
$template->templateSetVar('tableWord',$tableWord);
$template->templateSetVar('allq',$allQ);
$template->templateCompile();
$template->templateDisplay();
