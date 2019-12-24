<?php
class AdditionalFields {
	public function getAllFields() {
		$stmt = DataBase::query()->prepare("SELECT * FROM `additional_fields` WHERE `id_group` = ?");
		$stmt->bindValue(1,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
		$stmt->execute();
		if ($stmt->rowCount() == 0) {
			return 0; 
		}
		return $stmt->fetchAll();
	}
	public function getUsers($idArray) {
		$tmpArr = [];
		foreach ($idArray as $id) {
			$stmt = DataBase::query()->prepare("SELECT `value`,`id_user` FROM `additional_values` 
			WHERE `id_field` = ? AND `id_group` = ?");
			$stmt->bindValue(1,  $id, PDO::PARAM_INT);
			$stmt->bindValue(2,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
			$stmt->execute();
			if ($stmt->rowCount() != 0) {
				foreach ($stmt->fetchAll() as $s) {
					if (!isset($tmpArr[$s['id_user']])) {
						$tmpArr[$s['id_user']] = [];
						$tmpArr[$s['id_user']][$id]= $s['value'];
					} else {
						$tmpArr[$s['id_user']][$id]= $s['value'];
					}
				}
			}
		}
		// var_dump($tmpArr);
		return $tmpArr;
	}
	
	public function issetName($name) {
		$stmt = DataBase::query()->prepare("SELECT * FROM `additional_fields` WHERE `name` = ? AND `id_group` = ?");
		$stmt->bindValue(1,  trim($name), PDO::PARAM_STR);
		$stmt->bindValue(2,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
		$stmt->execute();
		return ($stmt->rowCount() == 0) ? true : false ;
	}
	
	public function add($name, $desc) {
		$stmt = DataBase::query()->prepare("INSERT INTO `additional_fields`(`name`, `description`,`id_group`) VALUES (?,?,?)");
		$stmt->bindValue(1,  trim($name), PDO::PARAM_STR);
		$stmt->bindValue(2,  trim($desc), PDO::PARAM_STR);
		$stmt->bindValue(3,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
		$stmt->execute();
	}
	public function delete($id) {
		$stmt = DataBase::query()->prepare("DELETE FROM `additional_fields` WHERE `ID` = ?");
		$stmt->bindValue(1,  intval($id), PDO::PARAM_INT);
		$stmt->execute();
		$stmt = DataBase::query()->prepare("DELETE FROM `additional_values` WHERE `id_field` = ?");
		$stmt->bindValue(1,  intval($id), PDO::PARAM_INT);
		$stmt->execute();
	}
	
}