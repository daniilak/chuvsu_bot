<!DOCTYPE HTML>
<html>
 <head>
  <meta charset="utf-8">
  <title>Для группы вуза</title>
 </head>
 <body>
 	<center>
 	<p><b>Штука, чтобы был включен автолайк:)</b></p><h4>
<?php
$a = file_get_contents('test.txt');
$ar = explode('
', $a);
if (isset($_GET['inst']))
{
	$bool = 0;
	$kek = 0;
	$tmp = explode("&expires_in", trim(strip_tags($_GET['inst'])));
	if (isset($tmp[1])) {
		$tmp = explode("access_token=", $tmp[0]);
		$kek = $tmp[1];
	}
	if ($kek != 0) {
	
	// $ins = str_replace("@","",trim(strip_tags($_GET['inst'])) );
	// var_dump($ins);
	// die();
	foreach ($ar as $acc) {
		if ($kek == $acc) {
			$bool = 1;
			break;
		}
	}
	if ($bool != 1) {
		file_put_contents('test.txt', '
'.$kek, FILE_APPEND);

	echo "СПАСИБО ВСЁ СДЕЛАНО<br>СПАСИБО ВСЁ СДЕЛАНО<br>";
	}
}

// $a = file_get_contents('test.txt');
// $ar = explode('
// ', $a);
// foreach ($ar as $acc)
// {
// 	echo "<a target='_blank' href='https://instagram.com/{$acc}'>{$acc}</a><br>";
// }
}
?>
</h4>
 <form action="index.php">
  <p><b>1) Перейдите по ссылке <a href="https://oauth.vk.com/authorize?client_id=6121396&scope=73728&redirect_uri=https://oauth.vk.com/blank.html&display=page&response_type=token&revoke=1" target="_blank">ЗДЕСЬ</a></b></p>
  <p><b>2) Нажмите разрешить </b></p>
  <p><b>3) Скопируйте из адресной строки весь текст </b></p>
  <p><b>3) Вставьте сюда: </b></p>
  <p><input type="text" name="inst"></p>
  <p><input type="submit"></p>
 </form>
</center>
 </body>
</html>


