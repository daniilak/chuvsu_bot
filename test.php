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
	if (strlen($u['value']) > 2){
		foreach (json_decode($u['value'],true) as $val){
			$msg = urlencode("Расписание на завтра!");
			$user = new User($u['id_user']);
			$date_timetable = date("Y-m-d");
			$date_timetable = date('Y-m-d', strtotime($date_timetable. ' + 1 days'));
			$id_group = $user->getValueInJSON('id_group');
			$timetableStr = $user->getTimeTableData($id_group, $date_timetable);
			
			requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg.PHP_EOL.$timetableStr[1].'&keyboard='.$timetableStr[0]);
			// echo 'ok'; die();
		}
	}
	// echo $user['id_user'].$user['value']."<br>";
}




exit();

// $ex = explode(PHP_EOL,$str);
// foreach ($ex as $a) {
// 	$r = trim(preg_replace('/( )+/', ' ', $a));
// 	$r = explode('	', $r);
// 	$msg = urlencode('Ура! Мы долго ждали!'.PHP_EOL.'Ваш логин: '.$r[1].PHP_EOL.'Ваш пароль (на англ раскладке): '.$r[2].
// 		PHP_EOL.'Можно войти в почтовый ящик через браузер, набрав в адресной строке
// 		https://mail.chuvsu.ru. В появившемся окне набрать имя почтового ящика  и пароль.
// 		Пароль набирается в английской раскладке. ');
// 		requestVkAPI("messages.send", "peer_id=".$r[0]."&message=".$msg);
// 	usleep(333333);
// 	// die();
// }
// die();
function getValueInJSON($json, $key)
{
	$a = json_decode($json,true);
	if (isset($a[$key]))
		return $a[$key];
	else 
		return '';
}
$stmt = DataBase::query()->prepare("SELECT *  FROM `user` WHERE `json` != ''"); 
$stmt->execute();

foreach ($stmt->fetchAll() as $user) {
	$j = json_decode($user['json'], true);
	// var_dump($j);
	// if (is_array($j))
	// 	continue;
	foreach ($j as $key => $json) {
		$stmt = DataBase::query()->prepare("SELECT *  FROM `additional_fields` WHERE `name` = ?"); 
		$stmt->bindValue(1,  $key, PDO::PARAM_STR);
		$stmt->execute();
		$t = $stmt->fetchAll();
		$idField = $t[0]['ID'];
		{
			$stmt = DataBase::query()->prepare("SELECT *  FROM `additional_values` WHERE  `id_user` = ? AND `id_field` = ?"); 
			$stmt->bindValue(1,  $user['id_vk'], PDO::PARAM_STR);
			$stmt->bindValue(2,  $idField, PDO::PARAM_STR);
			$stmt->execute();
			if ($stmt->rowCount() != 0) {
				$tt = $stmt->fetchAll();
				$OldId = $tt[0]['ID'];
				$stmt = DataBase::query()->prepare("UPDATE `additional_values` SET `value` = ? WHERE `ID` = ?"); 
				$stmt->bindValue(1,  $user['id_vk'], PDO::PARAM_STR);
				$stmt->bindValue(2,  $OldId, PDO::PARAM_STR);
				$stmt->execute();
			}
			$stmt = DataBase::query()->prepare("INSERT INTO `additional_values`  (`id_user`,`id_field`,`value`) VALUES (?,?,?)"); 
			$stmt->bindValue(1,  $user['id_vk'], PDO::PARAM_STR);
			$stmt->bindValue(2,  $idField, PDO::PARAM_STR);
			$stmt->bindValue(3,  is_array($json) ?  json_encode($json) : $json , PDO::PARAM_STR);
			$stmt->execute();
		}
		// $stmt = DataBase::query()->prepare("INSERT INTO `additional_fields`  (`name`) VALUES (?)"); 
		// $stmt->bindValue(1,  $key, PDO::PARAM_STR);
		// $stmt->execute();
	}
}

die();
function plural_form($number, $after) {
  $cases = array (2, 0, 1, 1, 1, 2);
  return $after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
}


	$aDate = time(); // Текущая дата
	$bDate = strtotime("01.09.2018"); // Установленная мною дата
	 
	$datediff = $bDate - $aDate;
	$photo = imagecreatefromjpeg((( date('G') >= 6 && date('G') <= 18) ? plural_form(floor($datediff/(60*60*24)), array('02.jpg','022.jpg','0222.jpg')) : plural_form(floor($datediff/(60*60*24)), array('01.jpg','011.jpg','0111.jpg'))));
	
echo __DIR__. '/Panton-BlackCaps.otf';

	// imagefttext($photo, 15, 0, 1190, 28, imagecolorallocate($photo, 255,255,255),'PFBeauSansPro-Black.ttf',date("H:i")." Обновляется в 18:00");
	// imagettftext($photo, 15, 0, 1190, 28, imagecolorallocate($photo, 255,255,255),  __DIR__. '/Panton-BlackCaps.otf'," Обновляется в 18:00");
	// echo floor($datediff/(60*60*24));
	// echo date('G');
	// echo (( date('G') >= 6 && date('G') <= 18) ?'a' :'b' ) ;
	
	// imagettftext($photo, 50, 0, 0, 0,  imagecolorallocate($photo, 0,0,0), 'Panton-BlackCaps.otf','23');
		imagettftext($photo, 40, 0, 1095, 325, 
		 (( date('G') >= 6 && date('G') <= 18) ? imagecolorallocate($photo, 0,63,0) : imagecolorallocate($photo, 33,27,41)), 
		__DIR__. '/Panton-BlackCaps.otf',floor($datediff/(60*60*24)));
	// imagettftext($photo, 30, 0, 1225, 320, 
	// 	 (( date('G') >= 6 && date('G') <= 18) ? imagecolorallocate($photo, 0,63,0) : imagecolorallocate($photo, 33,27,41)), 
	// 	'Panton-BlackCaps.otf', 
	// 	plural_form(floor($datediff/(60*60*24)), array('день','дня','дней')));
	imagejpeg($photo, 'im.jpg');
	imagedestroy($photo);
		// die();
	$request = requestVkAPI("photos.getOwnerCoverPhotoUploadServer", "group_id=".$GLOBALS['id_group']."&crop_x=0&crop_y=0&crop_x2=1590&crop_y2=400");
	// var_dump($request);
	$curl = curl_init($request['upload_url']);
	$opts = [
			CURLOPT_USERAGENT => 'LOCALHOST',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_POSTFIELDS => ['photo' => class_exists("CURLFile", false) ? new CURLFile("im.jpg") : "im.jpg"]
	]; 
	curl_setopt_array($curl, $opts);
	$request = json_decode(curl_exec($curl), true); 
	curl_close($curl);
	requestVkAPI("photos.saveOwnerCoverPhoto", "photo={$request['photo']}&hash={$request['hash']}"); 
