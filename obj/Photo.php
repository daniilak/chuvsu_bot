<?php

// header('Content-type: image/png');
function getEvenOrOdd2 ($date) {
		$dataInstitute  = '2019-09-02';
		$dateStart      =  '2019-09-02';
		$typeWeek       =  1;
		$getMonday = $date;
		if ($typeWeek == 0) 
			$typeWeek   = (((strtotime($getMonday) - strtotime($dateStart))/3600/24/7 + 1) % 2 == 0) ? 1 : 2;
		else
			$typeWeek   = (((strtotime($getMonday) - strtotime($dateStart))/3600/24/7 + 1) % 2 == 0) ? 2 : 1;
		$num = intval((strtotime($getMonday) - strtotime($dateStart))/3600/24/7 + 1) ;
		return (intval($typeWeek) == 2) ? ['**','Чётная неделя','№ '.$num]  : ['*','Нечётная неделя','№ '.$num];
	}
function photoMake($name, $a, $name_group, $date, $date_timetable) {
	$date = getEvenOrOdd2($date);
	$days = ['', 'Пн','Вт','Ср', 'Чт','Пт','Сб'];
	$month = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
	if (intval(date("G")) > 6 &&  intval(date("G")) < 18 ) {
		$nig = false;
		$photo = imagecreatefromjpeg(__DIR__ . "/im3.jpg");
	} else {
		$nig = true;
		$photo = imagecreatefromjpeg(__DIR__ . "/im2.jpg");
	}
	$f = __DIR__ . '/PFBeauSansPro-Black.ttf';
	
	$color = imagecolorallocate($photo, 229, 229, 234);
	$colorA = [
		imagecolorallocate($photo, 94, 92, 230), //синий
		imagecolorallocate($photo, 94, 92, 230), 
		imagecolorallocate($photo, 94, 92, 230), 
		imagecolorallocate($photo, 255, 159, 10), // оранжевый
		imagecolorallocate($photo, 229, 229, 234),//белый
		imagecolorallocate($photo, 48, 209, 88)//зеленый
		];
	$colorB = [
		imagecolorallocate($photo, 10, 132, 255), 
		imagecolorallocate($photo, 10, 132, 255), 
		imagecolorallocate($photo, 10, 132, 255), 
		imagecolorallocate($photo, 255, 159, 10), 
		imagecolorallocate($photo, 28, 28, 30),
		imagecolorallocate($photo, 48, 209, 88)
		];
	$str = "";
	
	$CENTER = 600;
	$box = imagettfbbox(50, 0, $f,  $name_group);
	$left = $CENTER-round(($box[2]-$box[0])/2);
	imagettftext($photo, 50, 0, $left, 100, ($nig) ? $colorA[4] : $colorB[4], $f, $name_group);
	
	$box = imagettfbbox(20, 0, $f,  "Расписание загружено с сайта tt.chuvsu.ru 09 декабря 12:00");
	$left = $CENTER-round(($box[2]-$box[0])/2);
	imagettftext($photo, 20, 0, $left, 1300, ($nig) ? $colorA[4] : $colorB[4], $f,  "Расписание загружено с сайта tt.chuvsu.ru 09 декабря 12:00");
	$str = $days[date("w", strtotime($date_timetable))]
	.' '
	.date("j", strtotime($date_timetable)).' '.$month[(date("n", strtotime($date_timetable)) - 1)]; 
	$box = imagettfbbox(30, 0, $f,  $str);
	$left = $CENTER-round(($box[2]-$box[0])/2);
	imagettftext($photo, 30, 0, $left, 150, ($nig) ? $colorA[3] : $colorB[3], $f, $str);
	
	$str = $date[1].' '.$date[2]; 
	$box = imagettfbbox(30, 0, $f,  $str);
	$left = $CENTER-round(($box[2]-$box[0])/2);
	imagettftext($photo, 30, 0, $left, 200, ($nig) ? $colorA[3] : $colorB[3], $f, $str);
	
	
	imagettftext($photo, 40, 0, 1460, 480, ($nig) ? $colorA[4] : $colorB[4], $f, $date[1]);
	if ($date[0] == "**") {
		imagettftext($photo, 240, 0, 800, 190, ($nig) ? $colorA[4] : $colorB[4], $f, "**");
	} else {
		imagettftext($photo, 240, 0, 850, 190, ($nig) ? $colorA[4] : $colorB[4], $f, "*");
	}
	$start = 300;
	if (count($a) < 5) {
		$start = 360;
	} 
	$dates = ['', '08:20-09:40', '09:55-11:15', '11:30-12:50', '13:20-14:40', '14:55-16:15', '16:30-17:50', '18:05-19:25', '19:40-21:00'];
	
    $kfu = ['', '8.30-10.00', '10.10-11.40', '11.50-13.20', '14.00-15.30', '15.40-17.10', '17.20-18.50', '19.00-20.30', '?)'];
    

	foreach ($a as $key => $timetable) {
		
		// imagefilledrectangle($photo, 200, $start - 45, 208, $start + 50, $color);
		// imagefilledrectangle($photo, 1400, $start - 45, 1408, $start + 50, $color);
		if (($key - 1) != -1 && $a[$key - 1]['time_id'] == $timetable['time_id']) {
			// if ($key > 0)
			// imagefilledrectangle($photo, 200, $start - 50, 1408, $start - 45, $color);
		} else {
			imagettftext($photo, 50, 0, 80, $start + 25, ($nig) ? $colorA[5] : $colorB[5], $f, $timetable['time_id']);
			$aa = explode("-", $dates[$timetable['time_id']]);
			imagettftext($photo, 20, 0, 125, $start-3, ($nig) ? $colorA[5] : $colorB[5], $f, $aa[0]);
			imagettftext($photo, 20, 0, 125, $start + 25, ($nig) ? $colorA[5] : $colorB[5], $f, $aa[1]);
			// if ($key > 0)
			// imagefilledrectangle($photo, 50, $start - 50, 1408, $start - 45, $color);
		}
		$str =
			mb_strimwidth($timetable['pairname'], 0, 37, "...")
			.PHP_EOL
			.mb_strimwidth($timetable['teacher'], 0, 30, "...")
			
			.PHP_EOL.PHP_EOL;
		imagettftext($photo, 25, 0, 320, $start, ($nig) ? $colorA[4] : $colorB[4], $f, $str);
		if ($timetable['sub'] > 0) {
			imagettftext($photo, 20, 0, 1030, $start - 3, ($nig) ? $colorA[0] : $colorB[0], $f, $timetable['sub'].' п/гр.');
		}
		imagettftext($photo, 20, 0, 1030, $start + 25, ($nig) ? $colorA[0] : $colorB[0], $f, mb_strimwidth($timetable['cab'], 0, 6, "."));
		// } 
		// else {
			// imagettftext($photo, 30, 0, 1050, $start + 15, $color, $f, mb_strimwidth($timetable['cab'], 0, 6, "."));
		// }
		if (mb_strtoupper($timetable['types_pair']) == "ЛК" 
			|| mb_strtoupper($timetable['types_pair']) == "ПР"
			|| mb_strtoupper($timetable['types_pair']) == "ЛБ") {
			imagettftext($photo, 50, 0, 205, $start + 25, ($nig) ? $colorA[0] : $colorB[0], $f, mb_strtoupper($timetable['types_pair']));
		} else {
			imagettftext($photo, 50, 0, 205, $start + 25, ($nig) ? $colorA[0] : $colorB[0], $f, mb_strimwidth(mb_strtoupper($timetable['types_pair']), 0, 2, ""));
		}
		$start += 100;
	}
	imagejpeg($photo, __DIR__ . "/".$name.'.jpg');
	// imagedestroy($photo);
	return $name;
}
function photoMakeWeek($name, $a, $name_group, $date_, $date_timetable, $date_1) {
	$dayNow = date("N", strtotime($date_1));
	
	$date = getEvenOrOdd2($date_);
	$days = ['Пн','Вт','Ср', 'Чт','Пт','Сб'];
	$left = [0, 653, 1306];
	$month = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
	$photo = imagecreatefromjpeg(__DIR__ . "/image.jpg");
	$f = __DIR__ . '/PFBeauSansPro-Black.ttf';
	$color = imagecolorallocate($photo, 255, 255, 255);
	$colorA = [
		imagecolorallocate($photo, 13, 69, 64), 
		imagecolorallocate($photo, 13, 31, 69), 
		imagecolorallocate($photo, 52, 13, 69)
		];
	$str = "";
	imagettftext($photo, 50, 0, 850, 80, $color, $f, $name_group);
	imagettftext($photo, 20, 0, 20, 1110, $color, $f, "Расписание загружено с сайта tt.chuvsu.ru 09 декабря 12:00");
	// $days[date("w", strtotime($date_timetable))]
	// .date("j", strtotime($date_timetable)).' '.$month[(date("n", strtotime($date_timetable)) - 1)].' ('
	imagettftext($photo, 40, 0, 50, 80, $color, $f, $date[1].' '.$date[2]);
	if ($date[0] == "**") {
		imagettftext($photo, 240, 0, 1600, 200, $color, $f, "**");
	} else {
		imagettftext($photo, 240, 0, 1700, 200, $color, $f, "*");
	}
	$start = 300;
	if (count($a) < 5) {
		$start = 500;
	} 
	$dates = ['', '08:20-09:40', '09:55-11:15', '11:30-12:50', '13:20-14:40', '14:55-16:15', '16:30-17:50', '18:05-19:25', '19:40-21:00'];
	
    
	$startX = 326;
	$startY = 150;
	// var_dump($a);
// echo 'ok';		die();
	foreach ($a as $key => $day) {
		
		if ($key == 4) {
			$startX = 326;
			$startY = 650;
		}
		imagettftext($photo, 40, 0, $startX - 150 , $startY, $color, $f, 
			$days[$key - 1].' '
			.date('j', strtotime($date_1 . ' + '.($key - 1).' day'))
			.' '.$month[(date("n", strtotime($date_1 . ' + '.($key - 1).' day')) - 1)]);
		$start = $startY;
		
		foreach ($day as $index => $tt) {
			imagefilledrectangle($photo, $startX - 270, $start + 35, $startX - 268, $start + 90, $color);
			imagefilledrectangle($photo, $startX - 175, $start + 35, $startX - 173, $start + 90, $color);
			
			if (($index - 1) != -1 && $day[$index - 1]['time_id'] == $tt['time_id']) {
				// if ($index > 0) 
					imagefilledrectangle($photo, $startX - 180, $start + 80, $startX + 326, $start + 81, $color);
			} else {
				imagettftext($photo, 20, 0, $startX - 260, $start + 60, $color, $f, $tt['time_id']);
				$aa = explode("-", $dates[$tt['time_id']]);
				imagettftext($photo, 10, 0, $startX - 230, $start + 50, $color, $f, $aa[0]);
				imagettftext($photo, 10, 0, $startX - 230, $start + 70, $color, $f, $aa[1]);
				// if ($index > 0) 
					imagefilledrectangle($photo, $startX - 180, $start + 80, $startX + 326, $start + 81, $color);
			}
			$str =
				mb_strimwidth(mb_strtoupper($tt['pairname']), 0, 28, "...")
				.PHP_EOL
				.mb_strimwidth($tt['teacher'], 0, 30, "...")
				
				.PHP_EOL.PHP_EOL;
				imagettftext($photo, 14, 0, $startX - 160, $start + 50, $color, $f, $str);
			if ($tt['sub'] > 0) {
				imagettftext($photo, 15, 0, $startX + 250, $start + 50, $color, $f, $tt['sub'].' п/гр.');
				imagettftext($photo, 15, 0, $startX + 250, $start + 70, $color, $f, mb_strimwidth($tt['cab'], 0, 6, "."));
			} else {
				imagettftext($photo, 15, 0, $startX + 250, $start + 60, $color, $f, mb_strimwidth($tt['cab'], 0, 6, "."));
			}
			
			// imagettftext($photo, 15, 0, $startX + 200, $start + 60, $color, $f, mb_strimwidth(mb_strtoupper($tt['types_pair']), 0, 2, ""));
			if (mb_strtoupper($tt['types_pair']) == "ЛК") {
				imagettftext($photo, 15, 0, $startX + 200, $start + 60, $colorA[0], $f, mb_strimwidth(mb_strtoupper($tt['types_pair']), 0, 2, ""));
			}
			elseif (mb_strtoupper($tt['types_pair']) == "ПР") {
				imagettftext($photo, 15, 0, $startX + 200, $start + 60, $colorA[1], $f, mb_strimwidth(mb_strtoupper($tt['types_pair']), 0, 2, ""));
			}
			elseif (mb_strtoupper($tt['types_pair']) == "ЛБ") {
				imagettftext($photo, 15, 0, $startX + 200, $start + 60, $colorA[2], $f, mb_strimwidth(mb_strtoupper($tt['types_pair']), 0, 2, ""));
			} else {
				imagettftext($photo, 15, 0, $startX + 200, $start + 60, $color, $f, mb_strimwidth(mb_strtoupper($tt['types_pair']), 0, 2, ""));
			}
			$start += 50;
			
		}
		$startX	+= 326*2;
	}

	imagejpeg($photo, __DIR__. "/".$name.'.jpg');
	return $name;
}
// photoMake("ИВТ-41-15", [
// 	["sub"=> 0, "pairname"=>"Алгебра и геометрия", "cab"=>"Г-305", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	["sub"=> 1, "pairname"=>"Профессиональный иностранный языкПрофессиональный иностранный язык", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	["sub"=> 0, "pairname"=>"Информатика", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	["sub"=> 2, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
// 	], "ИВТ-41-15", getEvenOrOdd2("2019-11-12"), "2019-11-12");
