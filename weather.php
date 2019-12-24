<?php
ini_set("max_execution_time", "1000");
$token = "cd2b898c44a211a99f7054b9328237e81121c8e02ff4607156e201a78a9070f6dcc9f1f465799e525d736";
$page = 0;
$limit = 200;
$users = array();
do {
  $offset = $page * $limit;
  //Получаем список пользователей
  $members = json_decode(file_get_contents("https://api.vk.com/method/messages.getConversations?group_id=47825810&v=5.16&offset=$offset&count=$limit&access_token=$token"),true);

  //Спим
  usleep( 333333);

  foreach($members['response']['items'] as $user ) {
    $users []= $user["conversation"]["peer"]["id"]; // добавляем юзера к юзерам
  }
  //Увеличиваем страницу
  $page++;
} while($members['response']['count'] > $offset + $limit );


foreach ($users as $user) {
	echo $user.PHP_EOL;
}
//var_dump($users);
die();


function requestGismeteo($day)
{
	
	$options = [
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7',
		CURLOPT_HTTPHEADER => array('X-Gismeteo-Token:5b18f4510bd152.48552413',	'Accept-Language: ru,en-us'),
		CURLOPT_URL => 'https://api.gismeteo.ru/v2/weather/forecast/aggregate/4361/?days=3',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_POST=> true,
		CURLOPT_SSL_VERIFYHOST => false,
	
	];

	$ch = curl_init();
	curl_setopt_array($ch, $options);

	if(!$res = curl_exec($ch)) 
	{
		return false;
	} else 
	{
		$parsedResult = json_decode($res, true);
	}
	curl_close($ch);
	
	return $parsedResult['response'][$day];
}
$weather = requestGismeteo(0);

$message = $weather['description']['full'];
switch ($weather['precipitation']['intensity'])
{
	case 0:
		$message .= 'Нет осадков ';
		break;
	case 1:
		$message .= 'Небольшой дождь / снег ';
		break;
	case 2:
		$message .= 'Дождь / снег ';
		break;
	case 3:
		$message .= 'Сильный дождь / снег ';
		break;
}


var_dump(requestGismeteo(0));
// 'X-Gismeteo-Token: 5b18f4510bd152.48552413' 'https://api.gismeteo.ru/v2/weather/forecast/aggregate/4361/?days=3'
