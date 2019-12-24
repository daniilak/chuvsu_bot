<?php
class Question 
{
	private $id;
	private $text;
	private $is_answer;
	private $father_id;
	private $is_disabled_send;
	private $id_admin;
	private $isStartOrEndTestAnswer;
	private $additional_questions;
	private $is_save_all_string;
	private $next_level;
	function __construct($id)
	{
		$this->setId($id);
	}
	public function getId()
	{
		return $this->id;
	}
	public function setId($value)
	{
		$this->id = intval($value);
	}
	public function getText()
	{
		return $this->text;
	}
	public function setText($value)
	{
		$this->text = trim($value);
	}
	public function getIsAnswer()
	{
		return $this->is_answer;	
	}
	public function setIsAnswer($value)
	{
		$this->is_answer = intval($value);
	}
	public function getFatherId()
	{
		return $this->father_id;	
	}
	
	public function setFatherId($value)
	{
		$this->father_id = intval($value);
	}
	public function getIsDisabledSend()
	{
		return $this->is_disabled_send;
	}
	public function setIsDisabledSend($value)
	{
		$this->is_disabled_send = intval($value);
	}
	
	public function getIdAdmin()
	{
		return $this->id_admin;
	}
	public function setIdAdmin($value)
	{
		$this->id_admin = intval($value);
	}
	public function setIsStartOrEndTestAnswer($value)
	{
		$this->isStartOrEndTestAnswer = intval($value);
	}
	public function getIsStartOrEndTestAnswer()
	{
		return $this->isStartOrEndTestAnswer;	
	}
	public function setAdditionalQuestions($value)
	{
		$this->additional_questions = $value;
	}
	public function getAdditionalQuestions()
	{
		return $this->additional_questions;	
	}
	
	public function setIsSaveAllString($value)
	{
		$this->is_save_all_string = $value;
	}
	public function getIsSaveAllString()
	{
		return $this->is_save_all_string;	
	}
	public function setNextLevel($value)
	{
		$this->next_level = $value;
	}
	public function getNextLevel()
	{
		return $this->next_level;	
	}

	
	public function issetAdditionalQuestions()
	{
		$stmt = DataBase::query()->prepare("SELECT * FROM `additional_questions` WHERE `id_question` = ?");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) 
		{
			return 0;
		}
		$t = $stmt->fetchAll();
		$this->setAdditionalQuestions($t);
		
