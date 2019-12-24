<?php

header('Content-type: image/png');
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
		return (intval($typeWeek) == 2) ? ['**','чётная неделя','№ '.$num]  : ['*','нечётная неделя','№ '.$num];
	}
function photoMakeWeek($name, $a, $name_group, $date, $date_timetable) {
	$date = getEvenOrOdd2($date);
	$days = ['Пн','Вт','Ср', 'Чт','Пт','Сб'];
	$left = [0, 653, 1306];
	$month = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
	$photo = imagecreatefromjpeg(__DIR__ . "/image.jpg");
	$f = __DIR__ . '/PFBeauSansPro-Black.ttf';
	$color = imagecolorallocate($photo, 255, 255, 255);
	$str = "";
	imagettftext($photo, 50, 0, 850, 80, $color, $f, $name_group);
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
	
    $kfu = ['', '8.30-10.00', '10.10-11.40', '11.50-13.20', '14.00-15.30', '15.40-17.10', '17.20-18.50', '19.00-20.30', '?)'];
    
	$startX = 326;
	$startY = 150;
	foreach ($a as $key => $day) {
		
		if ($key == 3) {
			$startX = 326;
			$startY = 650;
		}
		imagettftext($photo, 40, 0, $startX , $startY, $color, $f, $days[$key]);
		$start = $startY;
		foreach ($day as $index => $tt) {
			imagefilledrectangle($photo, $startX - 250, $start + 35, $startX - 248, $start + 90, $color);
			imagefilledrectangle($photo, $startX - 180, $start + 35, $startX - 178, $start + 90, $color);
			
			if (($index - 1) != -1 && $a[$index - 1]['time_id'] == $timetable['time_id']) {
				if ($index > 0) 
					imagefilledrectangle($photo, $startX - 180, $start + 80, $startX + 326 - 50, $start + 82, $color);
			} else {
				imagettftext($photo, 15, 0, $startX - 240, $start + 60, $color, $f, '#'.$timetable['time_id']);
				$aa = explode("-", $dates[$timetable['time_id']]);
				imagettftext($photo, 15, 0, $startX - 230, $start + 40, $color, $f, $aa[0]);
				imagettftext($photo, 15, 0, $startX - 230, $start + 65, $color, $f, $aa[1]);
				if ($index > 0) 
					imagefilledrectangle($photo, $startX - 180, $start + 80, $startX + 326 - 50, $start + 82, $color);
			}
			$str =
				mb_strimwidth($tt['pairname'], 0, 37, "...")
				.PHP_EOL
				.mb_strimwidth($tt['teacher'], 0, 30, "...")
				
				.PHP_EOL.PHP_EOL;
				imagettftext($photo, 15, 0, $startX - 160, $start + 50, $color, $f, $str);
			if ($timetable['sub'] > 0) {
				imagettftext($photo, 15, 0, $startX + 200, $start + 10, $color, $f, $timetable['sub'].' п/гр.');
				imagettftext($photo, 15, 0, $startX + 200, $start + 30, $color, $f, mb_strimwidth($timetable['cab'], 0, 6, "."));
			} else {
				imagettftext($photo, 15, 0, $startX + 200, $start + 60, $color, $f, mb_strimwidth($timetable['cab'], 0, 6, "."));
			}
			
			imagettftext($photo, 15, 0, $startX + 180, $start + 60, $color, $f, mb_strimwidth(mb_strtoupper($timetable['types_pair']), 0, 2, ""));
			
			$start += 50;
			
		}
		$startX	+= 326*2;
	}

	imagejpeg($photo, __DIR__. "/".$name.'.jpg');
	return $name;
}
$a = [[
	["sub"=> 0, "pairname"=>"АААА", "cab"=>"Г-305", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 1, "pairname"=>"Профессиональный иностранный языкПрофессиональный иностранный язык", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Информатика", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 2, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	], [
	["sub"=> 0, "pairname"=>"ББББ", "cab"=>"Г-305", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 1, "pairname"=>"Профессиональный иностранный языкПрофессиональный иностранный язык", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Информатика", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 2, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	], [
	["sub"=> 0, "pairname"=>"ВВВВ", "cab"=>"Г-305", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 1, "pairname"=>"Профессиональный иностранный языкПрофессиональный иностранный язык", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Информатика", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 2, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	], [
	["sub"=> 0, "pairname"=>"ДДДД", "cab"=>"Г-305", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 1, "pairname"=>"Профессиональный иностранный языкПрофессиональный иностранный язык", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Информатика", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 2, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	],[
	["sub"=> 0, "pairname"=>"Алгебра и геометрия", "cab"=>"Г-305", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 1, "pairname"=>"Профессиональный иностранный языкПрофессиональный иностранный язык", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Информатика", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 2, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	],[
	["sub"=> 0, "pairname"=>"Алгебра и геометрия", "cab"=>"Г-305", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 1, "pairname"=>"Профессиональный иностранный языкПрофессиональный иностранный язык", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Информатика", "cab"=>"Г-355", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 2, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	// ["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	["sub"=> 0, "pairname"=>"Педагогическая деятельность", "cab"=>"Г-05", "teacher"=>"доцент к.б.н Алексеев А.А."],
	]];
photoMake("ИВТ-41-15", $a, "ИВТ-41-15", getEvenOrOdd2("2019-11-12"), "2019-11-12");
