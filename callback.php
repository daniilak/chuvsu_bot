<?php
ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);
require_once('config.php');
	
	// echo 'ok';
	// die();

// $encod = mb_detect_encoding(file_get_contents('php://input'));
// iconv($encod, 'UTF-8',file_get_contents('php://input'));
$data = json_decode(file_get_contents('php://input'), true); 

if ($data['type'] == 'confirmation')
{
	echo $GLOBALS['confirmation_token']; 
    die();
}
if ($data['type'] == 'wall_reply_new')
{
	
	$user = new User($data['object']['from_id']);
	// echo "ok"; die();
	$message = "5 баллов за комментарий приняты! 

	Не забудь поставить лайк или сделать репост (+20 баллов) к любой записи, чтобы увеличить шанс твоей армии на победу";
	// var_dump($data);
	if (mb_strtoupper($data['object']['text']) != 'ТАНОС')
	{
		echo 'ok'; die();
	}
	file_put_contents("index.txt" , $data['object']['text']);
	$userData = $user->getUserData();
	// file_put_contents("index.txt" , var_export($userData, true));
	
	$stmt = DataBase::query()->prepare("INSERT INTO  `game_comment` (`id_user`, `id_comment`) VALUES (?,?)");
	$stmt->bindValue(1,  $data['object']['from_id'], PDO::PARAM_INT);
	$stmt->bindValue(2,  $data['object']['post_id'], PDO::PARAM_INT);
	$stmt->execute();

	$ledArr = explode('*', file_get_contents('led.txt'));
	$ledArr[(mb_strtoupper($data['object']['text']) != 'ТАНОС') ? 0 : 1] 
		= intval($ledArr[(mb_strtoupper($data['object']['text']) != 'ТАНОС') ? 0 : 1])  + 5;
	file_put_contents('led.txt',$ledArr[0].'*'.$ledArr[1]);
	
	requestVkAPI("wall.createComment", 
		"owner_id=-47825810&from_group=1&reply_to_comment=".$data['object']['id']."&post_id=".$data['object']['post_id']."&message=124",
	1	);
	
		
	echo 'ok';
	die();
}

//////////////////////////

if ($data['type'] != 'message_new')
{
	echo 'ok';
	die();
}
$id_additional_fields = 0 ;
$function_code = 0 ;
$message = $data['object']['text']; 
$textMessage = $data['object']['text']; 
$payload = false;
if (isset($data['object']["payload"]))
{
	if ($message == 'Начать')
	{
		$message = 'q';
	} 
	else {
		
			$t = json_decode($data['object']["payload"],true);
			if (!isset($t['button']))
			{
				$message = 'q';
			} else {
				$b = explode('_',$t['button']);
				if ($b[0] == "888" && $b[1] == "888")
				{
					// var_dump("kek");
					$message = 'q';
				} else {
					$payload = true;
					$message = $b[0];
					$id_additional_fields = (!isset($b[1])) ? 0 : $b[1] ;
					$function_code = (!isset($b[2])) ? 0 : $b[2] ;
				}
			}
		
	}
}
if ($data['object']['peer_id'] == $data['object']['from_id'])
{
	$user = new User($data['object']['peer_id']);
} else
{
	$user = new User($data['object']['peer_id']);
}
$userData = $user->getUserData();

// if ($data['object']['peer_id'] == 2000000019)
// {
	// $chatID = 19; // $data['object']['peer_id'] - 2000000000;
// 	if ($data['object']['text'] == "top1") {
// requestVkAPI("messages.send", "chat_id=19&message=".
// urlencode("Шутка"));
// echo "ok"; die();
		
// 	}
// 	if ($data['object']['text'] == "hdm1") {
// requestVkAPI("messages.send", "chat_id=".$chatID."&message=".urlencode("Эй, работяга, просыпайся, пора тебя будить"));

