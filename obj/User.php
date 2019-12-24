<?php

class User 
{
	private $user_id;
	private $user_data;
	private $user_level;
	private $dates = ['', '8:20-9:40', '09:55-11:15', '11:30-12:50', '13:20-14:40', '14:55-16:15', '16:30-17:50', '18:05-19:25', '19:40-21:00'];
	
    private $kfu = ['', '8.30-10.00', '10.10-11.40', '11.50-13.20', '14.00-15.30', '15.40-17.10', '17.20-18.50', '19.00-20.30', '?)'];
    
    
	function __construct($user_id)
	{
		$this->setUserId($user_id);
	}
	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
	}
	public function getUserId()
	{
		return $this->user_id;
	}
	public function setLevel($val)
	{
		$this->user_level = $val;
	}
	public function getLevel()
	{
		return $this->user_level;
	}
	public function getUserDataByVkAPI()
	{
		//requestVkAPI("user.get", $params);
		
	}
	public function insertValueToJson($id,$value)
	{
		if (is_array($value)) {
			$value = json_encode($value);
		}
		// $stmt = DataBase::query()->prepare("SELECT *  FROM `additional_fields` WHERE `ID` = ?");
		// $stmt->bindValue(1,  $id, PDO::PARAM_STR);
		// $stmt->execute();
		// $a = $stmt->fetchAll();
		// $is_save_all_string = $a[0]['is_save_all_string'];
		// $value = ( $is_save_all_string != 0 ) ? $message : $value;
		$stmt = DataBase::query()->prepare("SELECT *  FROM `additional_values` WHERE `id_field` = ? AND `id_user` = ?");
		$stmt->bindValue(1,  $id, PDO::PARAM_INT);
		$stmt->bindValue(2,  $this->getUserId(), PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) {
			$stmt = DataBase::query()->prepare("INSERT INTO `additional_values`  (`id_user`,`id_field`,`value`) VALUES (?,?,?)"); 
			$stmt->bindValue(1,  $this->getUserId(), PDO::PARAM_INT);
			$stmt->bindValue(2,  $id, PDO::PARAM_INT);
			$stmt->bindValue(3,  $value, PDO::PARAM_STR);
			$stmt->execute();
			
		} else {
			$a = $stmt->fetchAll();
			$OldId = $a[0]['ID'];
			$stmt = DataBase::query()->prepare("UPDATE `additional_values` SET `value` = ? WHERE `ID` = ?"); 
			$stmt->bindValue(1,  $value, PDO::PARAM_STR);
			$stmt->bindValue(2,  $OldId, PDO::PARAM_INT);
			$stmt->execute();
		}
	}
	public function getValueInJSON($key)
	{
		$stmt = DataBase::query()->prepare("SELECT *  FROM `additional_fields` WHERE `name` = ?");
		$stmt->bindValue(1,  $key, PDO::PARAM_STR);
		$stmt->execute();
		$a = $stmt->fetchAll();
		$stmt = DataBase::query()->prepare("SELECT *  FROM `additional_values` WHERE `id_field` = ? AND `id_user` = ?");
		$stmt->bindValue(1,  $a[0]['ID'], PDO::PARAM_INT);
		$stmt->bindValue(2,  $this->getUserId(), PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) {
			return false;
		} else {
			$a = $stmt->fetchAll();
		
			return $a[0]['value'];
		}
	}
	public function getTimetable($id_group, $date, $day, $name_group, $date_timetable)
	{
		$im = "";
		$stmt = DataBase::query()->prepare("SELECT *  FROM `timetable` WHERE `id_group` = ? AND `date` = ? AND `day` = ? ORDER BY `time_id`");
		$stmt->bindValue(1,  $id_group, PDO::PARAM_INT);
		$stmt->bindValue(2,  $date, PDO::PARAM_INT);
		$stmt->bindValue(3,  $day, PDO::PARAM_INT);
		$stmt->execute();
		// return 'Расписание на техническом обслуживании';
		if ($id_group == 1)
		{
			return ['Необходимо перезайти в расписание. Нажмите кнопку Назад и снова напишите слово Расписание!', 0];
		}
		if ($stmt->rowCount() == 0) 
		{
			
			return ['Наш бот очень упорно трудился. Сейчас он в отпуске. Расписание отключено. Увидимся в следующем семестре!',0];
		} else {
			$str = '';
			$a = $stmt->fetchAll();
			
			require_once('Photo.php');
			$timeUnix = 0;
			
			foreach ($a as $key => $timetable) {
				// here
				if (mb_strlen($timetable['string']) > 0) {
					$t = preg_replace("/\s{2,}/"," ",$timetable['string']);
					$t = str_replace(array("\r","\n"),"",$t);
					
				} else {
					$str = "";
					$timeUnix = time();
					photoMake($timeUnix, $a, $name_group, $date, $date_timetable);
					break;
					// $t = (($timetable['sub'] > 0) ? $timetable['sub'].' подгр. ' : '') . $timetable['pairname'] .PHP_EOL.$timetable['cab'].' '.$timetable['teacher'];
				}
				if (($key - 1) != -1 && $a[$key - 1]['time_id'] == $timetable['time_id']) {
					$str .= $t.PHP_EOL;
				} else {
					if ($id_group >= 286 && $id_group <= 332) {
						$str .= ($timetable['time_id']).'&#8419; '.$this->kfu[$timetable['time_id']].PHP_EOL.$t.PHP_EOL;
					} else {
						$str .= ($timetable['time_id']).'&#8419; '.$this->dates[$timetable['time_id']].PHP_EOL.$t.PHP_EOL;
					}
				}
				//	$str .= ($timetable['time_id'] + 1).'&#8419; '.$this->dates[$timetable['time_id']].PHP_EOL.$t.PHP_EOL;

			}
			return [$str,$timeUnix];
		}
		
	}
	public function getTimetableWeek($id_group, $date, $day, $name_group, $date_timetable)
	{
		$day = date("N", strtotime($date_timetable));
		if ($day == 7) {
			$date_1  = date('Y-m-d', strtotime($date . ' + 1 day'));
			$date_2  = date('Y-m-d', strtotime($date . ' + 8 day'));
		} else {
			$date_1  = date('Y-m-d', strtotime($date . ' - '.($day - 1).' day'));
			$date_2  = date('Y-m-d', strtotime($date . ' + '.(7 - $day).' day'));
		
		}
		// var_dump($day, $date_1, $date_2);
		$im = "";
		$stmt = DataBase::query()->prepare("SELECT *  FROM `timetable` WHERE `id_group` = ? AND `date` >= ? AND `date` <= ? ORDER BY `time_id`");
		$stmt->bindValue(1,  $id_group, PDO::PARAM_INT);
		$stmt->bindValue(2,  $date_1, PDO::PARAM_STR);
		$stmt->bindValue(3,  $date_2, PDO::PARAM_STR);
		$stmt->execute();
		// return 'Расписание на техническом обслуживании';
		if ($id_group == 1)
		{
			return ['Необходимо перезайти в расписание. Нажмите кнопку Назад и снова напишите слово Расписание!', 0];
		}
		if ($stmt->rowCount() == 0) 
		{
			
			return ['...',0];
		} else {
			$str = '';
			$a = $stmt->fetchAll();
			
			require_once('Photo.php');
			$timeUnix = 0;
			$all = [1=>[],2=>[],3=>[],4=>[],5=>[],6=>[]];
			foreach ($a as $key => $timetable) {
				if (mb_strlen($timetable['string']) > 0) {
					return ['Доступно 9 факультетам из 16',0];
				} else {
					$all[$timetable['day']] []= $timetable;
				}
				
			}
			$timeUnix = time();
			photoMakeWeek($timeUnix, $all, $name_group, $date, $all, $date_1);
							
			return [$str,$timeUnix];
		}
		
	}
	public function getPollGollandKeyboard($arr) {
		$buttons = [];
		$index = 0;
		foreach ($arr as $key => $answer) {
			$buttons []= [[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>$answer['field']]),
					'label' => urlencode($answer['name'])
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
	
	public function findGroup($name)
	{
		$stmt = DataBase::query()->prepare("SELECT `ID`, `name_group` as `name` FROM `groups` WHERE `name_group` = ? LIMIT 1");
		$stmt->bindValue(1,  $name, PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) 
		{
			$t['ID'] = 0;
			return $t;
		} else {
			$t= $stmt->fetchAll();
			return $t[0];
		}
	}
	public function setUserData()
	{
		$stmt = DataBase::query()->prepare("SELECT * FROM `user` WHERE `id_vk` = ? AND `id_group` = ? LIMIT 1");
		$stmt->bindValue(1,  $this->getUserId(), PDO::PARAM_INT);
		$stmt->bindValue(2,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount() == 0) 
		{
		
			$this->setLevel(0);
			$this->addUserData();
			$this->user_data = [
				'id_vk'=>$this->getUserId(),
				'first_name' => '',
				'last_name' => '',
				'sex' => 3
				];
		}
		else
		{
		
			$t= $stmt->fetchAll();
			$this->setLevel($t[0]['level']);
			$this->user_data = $t[0];
			// $stmt = DataBase::query()->prepare("
			// 	SELECT `id_field`, `name`, `value` FROM `additional_values` 
			// 	INNER JOIN `additional_fields` ON `additional_values`.`id_field` = `additional_fields`.`ID`
			// 	WHERE `additional_values`.`id_user` = ?");
			// $stmt->bindValue(1, $t[0]['ID'], PDO::PARAM_INT);
		}
		
	}
	public function getUserData()
	{
		if (!isset($this->user_data))
		{
			$this->setUserData();
		}
	
		return $this->user_data;
	}
	public function addUserData()
	{
	
		//$this->getUserDataByVkAPI();
		$stmt = DataBase::query()->prepare("INSERT INTO `user` (`id_vk`, `id_group`, `level`) VALUES (?,?,?)");
		//$stmt = DataBase::query()->prepare("INSERT INTO `user` (`id_vk`,`first_name`,`last_name`,`sex`) VALUES (?)");
		$stmt->bindValue(1,  $this->getUserId(), PDO::PARAM_INT);
		$stmt->bindValue(2,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		$stmt->bindValue(3,  $GLOBALS['start_id'], PDO::PARAM_INT);
		
		$stmt->execute();
	}
	public function getNameGroup($id)
	{
		$stmt = DataBase::query()->prepare("SELECT `name_group` FROM `groups` WHERE `ID` = ?");
		$stmt->bindValue(1,  $id, PDO::PARAM_INT);
		$stmt->execute();
		$t= $stmt->fetchAll();
		if ($stmt->rowCount() == 0) 
		{
			return "Группа не выбрана, просьба нажать кнопку Назад и перезайти в расписание";
		}
		return $t[0]['name_group'];
	}
	
	public function getIsDisabledSendByUser()
	{
		return 0; //((isset($this->user_data['is_send'] )) && $this->user_data['is_send'] == 0) ? 0 : 1;
	}
	public function updateLevel($lvl)
	{
		$stmt = DataBase::query()->prepare("UPDATE `user` SET `level` = ? WHERE `id_vk` = ?  AND `id_group` = ? LIMIT 1");
		$stmt->bindValue(1,  $lvl, PDO::PARAM_INT);
		$stmt->bindValue(2,  $this->getUserId(), PDO::PARAM_INT);
		$stmt->bindValue(3,  $GLOBALS['_id_group'], PDO::PARAM_INT);
		
		$stmt->execute();
		$this->setLevel($lvl);
	}
	public function updateDisabledSend($m)
	{
		$stmt = DataBase::query()->prepare("UPDATE `user` SET `is_send` = ?,`datetime_stop_send` = ? WHERE `id_vk` = ? LIMIT 1");
		$stmt->bindValue(1,  $m, PDO::PARAM_INT);
		$stmt->bindValue(2,  date("Y-m-d H:i:s"), PDO::PARAM_STR);
		$stmt->bindValue(3,  $this->getUserId(), PDO::PARAM_INT);
		$stmt->execute();
	}
	public function getTimeTableData($id_group, $date_timetable, $need = 0){
		$name_group = $this->getNameGroup($id_group);
		$date = date("Y-m-d", strtotime($date_timetable));
		$day = date("N", strtotime($date_timetable));
		$buttons = [];
		$index = 0;
		$days = ['Пн','Вт','Ср','Чт','Пт'];
		$daysEn = ['pn','vt','sr','ch','pt'];
		$month = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
		// foreach ($days as $key => $dayAr)
		// {
			$buttons []= [[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'pn']),
					'label' => urlencode('Пн')
				],
				'color'=>'primary',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'vt']),
					'label' => urlencode('Вт')
				],
				'color'=>'primary',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'sr']),
					'label' => urlencode('Ср')
				],
				'color'=>'primary',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'ch']),
					'label' => urlencode('Чт')
				],
				'color'=>'primary',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'pt']),
					'label' => urlencode('Пт')
				],
				'color'=>'primary',
			]];
		// }
		
		$buttons []= [[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'left']),
					'label' => urlencode('&#11013;')
				],
				'color'=>'default',
			],
			[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'sb']),
					'label' => urlencode("Сб")
				],
				'color'=>'primary',
			],
			[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'today']),
					'label' => urlencode('Сегодня')
				],
				'color'=>(strtotime(date("Y-m-d")) == strtotime($date_timetable)) ? 'positive' : 'primary',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'week']),
					'label' => urlencode('Неделя')
				],
				'color'=>'default',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'right']),
					'label' => urlencode('&#10145;')
				],
				'color'=>'default',
			],];
		if ($day == 7) {
			$buttons[0][0]['color'] = 'positive';
			$day = 0;
			$date  = date('Y-m-d', strtotime($date . ' + 1 day'));
		} else {
			if ($day <= 3) {
				$index = intval($day) - 1;
			} else {
				$index = 1;
			}
			// $buttons[(($day <= 3)? 0: 1)][$index]['color'] = 'positive';
			$buttons[(($day <= 5)? 0: 1)][$index]['color'] = 'positive';
		 }
		$buttons []= [[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'exit']),
					'label' => urlencode('Выйти')
				],
				'color'=>'default',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>($this->isLikedGroup($id_group) == 0) ? 'like' : 'unlike']),
					'label' => urlencode(($this->isLikedGroup($id_group) == 0) ? '&#10084;' : '&#10084;')
				],
				'color'=>($this->isLikedGroup($id_group) == 0) ? 'default' : 'positive',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>'star']),
					'label' => urlencode('Мои &#10084;')
				],
				'color'=>'default',
			],[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=>($this->isNotifyGroup($id_group) == 0) ? 'remind' : 'uremind']),
					'label' => urlencode(($this->isNotifyGroup($id_group) == 0) ? 'Вкл &#128227;' : '&#128227;')
				],
				'color'=>($this->isNotifyGroup($id_group) == 0) ? 'default' : 'positive',
			]];
			
		// $buttons []= [['action' => [
		// 			'type' => 'text',
		// 			'payload' =>  json_encode(['button'=>'wrong']),
		// 			'label' => urlencode('О системе')
		// 		],
		// 		'color'=>'default',
		// 	],['action' => [
		// 			'type' => 'text',
		// 			'payload' =>  json_encode(['button'=>'map']),
		// 			'label' => urlencode('Карта')
		// 		],
		// 		'color'=>'default',
		// 	],[
		// 		'action' => [
		// 			'type' => 'text',
		// 			'payload' =>  json_encode(['button'=>'dong']),
		// 			'label' => urlencode('Звонки')
		// 		],
		// 		'color'=>'default',
		// 	],];

		$q[0] = json_encode([
			'one_time' => false,
			'buttons' => $buttons
		], JSON_UNESCAPED_UNICODE);
		
		if ($need == 0) {
			return $q;
		}
		if ($need == 5) {
			$tmp = $this->getTimetableWeek($id_group, $date, $day, $name_group, $date_timetable);
			if ($tmp[1] == 0) {
			$q[1] =	urlencode($name_group.' &#128467;  '.$this->getEvenOrOdd ($date).' '.date("j", strtotime($date_timetable)).' '.
			$month[(date("n", strtotime($date_timetable)) - 1)].PHP_EOL.PHP_EOL.$tmp[0]);
			$q[2] = 0; 
				// фотки нет
			} else {
				$q[2] = $tmp[1]; 
				// фотка есть
			}
			return $q; 
		}
		$tmp = $this->getTimetable($id_group, $date, $day, $name_group, $date_timetable);
		
		if ($tmp[1] == 0) {
			$q[1] =	urlencode($name_group.' &#128467;  '.$this->getEvenOrOdd ($date).' '.date("j", strtotime($date_timetable)).' '.
			$month[(date("n", strtotime($date_timetable)) - 1)].PHP_EOL.PHP_EOL.$tmp[0]);
			$q[2] = 0; 
			// фотки нет
		} else {
			$q[2] = $tmp[1]; 
			// фотка есть
		}
		
		
		 return $q; 
		
	}
	public function isNotifyGroup($id_group) {
		$array = json_decode($this->getValueInJSON('notif'), true);
		if (is_array($array))
			foreach ($array as $a) {
				if ($a == $id_group) {
					return $id_group;
				}
			}
		return 0;
	}
	public function isLikedGroup($id_group) {
		$array = json_decode($this->getValueInJSON('like_timetable'), true);
		if (is_array($array))
		foreach ($array as $a) {
			if ($a == $id_group) {
				return $id_group;
			}
		}
		return 0;
	}
	public function getLikeGroup() {
		
		$array = json_decode($this->getValueInJSON('like_timetable'), true);
		$buttons = [];
		foreach ($array as $a) {
			$buttons []= [[
				'action' => [
					'type' => 'text',
					'payload' =>  json_encode(['button'=> $a]),
					'label' => urlencode($this->getNameGroup($a))
				],
				'color'=>'default',
			]];
		}
		$q[0] = json_encode([
			'one_time' => false,
			'buttons' => $buttons
		], JSON_UNESCAPED_UNICODE);
		$q[1] =	urlencode('Выберите группу:').PHP_EOL;
		return $q; 
	}
	
	public function getNewDate($message, $dateOld) {
		if ($message == 'like'  || $message == 'star' ) {
			return $dateOld;
		}
		switch ($message) {
			case 'left':
				return date('Y-m-d', strtotime($dateOld . ' - 7 days'));
				break;
			case 'right':
				return date('Y-m-d', strtotime($dateOld . ' + 7 days'));
				break;
			case 'today':
				return (date("N") == 7) ? date('Y-m-d', strtotime(date("Y-m-d") . ' + 1 day')) : date("Y-m-d"); 
				break;
		}
		$buttons = ['pn'=>0,'vt'=>1,'sr'=>2, 'ch'=>3,'pt'=>4,'sb'=>5];
		if ($message == 'pn' || $message == 'vt' || $message == 'sr' || $message == 'ch' || $message == 'pt' || $message == 'sb') {

			$t = date('Y-m-d', strtotime('Monday this week '.$dateOld));
			return date('Y-m-d', strtotime($t . ' + '.$buttons[$message].' days'));
		}
		return (date("N") == 7) ? date('Y-m-d', strtotime(date("Y-m-d") . ' + 1 day')) : date("Y-m-d"); 
	}
	public function getEvenOrOdd ($date) {
		$dataInstitute  = '2019-09-02';
		$dateStart      =  '2019-09-02';
		$typeWeek       =  1;
		$getMonday = $date;
		if ($typeWeek == 0) 
			$typeWeek   = (((strtotime($getMonday) - strtotime($dateStart))/3600/24/7 + 1) % 2 == 0) ? 1 : 2;
		else
			$typeWeek   = (((strtotime($getMonday) - strtotime($dateStart))/3600/24/7 + 1) % 2 == 0) ? 2 : 1;
		$num = intval((strtotime($getMonday) - strtotime($dateStart))/3600/24/7 + 1) ;
		return (intval($typeWeek) == 2) ? '** (чётная) неделя (№ '.$num.')'  : '* (нечётная) неделя (№ '.$num.')';
	}
}
