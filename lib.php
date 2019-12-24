<?php
function requestVkAPI($method, $params, $a = 0)
{
	$tok = $GLOBALS['token'];
	if ($a == 1)  {
		$tok = "85f4be2bf41f95596bc96497c30592cc75059452111bcd96e8dfcc766e794ebb14fa415547b51c0bb6ae6";
	}
	// if ($a == 1)  {
	// 	$tok = "85f4be2bf41f95596bc96497c30592cc75059452111bcd96e8dfcc766e794ebb14fa415547b51c0bb6ae6";
	// }
	if (strlen($a) > 2) {
		$tok = $a;
	}
	$url = 'https://api.vk.com/method/' . $method;
	$options = [
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7',
		CURLOPT_URL => $url,
		CURLOPT_ENCODING => '',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_POST=> true,
		CURLOPT_POSTFIELDS=> $params . '&access_token='.$tok.'&lang=ru&v=5.80',
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_HTTPHEADER => [
			'Accept-Language: ru,en-us'
		]
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
	// if ($i == 1)
		// var_dump($parsedResult);
	if (isset($parsedResult['error']) && $parsedResult['error']['error_code'] !== 14) 
	{
		return $parsedResult['error'];
	}
	else 
	{
		if (isset($parsedResult['response'])) 
			return $parsedResult['response'];
		else 
			return $parsedResult;
	}
}


require_once("obj/DataBase.php");
require_once("obj/User.php");
require_once("obj/Question.php");
require_once("obj/ActiveWord.php");

 