// 			requestVkAPI("messages.send", "chat_id=".$chatID."&message=".urlencode("Работяга выбери @id225249913 (Максима) или @id285374191 (Викторию) или @id_rezukova01(Дарью) или @id535645577 (Светлану) или @id314520804 (Полю) или @dr.ilfat(Ильфата) или @kostya.gromow(Костантина) или @id205822720 (Марию) или @id402197342 (Давыда) или @rrr_may(Машу)"));
// 	sleep(1);
// requestVkAPI("messages.send", "chat_id=".$chatID."&message=".urlencode("Работяга выбери @id225249913 (Максима) или @id285374191 (Викторию) или @id_rezukova01(Дарью) или @id535645577 (Светлану) или @id314520804 (Полю) или @dr.ilfat(Ильфата) или @kostya.gromow(Костантина) или @id205822720 (Марию) или @id402197342 (Давыда) или @rrr_may(Машу)"));
// // requestVkAPI("messages.send", "chat_id=".$chatID."&message=".urlencode("Cкоро розыгрыш, вы еще можете успеть попасть в топ"));
// 	sleep(1);
// requestVkAPI("messages.send", "chat_id=".$chatID."&message=".urlencode("Работяга выбери @id225249913 (Максима) или @id285374191 (Викторию) или @id_rezukova01(Дарью) или @id535645577 (Светлану) или @id314520804 (Полю) или @dr.ilfat(Ильфата) или @kostya.gromow(Костантина) или @id205822720 (Марию) или @id402197342 (Давыда) или @rrr_may(Машу)"));
// 	// requestVkAPI("messages.send", "chat_id=".$chatID."&message=".urlencode("Cкоро розыгрыш, вы еще можете успеть попасть в топ"));
// 	}
// 	echo "ok"; die();
// }
	// var_dump($data['object']['text']);
	// if (strtolower($data['object']['text']) == "бля"  ||
	// strpos($data['object']['text'], "кай ") > 0 ||	
	// strpos($data['object']['text'], "Кай ")  > 0  ||
	// strpos($data['object']['text'], "ха")  > 0)
	// // || mb_strtolower($data['object']['text']) == "сука" || mb_strtolower($data['object']['text']) == "пиздец")
	// {
		
	// 	}
// 	if (strpos($data['object']['text'], "Кай"))
// {
// 	$r = requestVkAPI("users.get", "user_id=".$data['object']['from_id']);
// 		// var_dump($r);
// 		// $msg = urlencode("Кай пред ".$r['first_name'].' '.$r['last_name']);
// 		$msg = urlencode("Кай пред ".$r[0]['first_name'].' '.$r[0]['last_name']);
// 		requestVkAPI("messages.send", "chat_id=19&message=".$msg);
	
// }	

	// if (strpos($data['object']['text'], "vk.me/join"))
	// {
	// 	if (strpos($data['object']['text'], "AJQ1d5zjphEbgrKg_rAiVySp") || strpos($data['object']['text'], "AJQ1d0zg0xFTUOCU82XoL1OC")) {
			
	// 	} else {
	// 		requestVkAPI("messages.removeChatUser", "chat_id=19"."&user_id=".$data['object']['from_id']);
	// 	}
	// }
	
	// if (in_array($data['object']['from_id'], [430580420,350525600, 385083439, 551409610, 501274243, 422788504, 120601466])) {
	// 	requestVkAPI("messages.removeChatUser", "peer_id=19"."&user_id=".$data['object']['from_id']);
	// }
	// (requestVkAPI("messages.removeChatUser", "chat_id=19"."&user_id=415869238"));

	// echo 'ok'; die();
// }
// if (isset($data['object']['action']['type']) &&  $data['object']['action']['type'] == 'chat_invite_user_by_link')
// if ($data['object']['text'] == '11')
// {
// }

/**
 * Тестирование по Голлангу
 */
