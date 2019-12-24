<?php
require_once ('session.php');
require_once ('../config.php');
require_once ('../obj/AdditionalFields.php');

if (isset($_POST['text_question']) && isset($_POST['question_id'])) {
	$id = intval($_POST['question_id']);
	$question = new Question($id);
	$question->editText(trim(strip_tags($_POST['text_question'])));
	echo (json_encode([code => 'ok']));
	die();
}


if (isset($_POST['answer_add'])) {
	$id = intval($_POST['answer_add']);
	$question = new Question($id);
	$question->addAnswer();
	echo (json_encode([code => 'ok']));
	die();
}  

if (isset($_POST['textarea_answer_val']) && isset($_POST['textarea_answer_id'])) {
	$id = intval($_POST['textarea_answer_id']);
	$question = new Question(1);
	$question->editAnswer(trim(strip_tags($_POST['textarea_answer_val'])),$id);
	echo (json_encode([code => 'ok']));
	die();
}

if (isset($_POST['function_code_val']) && isset($_POST['function_code_id'])) {
	$id = intval($_POST['function_code_id']);
	$question = new Question(1);
	$question->editAnswerFunctionCode(trim(strip_tags($_POST['function_code_val'])),$id);
	echo (json_encode([code => 'ok']));
	die();
}


if (isset($_POST['select_function_code_val']) && isset($_POST['select_function_code_id'])) {
	$id = intval($_POST['select_function_code_id']);
	$val = intval($_POST['select_function_code_val']);
	$question = new Question(1);
	$question->editSelectFuncCode($val, $id);
	echo (json_encode([code => 'ok']));
	die();
}

if (isset($_POST['select_answer']) && isset($_POST['id_answer'])) {
	$id = intval($_POST['id_answer']);
	$idNext = intval($_POST['select_answer']);
	$question = new Question(1);
	$question->editAnswerNextQuestion($idNext, $id);
	echo (json_encode([code => 'ok']));
	die();
}


if (isset($_POST['id_father_question']) && isset($_POST['question_id'])) {
	$id = intval($_POST['question_id']);
	$question = new Question($id);
	$question->editFatherQuestion(trim(strip_tags($_POST['id_father_question'])));
	echo (json_encode([code => 'ok']));
	die();
}

if (isset($_POST['question_add']) && isset($_POST['id_father_question_add'])) {
	$question = new Question(1);
	$question->add($_POST['question_add'], $_POST['id_father_question_add']);
	echo (json_encode([code => 'ok']));
	die();
}

if (isset($_POST['id_delete_answer'])) {
	$question = new Question(1);
	$question->deleteAnswer($_POST['id_delete_answer']);
	echo (json_encode([code => 'ok']));
	die();
}

if (isset($_POST['deleteQuestion'])) {
	$question = new Question($_POST['deleteQuestion']);
	$question->delete();
	echo (json_encode([code => 'ok']));
	die();
}


if (isset($_POST['is_disable_button']) && isset($_POST['id_question'])) {
	$question = new Question($_POST['id_question']);
	$question->findById();
	$question->editDisableButton($_POST['is_disable_button']);
	echo (json_encode([code => 'ok']));
	die();
}
if (isset($_POST['is_next_question']) && isset($_POST['id_question'])) {
	$question = new Question($_POST['id_question']);
	$question->findById();
	$question->editNextQuestion($_POST['is_next_question']);
	echo (json_encode([code => 'ok']));
	die();
}


require_once ('../obj/TemplateEngine.php');

$template = new TemplateEngine('index.tpl');
$question = new Question((isset($_GET['id'])) ? $_GET['id'] : $GLOBALS['start_id']);

$question->findById();