		return 1;
	}
	public function updateTestDataUser($id_test,$id_user,$mark)
	{
		$stmt = DataBase::query()->prepare("SELECT * FROM `test_datauser` WHERE `id_test` = ? AND `id_user` = ?");
		$stmt->bindValue(1,  $id_test, PDO::PARAM_INT);
		$stmt->bindValue(2,  $id_user, PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) 
		{
			$stmt = DataBase::query()->prepare("INSERT INTO `test_datauser` (`id_test`,`id_user`,`mark`) VALUES (?,?,?)");
			$stmt->bindValue(1,  $id_test, PDO::PARAM_INT);
			$stmt->bindValue(2,  $id_user, PDO::PARAM_INT);
			$stmt->bindValue(3,  $mark, PDO::PARAM_INT);
			$stmt->execute();
			return;
		}
		
		$t = $stmt->fetchAll();
		$mark = (($this->getIsStartOrEndTestAnswer() == 1 ) ? 0 : $t[0]['mark'] )  + $mark;
		$stmt = DataBase::query()->prepare("UPDATE `test_datauser` SET `mark` = ? WHERE `id_test` = ? AND `id_user` = ?");
		$stmt->bindValue(1,  $mark, PDO::PARAM_INT);
		$stmt->bindValue(2,  $id_test, PDO::PARAM_INT);
		$stmt->bindValue(3,  $id_user, PDO::PARAM_INT);
		$stmt->execute();
	}
	public function getAttachments()
	{
		$stmt = DataBase::query()->prepare("SELECT * FROM `attachments` WHERE `id_question` = ?");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) 
		{
			return 0; //??
		}
		
		return $stmt->fetchAll();
		
	}
	public function sendAdminMessage($id_user, $message)
	{
		requestVkAPI("messages.send", "user_id=".$this->getIdAdmin()."&message=".urlencode($message.PHP_EOL." Подробнее: https://vk.com/gim".$GLOBALS['_id_group']."?sel=".$id_user));
	}
	public function getIdAnswers()
	{
		$stmt = DataBase::query()->prepare(
			"SELECT * FROM `answers` WHERE `id_question` = ? ORDER BY `text` = 0, -`text` DESC, `text`");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) {
			$buttons = [];
			$buttons []= [[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=> $this->getFatherId()]),
					'label' => urlencode('Назад')
				],
				'color'=>'default',
				]];
			$q = json_encode([
				'one_time' => false,
				'buttons' => $buttons
			], JSON_UNESCAPED_UNICODE);
			return ($this->getIsSaveAllString() == 1) ? $q : '{"buttons":[],"one_time":true}' ;
		}
		$buttons = [];
		$index = 0;
		foreach ($stmt->fetchAll() as $key => $answer) {
			$buttons []= [[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>$answer['id_next_question'].'_'.$answer['id_additional_fields'].'_'.$answer['function_code']]),
					'label' => urlencode($answer['text'])
				],
				'color'=>'default',
			]];
		}
		$q = json_encode([
			'one_time' => false,
			'buttons' => $buttons
		], JSON_UNESCAPED_UNICODE);
		return $q;
		
	}
	
	public function getAllAddField()
	{
		$stmt = DataBase::query()->prepare(
			"SELECT * FROM `additional_fields`");
		$stmt->execute();
		return $stmt->fetchAll();
	}
	public function getAllAnswers()
	{
		$stmt = DataBase::query()->prepare(
			"SELECT * FROM `answers` WHERE `id_question` = ? ORDER BY `text` = 0, -`text` DESC, `text`");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) {
			return 0; 
		}
		return $stmt->fetchAll();
	}
	public function getAllQuestions()
	{
		$stmt = DataBase::query()->prepare("SELECT * FROM `questions` WHERE `id_group` = ? ORDER BY `ID`");
		$stmt->bindValue(1,  $GLOBALS['_id_group'], PDO::PARAM_INT);	
		$stmt->execute();
		return $stmt->fetchAll();
	}
	public function editText($text) {
		$stmt = DataBase::query()->prepare("UPDATE `questions` SET `text` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $text, PDO::PARAM_STR);
		$stmt->bindValue(2,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
	}
	public function editFatherQuestion($id) {
		$stmt = DataBase::query()->prepare("UPDATE `questions` SET `id_father_question` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $id, PDO::PARAM_STR);
		$stmt->bindValue(2,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
	}
	
	
	public function findById()
	{
		
		$stmt = DataBase::query()->prepare("SELECT * FROM `questions` WHERE `ID` = ? AND `id_group` = ? LIMIT 1");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->bindValue(2,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() == 0) 
		{
			return 0; //??
		}
		$t = $stmt->fetchAll();
		
		$this->setText($t[0]['text']);
		$allAnswers = $this->getAllAnswers();
		$this->setIsAnswer( (is_array($allAnswers)) ? 1 : 0 );
		$this->setFatherId($t[0]['id_father_question']);
		$this->setIsDisabledSend($t[0]['is_disabled_send']);
		$this->setIdAdmin($t[0]['id_admin']);
		$this->setIsStartOrEndTestAnswer($t[0]['is_start_or_end_test_answer']);
		$temp = explode('_',$t[0]['is_save_all_string']);
		$this->setIsSaveAllString($temp[0]);
		$this->setNextLevel($temp[1]);
		
		
		unset($t);
	}
	public function findIsAnswer()
	{
		
		$stmt = DataBase::query()->prepare("SELECT * FROM `answers` WHERE `ID` = ? LIMIT 1");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt->rowCount() == 0) 
		{
			return 0; //??
		}
		$t = $stmt->fetchAll();
		
		$this->setText($t[0]['text']);
		$allAnswers = $this->getAllAnswers();
		$this->setIsAnswer( (is_array($allAnswers)) ? 1 : 0 );
		$this->setFatherId($t[0]['id_father_question']);
		$this->setIsDisabledSend($t[0]['is_disabled_send']);
		$this->setIdAdmin($t[0]['id_admin']);
		$this->setIsStartOrEndTestAnswer($t[0]['is_start_or_end_test_answer']);
		
		unset($t);
	}
	public function add($question_add, $id) {
		$stmt = DataBase::query()->prepare("INSERT INTO `questions` (`text`,`id_father_question`,`id_group`) VALUES (?,?,?)");
		$stmt->bindValue(1,  $question_add, PDO::PARAM_STR);
		$stmt->bindValue(2,  $id, PDO::PARAM_INT);
		$stmt->bindValue(3,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	public function delete() {
		$stmt = DataBase::query()->prepare("DELETE FROM`questions` WHERE `ID` = ?");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
	}
	
	public function getRoadMap() {
		$last = 143;
		$fatherID = $this->getFatherId(); 
		$str = '<li><a href="https://daniilak.ru/bot_tg/snochuvsu/editor/index.php?id=1">Привет..</a></li>';
		while ($last != $fatherID) {
		
			$stmt = DataBase::query()->prepare("SELECT `id_father_question`,`text`,`ID` FROM`questions` 
				WHERE `ID` = ? AND `id_group` = ?");
			$stmt->bindValue(1,  $fatherID, PDO::PARAM_INT);
			$stmt->bindValue(2,  $GLOBALS['_id_group'], PDO::PARAM_INT);	
			$stmt->execute();
			$t = $stmt->fetchAll();
			$fatherID = $t[0]['id_father_question'];
			$str .= '<li><a href="https://daniilak.ru/bot_tg/snochuvsu/editor/index.php?id='.$t[0]['ID'].'">'.mb_strimwidth($t[0]['text'], 0, 70, "...").'</a></li>';
		// var_dump("fet");
		// die();	
		}
		
		return $str.' <li class="active">'.mb_strimwidth($this->getText(), 0, 70, "...").'</li>';
	}
	
	public function editAnswer($val, $id) {
		$stmt = DataBase::query()->prepare("UPDATE `answers` SET `text` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $val, PDO::PARAM_STR);
		$stmt->bindValue(2,  $id, PDO::PARAM_INT);
		$stmt->execute();
	}
	
	public function editAnswerNextQuestion($idNext, $id) {
		$stmt = DataBase::query()->prepare("UPDATE `answers` SET `id_next_question` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $idNext, PDO::PARAM_STR);
		$stmt->bindValue(2,  $id, PDO::PARAM_INT);
		$stmt->execute();
	}

	public function addAnswer() {
		$stmt = DataBase::query()->prepare("INSERT INTO `answers` (`id_question`, `id_next_question`) VALUES (?,1)");
		$stmt->bindValue(1,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
	}
	
	public function deleteAnswer($id) {
		$stmt = DataBase::query()->prepare("DELETE FROM `answers` WHERE `ID` = ?");
		$stmt->bindValue(1,  $id, PDO::PARAM_INT);
		$stmt->execute();
	}

	public function editSelectFuncCode($val, $id) {
		$stmt = DataBase::query()->prepare("UPDATE `answers` SET `id_additional_fields` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $val, PDO::PARAM_STR);
		$stmt->bindValue(2,  $id, PDO::PARAM_INT);
		$stmt->execute();
	}
	public function editAnswerFunctionCode($val, $id) {
		$stmt = DataBase::query()->prepare("UPDATE `answers` SET `function_code` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $val, PDO::PARAM_STR);
		$stmt->bindValue(2,  $id, PDO::PARAM_INT);
		$stmt->execute();
	}
	
	public function editDisableButton($val) {
		var_dump($val.'_'.$this->getNextLevel());
		$stmt = DataBase::query()->prepare("UPDATE `questions` SET `is_save_all_string` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $val.'_'.$this->getNextLevel(), PDO::PARAM_STR);
		$stmt->bindValue(2,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
	}
	public function editNextQuestion($val) {
		var_dump($val.'_'.$this->getIsSaveAllString().'getIsSaveAllString');
		$stmt = DataBase::query()->prepare("UPDATE `questions` SET `is_save_all_string` = ? WHERE `ID` = ?");
		$stmt->bindValue(1,  $this->getIsSaveAllString().'_'.$val, PDO::PARAM_STR);
		$stmt->bindValue(2,  $this->getId(), PDO::PARAM_INT);
		$stmt->execute();
	}
	
}