if ($user->getLevel() == 118)
{
	if ($textMessage == 'Далее')
	{
		$user->insertValueToJson(32, -1);
		$user->insertValueToJson(33, json_encode([0,0,0,0,0,0,0]));
		$user->updateLevel(119);
	}
	
}
if ($user->getLevel() == 119 && $payload === TRUE) {

	$text = "Инженер (1) - Социолог (2)
	Кондитер (1) - Священнослужитель(З)
	Повар (1) - Статистик (4)
	Фотограф (1) - Торговый администратор (5)
	Механик (1) - Дизайнер (6)
	Философ (2) - Врач (3)
	Эколог (2) - Бухгалтер (4)
	Программист (2) - Адвокат (5)
	Кинолог (2) - Литературный переводчик (б)
	Страховой агент (з) - Архивист (4)
	Тренер (3) - Телерепортер (5)
	Следователь (3) - Искусствовед (6)
	Нотариус (4) - Брокер (5)
	Оператор ЭВМ (4) - Манекенщица (6)
	Фотокорреспондент (5) - Реставратор (6)
	Озеленитель (1) - Биолог исследователь (2)
	Водитель (1) - Бортпроводник (3)
	Метролог (1) - Картограф (4)
	Радиомонтажник(1) - Художник по дереву (6)
	Геолог (2) - Переводчик гид (3)
	Журналист (5) - Режиссер (6)
	Библиограф (2) - Аудитор (4)
	Фармацевт (2) - Юрисконсульт (3)
	Генетик (2) - Архитектор (6)
	Продавец (3) - Оператор почтовой связи (4)
	Социальный работник (3) - Предприниматель (5)
	Преподаватель вуза (3) - Музыкант исполнитель (6)
	Экономист (4) - Менеджер (5)
	Корректор (4) - Дирижер (6)
	Инспектор таможни (5) - Художник модельер (б)
	Телефонист (1) - Орнитолог (2)
	Агроном (1) - Топограф (4)
	Лесник (1) - Директор (5)
	Мастер по пошиву одежды (1) - Хореограф (б)
	Историк (2) - Инспектор ГАИ (4)
	Антрополог (2) - Экскурсовод (3)
	Вирусолог (2) - Актер (б) 
	Официант (3) - Товаровед (5)
	Главный бухгалтер (4) - Инспектор уголовного розыска (5)
	Парикмахер модельер (б) - Психолог (3)
	Пчеловод (1) - Коммерсант (5)
	Судья (3) - Стенографист (4) ";
	
	
	$indexPoll = intval($user->getValueInJSON('indexPollGolland'));
	$countPoll = json_decode($user->getValueInJSON('countPollGolland'), true);
	if ($indexPoll >= 0 && $indexPoll <= 41) {
		$userClick  = intval($message);
		$arrayPoll = explode("\n", $text);
		// Проверяем текущее значение
		$elem = explode("-", $arrayPoll[$indexPoll]);
		if ($userClick == "0")
		{
		    $elem = explode("(", $elem[0]);
		    $countPoll[intval($elem[1])] = $countPoll[intval($elem[1])]  + 1;
		}
		elseif ($userClick == "1")
		{
		    $elem = explode("(", $elem[1]);
		    $countPoll[intval($elem[1])] = $countPoll[intval($elem[1]) ] + 1;
		} 
		$user->insertValueToJson(33, json_encode($countPoll));
	}
	$indexPoll++;

	$user->insertValueToJson(32, $indexPoll);
	
	if ($indexPoll > 41) {
	    $resPoll = 0;
	    $mx = 0;
		foreach ($countPoll as $key => $c) {
		 if ($c > $mx ) {
			 $resPoll = $key + 1;
			 $mx = $c;
		 }
		}
		$msgResText = "";
		$a = "";
	    switch ($resPoll) {
	        case 1:
	        $a = "attachment=photo-129653556_456239581";
	        $msgResText = "
	        Реалистический тип
	        Профессионалы данного типа склонны заниматься конкретными вещами и их использованием, отдают предпочтение занятиям, требующим применения физической силы, ловкости. 
	        Ориентированы в основном на практический труд, быстрый результат деятельности. Способности к общению с людьми, формулировке и изложению мыслей развиты слабее. 
	        Чаще люди этого типа выбирают профессии механика, электрика, инженера, агронома, садовода, кондитера, повара и другие профессии, которые предполагают решение конкретных за дач, наличие подвижности, настойчивости, связь с техникой. Общение не является ведущим в структуре деятельности. ";
	        break;
	        case 2:
	        $a = "attachment=photo-129653556_456239582";
	        	
	        $msgResText = "
	        Интеллектуальный тип
	         Профессионалы данного типа отличаются аналитичностью, рационализмом, независимостью, оригинальностью, не склонны ориентироваться на социальные нормы.
	         Обладают достаточно развитыми математическими способностями, хорошей  формулировкой и изложением мыслей, склонностью к решению логических, абстрактных задач.
	         Люди этого типа предпочитают профессии научно-исследовательского направления: ботаник, физик, философ, программист и другие, в деятельности которых необходимы творческие способности и нестандартное мышление. Общение не является ведущим видом деятельности.
	         Также к этому типы относятся я профессии: бухгалтер, патентовед, нотариус, топограф, корректор и другие, направленные на обработку информации, предоставленной в виде условных знаков, цифр, формул, текстов.
	         Сфера общения в таких видах деятельности ограничена и не является ведущей, что вполне устраивает данный тип личности. Коммуникативные и организаторские способности развиты слабо, но зато прекрасно развиты исполнительские качества. ";
	        break;
	        case 3:
	        $a = "attachment=photo-129653556_456239583";
	        	
	        $msgResText = "Социальный тип
	        Профессионалы данного типа гуманны, чувствительны, ак-тивны, ориентированы на социальные нормы, способны к сопереживанию, умению понять эмоциональное состояние другого человека.
	        Обладают хорошими вербальными (словесными] способностями, с удовольствием общаются с людьми. Математические способности развиты слабее.
	        Люди этого типа ориентированы на труд, главным содержанием которого является взаимодействие с другими людьми, возможность решать задачи, предполагающие анализ поведения и обучения людей. Возможные сферы деятельности: обучение, лечение, обслуживание и другие, требующие постоянного контакта и общения с людьми, способностей к убеждению.
	        ";
	        break;
	        case 4:
	        $a = "attachment=photo-129653556_456239583";
	        	
	        $msgResText = "Артистический тип
	        Профессионалы данного типа оригинальны, независимы в принятии решений, редко ориентируются на социальные нормы и одобрение, обладают необычным взглядом на жизнь, гибкостью и скоростью мышления, высокой эмоциональной чувствительностью. Отношения с людьми строят, опираясь на свои ощущения, эмоции, воображение, интуицию. Обладают хорошей реакцией и обостренным восприятием. Любят и умеют общаться. 
	        Профессиональная предрасположенность в наибольшей степени связана с актерскосценической, музыкальной, изобразительной деятельностью. 
	        ";
	        break;
	        case 5:
	        $a = "attachment=photo-129653556_456239584";
	        	
	        $msgResText = "Предприимчивый тип
	        Профессионалы данного типа находчивы, практичны, быстро ориентируются в сложной обстановке, склонны к самостоятельному принятию решений, социальной активности, лидерству; имеют тягу к приключениям (возможно, авантюрным). Обладают достаточно развитыми коммуникативными способностями.
	        Не предрасположены к занятиям, требующим усидчивости, большой и длительной  концентрации внимания. Предпочитают деятельность, требующую энергии, организаторских способностей. Профессии: предприниматель, менеджер, продюсер и другие, связанные с руководством, управлением и влиянием на разных людей в разных ситуациях. 
	        ";
	        break;
	        case 6:
	        $a = "attachment=photo-129653556_456239585";
	        	
	        $msgResText = "Конвенциональный тип
	        Профессионалы данного типа практичны, конкретны, не любят отступать от задуманного, энергичны, ориентированы на социальные нормы.
	        Предпочитают четко определенную деятельность, выбирают из окружающей среды цели и задачи, поставленные перед ними обычаями и обществом. В основном выбирают профессии, связанные с канцелярскими и расчетными работами, со зданием и оформлением документов, установлением количественных соотношений между числами, системами условных знаков.
	        ";
	        break;
	    }
	    	$user->updateLevel(1);
	
	    requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&'.$a.'&message='.urlencode($msgResText) . '&keyboard='.$user->getPollGollandKeyboard([["field"=>"888_888","name"=>"Выйти в главное меню"]]));
	    
		echo "ok"; die();		
	 } else {
	 	$arrayPoll = explode("\n", $text);
	    $elem = explode("-", $arrayPoll[$indexPoll]);
	    $elemA = explode("(", $elem[0]);
	    $elemB = explode("(", $elem[1]);
	    
	    requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.urlencode(($indexPoll + 1)." из 42. ".$elemA[0]." или ".$elemB[0]. "?").'&keyboard='
	    	.$user->getPollGollandKeyboard([["field"=>"0_0","name"=>$elemA[0]],["field"=>"1_1","name"=>$elemB[0]],]));
		echo "ok"; die();
	 }
}


