<?php
class ActiveWord 
{
	static public function declension_word($number, $word) {
	    $ar= array (2, 0, 1, 1, 1, 2);
	    return $word[ ($number%100 > 4 && $number%100 < 20) ? 2 : $ar[min($number%10, 5)] ];
	}
	static public function find($word)	{
		$word = strtolower($word);
		// var_dump($word);
		$stmt = DataBase::query()->prepare("SELECT * FROM `active_words` WHERE `text` LIKE ? AND `id_group` = ? LIMIT 1");
		$stmt->bindValue(1,  $word, PDO::PARAM_STR);
		$stmt->bindValue(2,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		// var_dump($GLOBALS['_id_group']);
		$stmt->execute();
		return ($stmt->rowCount() == 0) ? 0 : $stmt->fetchAll();
	}
	static public function findAnswer($word,$id, $payload = false) {
		$word = strtolower($word);
		$stmt = DataBase::query()->prepare("SELECT * FROM `answers` WHERE `id_next_question` LIKE ? AND `id_question` = ? LIMIT 1");
		$stmt->bindValue(1,  $word, PDO::PARAM_STR);
		$stmt->bindValue(2,  $id, PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() != 0) {
			return $stmt->fetchAll();
		} else {
			return (empty($payload)) ? 0 : $word;
		}
	}
	static public function getResultTest($userId,$testId) {
		$stmt = DataBase::query()->prepare("
			SELECT * 
				FROM `tests` 
			INNER JOIN `test_datauser` 
				ON `tests`.`ID` = `test_datauser`.`id_test` 
			WHERE `tests`.`ID` = ? AND `test_datauser`.`id_user` = ? 
			LIMIT 1");
		$stmt->bindValue(1,  $testId, PDO::PARAM_INT);
		$stmt->bindValue(2,  $userId, PDO::PARAM_INT);
		$stmt->execute();
		return ($stmt->rowCount() == 0) ? 0 : $stmt->fetchAll();
	}
	
	public function getAllQuestion() {
		$stmt = DataBase::query()->prepare(
			"SELECT `ID`, `text`
			FROM `questions` WHERE `questions`.`id_group` = ?
			");
			$stmt->bindValue(1,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function getAll() {
		$stmt = DataBase::query()->prepare(
			"SELECT `active_words`.`ID`, `active_words`.`text`, `active_words`.`id_question`, `questions`.`text` as `text_q` 
			FROM `active_words` 
			INNER JOIN `questions` ON `questions`.`ID` = `active_words`.`id_question`
			WHERE `questions`.`id_group` = ?
			");
			$stmt->bindValue(1,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function delete($id) {
		$stmt = DataBase::query()->prepare("DELETE FROM `active_words` WHERE `ID` = ?");
		$stmt->bindValue(1,  intval($id), PDO::PARAM_INT);
		$stmt->execute();
	}
	public function add($word, $id) {
		$word = mb_strtolower($word);
		$stmt = DataBase::query()->prepare("INSERT INTO `active_words` (`text`,`id_question`,`id_group`)  VALUES (?,?,?)");
		$stmt->bindValue(1,  $word, PDO::PARAM_STR);
		$stmt->bindValue(2,  intval($id), PDO::PARAM_INT);
			$stmt->bindValue(3,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		$stmt->execute();
	}
}