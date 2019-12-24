<?php
require_once ('session.php');
require_once ('../config.php');

require_once ('../obj/TemplateEngine.php');
require_once ('../obj/AdditionalFields.php');
$additionalFields = new AdditionalFields();

$template = new TemplateEngine('datauser.tpl');
if (isset($_POST['idListField'])) {
	$listUser = '';
	$listUser = '<tr><th>№</th>';
		for ($i = 0; $i <= count($_POST['idListField']); $i++) {
			 
			 $listUser .= '<th onclick="sortTable('.$i.')">Name</th>';
		}
		$listUser .= '<th>Ссылка</th></tr>';
		$index = 1;
	foreach ($additionalFields->getUsers($_POST['idListField']) as $key => $q) {
		
		$str = '';
		foreach ($_POST['idListField'] as $t) {
			$str .= '<td>'. ((isset($q[$t])) ? $q[$t] : ' ') .'</td>';
		}
		$listUser .= '<tr><td>'. $index++ .'</td><td>'.$key.'</td>'.$str.'<td><a target="_blank" href="https://vk.com/gim47825810?sel='.$key.'">Ответить</a></td></tr>';
	}
	echo json_encode([options => $listUser]);

	die();
}
if (isset($_POST['desc']) && isset($_POST['name'])) { 
	if (!$additionalFields->issetName($_POST['name'])) {
		echo json_encode([msg => 'Данное поле уже есть']);
		die();
	}
	$additionalFields->add($_POST['name'], $_POST['desc']);
	echo json_encode([msg => 'ok']);
	die();
}
if (isset($_POST['listFieldDelete'])) { 
	$additionalFields->delete($_POST['listFieldDelete']);
	echo json_encode([msg => 'ok']);
	die();
}

$listFields = '';
foreach ($additionalFields->getAllFields() as $q) {
	$listFields .= '<option value="'.$q['ID'].'">'.$q['description'].'</option>';
}


$listFields2 = '';
foreach ($additionalFields->getAllFields() as $q) {
	// 
	// $listFields2 .='<div class="checkbox"><label><input type="checkbox" value="'.$q['ID'].'">'.$q['description'].'</label></div>';
	// "<input type='checkbox' value='".$q['ID']."' /> ""<br>";
	$listFields2 .= '<option value="'.$q['ID'].'">'.$q['description'].'</option>';
}	

$template->templateSetVar('listField',$listFields);
$template->templateSetVar('listFields2',$listFields2);
$template->templateCompile();
$template->templateDisplay();