/**
 * Расписание университета
 */
 
$buttons = ['pn','vt','sr', 'ch','pt','sb','left','right','today'];
if ($user->getLevel() == 58) {
	$id_group = $user->findGroup($message);
	if ($id_group['ID'] == 0) {
		$msg = urlencode('К сожалению, вашей группы нет в базе или у вас ошибка в названии.'.
		PHP_EOL.'Напишите ещё раз название группы');
		requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg);
	} else {
		// var_dump($id_group);
		$user->insertValueToJson(19,$id_group['ID']);
		$buttons[] = $message;
		$date  = (date("N") == 7) ? date('Y-m-d', strtotime(date("Y-m-d") . ' + 1 day')) : date("Y-m-d"); 
		$user->insertValueToJson(2,$date);
		$user->updateLevel(66);
	}
}
if ($user->getLevel() == 66) {
	if (mb_strtolower($message) == "идея" ) {
		$user->updateLevel(153);
		requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&keyboard={"buttons":[],"one_time":true}&message='.urlencode("
		Оставь свой отзыв о работе бота здесь
		
Чтобы перейти назад, напиши слово \"Расписание\" или слово \"Привет\""));
		
		echo 'ok'; die();
	}

	// if (rand(1, 1000) < 700)
	// {
		// $answer = urlencode("Ты активировал(-а) подсказку!".PHP_EOL."Наш чат-бот может присылать тебе сам расписание на следующий день в 20:00. Для активации, нажми кнопку со смайлом звонка! Зеленый цвет кнопки - значит уже активно!");
		// requestVkAPI("messages.send", "user_id=".$user-> getUserId()."&message={$answer}");	
	// }
	if (requestVkAPI("groups.isMember", "group_id=".$GLOBALS['asdsadas']."&user_id=".$user-> getUserId()) == 0) {
	$answer = urlencode("Извини, но чтобы мы тебе могли показать расписание, необходимо вступить в группу:)");
	requestVkAPI("messages.send", "user_id=".$user-> getUserId()."&message={$answer}");	
	echo 'ok'; die(); }

	$id_group = $user->getValueInJSON('id_group');
	
	if ($message == 'map') {
		$msg = urlencode('Подробнее здесь: https://vk.com/wall-47825810_16157');
		requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.$msg.'&attachment=photo-47825810_457291088,photo-47825810_457291089,photo-47825810_457291090');
		echo 'ok'; die();
	}
	if ($message == 'dong') {
		$msg = urlencode('Расписание звонков
		1 пара 08.20 – 09.40 
		2 пара 09.55 – 11.15 
		3 пара 11.30 – 12.50 
		Большой перерыв 
		4 пара 13.20 – 14.40 
		5 пара 14.55 – 16.15 
		6 пара 16.30 – 17.50 
		7 пара 18.05 – 19.25 
		8 пара 19.40 – 21.00');
		requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.$msg);
		echo 'ok'; die();
	}
	if ($message == 'wrong') {
		$msg = urlencode('
		Расписание в формате фотографий формируется с сайта университета tt.chuvsu.ru.
		Расписание в текстовом формате публикуется как есть, за любые несоответствия не несем ответственности.
		Если вас интересует заняться работой с расписанием, просьба писать @daniilakk(Даниилу)
		Благодарим группу ИВТ-41-15 за огромную помощь при создании данной системы');
		requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.$msg);
		echo 'ok'; die();
	}
	if ($message == 'unlike') {
		$arraylike_timetable = $user->getValueInJSON('like_timetable');
		if (strlen($arraylike_timetable) == 2) {
			$arraylike_timetable_new = []; 
		} else {
			$arraylike_timetable_new = []; 
			$arraylike_timetable = json_decode($arraylike_timetable, true);

				foreach ($arraylike_timetable as $a) {
					if ($a != $id_group) {
						$arraylike_timetable_new [] = $a;
					}
				}

			
		}
		$user->insertValueToJson(3, $arraylike_timetable_new);
		$date_timetable = $user->getValueInJSON('date_timetable');
		$id_group = $user->getValueInJSON('id_group');
		$timetableStr = $user->getTimeTableData($id_group, $date_timetable);
		requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.urlencode('Убрано из избранных групп').'&keyboard='.$timetableStr[0]);
		echo 'ok'; die();
	}
	if ($message == 'like') {
		$arraylike_timetable = $user->getValueInJSON('like_timetable');
		
		
		$photoLove = array(
			"photo-129653556_457239609",
			"photo-129653556_457239610",
			"photo-129653556_457239608",
			"photo-129653556_457239611",
			"photo-129653556_457239612",
			"photo-129653556_457239613",
			"photo-129653556_457239614",
			"photo-129653556_457239615",
			// "sticker/1-4278-128"
		);
		$rand_love = array_rand($photoLove, 2);

		requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&attachment='.$photoLove[$rand_love[0]]);
		// var_dump($arraylike_timetable);
		if ($arraylike_timetable === false || strlen($arraylike_timetable) == 2) {
			$arraylike_timetable = [];
			$arraylike_timetable [] = $id_group;
			$user->insertValueToJson(3, $arraylike_timetable);
			$date_timetable = $user->getValueInJSON('date_timetable');
			$id_group = $user->getValueInJSON('id_group');
			$timetableStr = $user->getTimeTableData($id_group, $date_timetable);
			requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.urlencode('Добавлено в ваши избранные группы').'&keyboard='.$timetableStr[0]);
			echo 'ok'; die();
		} else {
			$arraylike_timetable = json_decode($arraylike_timetable, true);
			if (count($arraylike_timetable) == 0) {
				echo "ok"; die();
			}
			$arraylike_timetable = array_unique($arraylike_timetable);
			$b = 0;
			foreach ($arraylike_timetable as $a) {
				if ($a == $id_group) {
					$b = 1;
					break;
				}
			}
			if ($b == 0) {
				if (count($arraylike_timetable) < 5) {
					array_unshift($arraylike_timetable, $id_group);
				} else {
					array_pop($arraylike_timetable); 
				}
				$arraylike_timetable [] = $id_group;
				$user->insertValueToJson(3, $arraylike_timetable);
				$date_timetable = $user->getValueInJSON('date_timetable');
				$id_group = $user->getValueInJSON('id_group');
				$timetableStr = $user->getTimeTableData($id_group, $date_timetable);
				requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.urlencode('Добавлено в ваши избранные группы').'&keyboard='.$timetableStr[0]);
				echo 'ok'; die();
				
			} else {
			$msg = urlencode('Уже добавлено в избранное');
				requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg);
			echo 'ok'; die();
			}
			
		}
	
		
	}
	if ($message == 'remind') {
		$notify = $user->getValueInJSON('notif');
		if ($notify == FALSE || $notify == "[]") {
			$user->insertValueToJson(36, [$id_group]);
		} else {
			$notify = json_decode($notify, true);
			if (count($notify) == 0) {
				$user->insertValueToJson(36, [$id_group]);
			} else {
				$notify_ = [$id_group];
				foreach ($notify as $a) {
					if ($a != $id_group)
						$notify_[]=$a;
				}
				$user->insertValueToJson(36, $notify_);
			}
		}
		
		$msg = urlencode('Уведомления  у данной группы включены');
		$date_timetable = $user->getValueInJSON('date_timetable');
		$id_group = $user->getValueInJSON('id_group');
		$timetableStr = $user->getTimeTableData($id_group, $date_timetable);
		requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg.'&keyboard='.$timetableStr[0]);
		echo 'ok'; die();
	}
	if ($message == 'uremind') {
		$notify = $user->getValueInJSON('notif');
		if ($notify == FALSE || $notify == "[]") {
			$user->insertValueToJson(36, "[]");
		} else {
			$notify = json_decode($notify, true);
			if (count($notify) == 0) {
				$user->insertValueToJson(36, "[]");
			} else {
				$notify_ = [];
				foreach ($notify as $a) {
					if ($a != $id_group)
						$notify_[]=$a;
				}
				$user->insertValueToJson(36, $notify_);
			}
		}
		$msg = urlencode('Уведомления у данной группы выключены');
		$date_timetable = $user->getValueInJSON('date_timetable');
		$id_group = $user->getValueInJSON('id_group');
		$timetableStr = $user->getTimeTableData($id_group, $date_timetable);
		requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg.'&keyboard='.$timetableStr[0]);
		echo 'ok'; die();
	}
	if ($message == 'star') {
		$arraylike_timetable = $user->getValueInJSON('like_timetable');
		if ($arraylike_timetable == FALSE || $arraylike_timetable == "[]") {
			$msg = urlencode('Избранных групп нет');
			requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg);
			echo 'ok'; die();
		} else {
			$user->updateLevel(67);
			$timetableStr = $user->getLikeGroup ();
					// here		// here		// here		// here		// here		// here		// here
			requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$timetableStr[1].'&keyboard='.$timetableStr[0]);
			echo 'ok'; die();
		}
		
	}
	if ($message == 'week') {
		$date_timetable = $user->getValueInJSON('date_timetable');
		$date_timetable = $user->getNewDate($message, $date_timetable);
		$user->insertValueToJson(2,$date_timetable);
		$timetableStr = $user->getTimeTableData($id_group, $date_timetable, 5);
		if (!isset($timetableStr[0])) {
			echo "ok"; die();
		}
		// var_dump($timetableStr);
		if ($timetableStr[2] != 0) {
			$photoUrl = $timetableStr[2];
			$requestPhoto = requestVkAPI("photos.getMessagesUploadServer", "peer_id=".$user->getUserId());
			$curl = curl_init($requestPhoto['upload_url']);
			$opts = [
							CURLOPT_USERAGENT => 'LOCALHOST',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_SSL_VERIFYPEER => false,
							CURLOPT_SSL_VERIFYHOST => false,
							CURLOPT_POSTFIELDS => [
								'photo' => 
								class_exists("CURLFile", false) ? new CURLFile(__DIR__ ."/obj/".$photoUrl.".jpg") : __DIR__ ."/obj/".$photoUrl.".jpg"]
			]; 
			curl_setopt_array($curl, $opts);
			$request = json_decode(curl_exec($curl), true); 
			curl_close($curl);
			$request = requestVkAPI("photos.saveMessagesPhoto", "photo={$request['photo']}&server={$request['server']}&hash={$request['hash']}"); 
			// var_dump($photoUrl);
			@unlink(__DIR__."/obj/".$photoUrl.'.jpg');
			if (!isset($request[0])) {
				// var_dump($request);
				
				requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.urlencode("Что-то пошло не так с фотографией, видимо она не дошла...").'&keyboard='.$timetableStr[0]);
			} else {
				requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&attachment=photo'.$request[0]['owner_id'].'_'.$request[0]['id'].'&keyboard='.$timetableStr[0]);
			}
		} else {
			$a = requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$timetableStr[1].'&keyboard='.$timetableStr[0]);
		}
		echo 'ok'; die();
		
	}
	$date_timetable = $user->getValueInJSON('date_timetable');
	if (!in_array($message, $buttons)) {
		if ($message == 'exit' || mb_strtoupper($message) == 'ПРИВЕТ') {
			$user->updateLevel(1); 
		} else {
			echo 'ok'; die();
		}
	} else {
		$date_timetable = $user->getNewDate($message, $date_timetable);
		$user->insertValueToJson(2,$date_timetable);
		$timetableStr = $user->getTimeTableData($id_group, $date_timetable, 1);
		if (!isset($timetableStr[0])) {
			echo "ok"; die();
		}
		// var_dump($timetableStr);
		if ($timetableStr[2] != 0) {
			$photoUrl = $timetableStr[2];
			$requestPhoto = requestVkAPI("photos.getMessagesUploadServer", "peer_id=".$user->getUserId());
			$curl = curl_init($requestPhoto['upload_url']);
			$opts = [
							CURLOPT_USERAGENT => 'LOCALHOST',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_SSL_VERIFYPEER => false,
							CURLOPT_SSL_VERIFYHOST => false,
							CURLOPT_POSTFIELDS => [
								'photo' => 
								class_exists("CURLFile", false) ? new CURLFile(__DIR__ ."/obj/".$photoUrl.".jpg") : __DIR__ ."/obj/".$photoUrl.".jpg"]
			]; 
			curl_setopt_array($curl, $opts);
			$request = json_decode(curl_exec($curl), true); 
			curl_close($curl);
			$request = requestVkAPI("photos.saveMessagesPhoto", "photo={$request['photo']}&server={$request['server']}&hash={$request['hash']}"); 
			// var_dump($photoUrl);
			@unlink(__DIR__."/obj/".$photoUrl.'.jpg');
			if (!isset($request[0])) {
				// var_dump($request);
				
				requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.urlencode("Что-то пошло не так с фотографией, видимо она не дошла...").'&keyboard='.$timetableStr[0]);
			} else {
				requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&attachment=photo'.$request[0]['owner_id'].'_'.$request[0]['id'].'&keyboard='.$timetableStr[0]);
			}
		} else {
			$a = requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$timetableStr[1].'&keyboard='.$timetableStr[0]);
		}
			if (rand(1, 1000) < 100)
	{
		$answer = urlencode("
		Помоги боту стать лучше!
Оставь свой отзыв о работе системы, а может у тебя есть рекомендации или интересные предложения?
	Пиши нам слово \"идея\". Будущее бота зависит от тебя
");
		// $answer = urlencode("Ты активировал(-а) подсказку!".PHP_EOL."Наш чат-бот может присылать тебе сам расписание на следующий день в 20:00. Для активации, нажми кнопку со смайлом звонка! Зеленый цвет кнопки - значит уже активно!");
		requestVkAPI("messages.send", "user_id=".$user-> getUserId()."&message={$answer}");	
	} 
		echo 'ok'; die();
	}
}
if ($user->getLevel() == 67) {
	if (mb_strtolower($message) == "расписание" || mb_strtolower($message) == "начать" || mb_strtolower($message) == "привет" ) {
		$user->updateLevel(58);
		requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".urlencode("Напишите название группы:"));
		
		echo 'ok'; die();
	}
	if (mb_strtolower($message) == "идея" ) {
		$user->updateLevel(153);
		requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&keyboard={"buttons":[],"one_time":true}&message='.urlencode("
	Оставь свой отзыв о работе бота здесь
		
Чтобы перейти назад, напиши слово \"Расписание\" или слово \"Привет\""));
		
		echo 'ok'; die();
	}
	$m = intval($message);
	if ($m != 0) {
		$user->insertValueToJson(19,$message);
		$date_timetable = $user->getValueInJSON('date_timetable');
		$id_group = $user->getValueInJSON('id_group');
		$timetableStr = $user->getTimeTableData($id_group, $date_timetable, 1);
		if (!isset($timetableStr[0])) {
			// requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=);
		
			echo "ok"; die();
		}
		// var_dump($timetableStr);
		if ($timetableStr[2] != 0) {
			$photoUrl = $timetableStr[2];
			$requestPhoto = requestVkAPI("photos.getMessagesUploadServer", "peer_id=".$user->getUserId());
			$curl = curl_init($requestPhoto['upload_url']);
			$opts = [
							CURLOPT_USERAGENT => 'LOCALHOST',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_SSL_VERIFYPEER => false,
							CURLOPT_SSL_VERIFYHOST => false,
							CURLOPT_POSTFIELDS => [
								'photo' => 
								class_exists("CURLFile", false) ? new CURLFile(__DIR__ ."/obj/".$photoUrl.".jpg") : __DIR__ ."/obj/".$photoUrl.".jpg"]
			]; 
			curl_setopt_array($curl, $opts);
			$request = json_decode(curl_exec($curl), true); 
			curl_close($curl);
			$request = requestVkAPI("photos.saveMessagesPhoto", "photo={$request['photo']}&server={$request['server']}&hash={$request['hash']}"); 
			// var_dump($photoUrl);
			@unlink(__DIR__."/obj/".$photoUrl.'.jpg');
			if (!isset($request[0])) {
				// var_dump($request);
				
				requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&message='.urlencode("Что-то пошло не так с фотографией, видимо она не дошла...").'&keyboard='.$timetableStr[0]);
			} else {
				requestVkAPI("messages.send", "peer_id=".$user->getUserId().'&attachment=photo'.$request[0]['owner_id'].'_'.$request[0]['id'].'&keyboard='.$timetableStr[0]);
			}
		} else {
			$a = requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$timetableStr[1].'&keyboard='.$timetableStr[0]);
		
		}
		$user->updateLevel(66);
		echo 'ok'; die();
	} else {
		echo 'ok'; die();
	}
}