$roadMap = '';
$breadcrumb = '';//$question->getRoadMap();
// var_dump("sdd");
// die();
$listAnswers = '';
$allAnswers = $question->getAllAnswers();
if (is_array($allAnswers)) {
	$index = 1;
	foreach ($allAnswers as $answer) {
		$allQuestionsEdit ='';
		$nextQuestion = $answer['id_next_question'];
		$idAddField = $answer['id_additional_fields'];
		foreach ($question->getAllQuestions() as $q) {
			$allQuestionsEdit .= '<option value="'.$q['ID'].'"'.(($q['ID'] == $answer['id_next_question']) ? 'selected="selected"' : '').'>'.mb_strimwidth($q['text'], 0, 70, "...").'</option>';
		}
		$allAddField = '<option value="0">...Тип данных....</option>';
		foreach ($question->getAllAddField() as $q) {
			$allAddField .= '<option value="'.$q['ID'].'"'.(($q['ID'] == $answer['id_additional_fields']) ? 'selected="selected"' : '').'>'.mb_strimwidth($q['description'], 0, 70, "...").'</option>';
		}
		
		$listAnswers .= '
		<div class="form-group row">
		<div class="col-md-3">
			<div class="form-group">
				<input id="textarea_answer" placeholder="Текст кнопки ответа:" class="form-control" data-id="'.$answer['ID'].'" value="'.$answer['text'].'"> 
			</div>
		</div>
		<div class="col-md-3">
			<div class="input-group">
            	<select class="form-control" id="select_answer" data-id="'.$answer['ID'].'">'.$allQuestionsEdit.'</select>
                    <span class="input-group-btn">
                    	<button type="button" id="get_next_answer" class="btn btn-info" data-next="'.$answer['id_next_question'].'" data-id="'.$answer['ID'].'">
					<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
            	</span>
            </div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<select class="form-control" id="select_function_code" data-id="'.$answer['ID'].'">'.$allAddField.'</select>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<input id="function_code" placeholder="Как сохранять:" class="form-control" data-id="'.$answer['ID'].'" value="'.$answer['function_code'].'"> 
			</div>
		</div>
		<div class="col-md-1">
	        <span id="get_delete_answer" class="btn btn-danger pull-right" data-id="'.$answer['ID'].'">
	        	<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
	        </span>
	    </div>
	    </div>
	    <hr>';
	}
}
$allQuestions = '';
$allQuestionsEdit ='';
$allQuestionsNext ='';
foreach ($question->getAllQuestions() as $q) {
	$allQuestions .= '<option value="'.$q['ID'].'"'.(($q['ID'] == $question->getFatherId()) ? 'selected="selected"' : '').'>'.mb_strimwidth($q['text'], 0, 70, "...").'</option>';
	$allQuestionsEdit .= '<option value="'.$q['ID'].'"'.(($q['ID'] == $question->getId()) ? 'selected="selected"' : '').'>'.mb_strimwidth($q['text'], 0, 70, "...").'</option>';
	$allQuestionsNext .= '<option value="'.$q['ID'].'"'.(($q['ID'] == $question->getNextLevel()) ? 'selected="selected"' : '').'>'.mb_strimwidth($q['text'], 0, 70, "...").'</option>';
}

$additionalFields = new AdditionalFields();
$is_disable_button ='';
foreach ($additionalFields->getAllFields() as $q) {
	$is_disable_button .= '<option value="'.$q['ID'].'"'.(($q['ID'] == $question->getIsSaveAllString()) ? 'selected="selected"' : '').'>'.$q['description'].'</option>';
}

$template->templateSetVar('id_question',$question->getId());
$template->templateSetVar('is_save_all_string',$is_disable_button );
$template->templateSetVar('allQuestionsNext',$allQuestionsNext );


$questionText = str_replace(PHP_EOL, "<br>", $question->getText());
$template->templateSetVar('question', $questionText);
$template->templateSetVar('questionTextarea',  $question->getText());

$template->templateSetVar('allQuestionsEdit',$allQuestionsEdit);
$template->templateSetVar('allQuestions',$allQuestions);
$template->templateSetVar('breadcrumb',$breadcrumb);

$template->templateSetVar('listAnswers',$listAnswers);
$template->templateCompile();
$template->templateDisplay();