if ($user->getIsDisabledSendByUser() == 1) {
	echo 'ok';	
	die();
}

$ActiveWordData = ActiveWord::find($message);
if ($ActiveWordData != 0) {
	$question = new Question($ActiveWordData[0]['id_question']);
	$question->findById();
	$msg = urlencode($question->getText());
	$attachments = $question->getAttachments();
	if ($attachments != 0) {
		$msg .= '&attachment=';
		foreach ($attachments as $key => $attachment) {
			$msg .= $attachment['url'];
			if (isset($attachments[$key + 1])) { $msg .=','; }
		}
	}
	$user->updateLevel($ActiveWordData[0]['id_question']);
	if ($question->getIsDisabledSend() == 1) {
		$user->updateDisabledSend($question->getIsDisabledSend());
	}
	if ($question->getIdAdmin() != 0) {
		$question->sendAdminMessage($user->getUserId(), $message);
	}
	requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg.'&keyboard='.$question->getIdAnswers());
} else {
	if (!isset($userData['level'])) {
		$userData['level'] = 1;
	}
	$question = new Question($userData['level']);
	$question->findById();
	if ($question->getIsSaveAllString() != 0) {
		$user->insertValueToJson($question->getIsSaveAllString(),$textMessage);
		$payload = true;
	}
	$answer = ActiveWord::findAnswer($message, $userData['level'], $payload);
	if (is_int($answer) && $answer == 0) {	echo 'ok';	die(); }
	if ($id_additional_fields != 0) {
		$user->insertValueToJson($id_additional_fields,$function_code);
	}
	if (!is_array($answer)) {
		$t = ($question->getNextLevel() == 0) ? $question->getFatherId() : $question->getNextLevel();
		unset($question);
		$question = new Question($t);
		$user->updateLevel($t);
	} else {
		if ($answer[0]['id_test'] != 0)	{
			$question->updateTestDataUser($answer[0]['id_test'],$user-> getUserId(),$answer[0]['count_mark']);
		}
		unset($question);
		$question = new Question($answer[0]['id_next_question']);
		$user->updateLevel($answer[0]['id_next_question']);
	}
	$question->findById();
	$msg = $question->getText();
	$attachments = $question->getAttachments();
	$msg = urlencode($msg);
	if ($attachments != 0) {
		$msg .= '&attachment=';
		foreach ($attachments as $key => $attachment) {
			$msg .= $attachment['url'];
			if (isset($attachments[$key + 1])) {
				$msg .=',';
			}
		}
	}
	
	requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".$msg.'&keyboard='.$question->getIdAnswers());
		if ($question->issetAdditionalQuestions() == 1)	{
		foreach ($question->getAdditionalQuestions() as $additionalQuestion) {
			if (stristr($additionalQuestion['text'], '{result}')) {
				$a = ActiveWord::getResultTest($user-> getUserId(),$answer[0]['id_test']);
				$additionalQuestion['text'] = str_replace("{result}", "У Вас ".$a[0]['mark']." ".ActiveWord::declension_word($a[0]['mark'],['балл','балла','баллов'])."!", $additionalQuestion['text']);
			}
			$msg = '';
			if (isset($additionalQuestion['attachment'])) {
				$msg = '&attachment='.$additionalQuestion['attachment'];
			}
			requestVkAPI("messages.send", "peer_id=".$user->getUserId()."&message=".urlencode($additionalQuestion['text']).$msg.'&keyboard='.$question->getIdAnswers());
		}
	}
}

echo 'ok';	
