<?php

require_once('config.php');

$data = json_decode(file_get_contents('php://input')); 
if ($data->type == 'confirmation')
{
	echo $GLOBALS['confirmation_token']; 
    die();
}
if ($data->type  != 'wall_post_new')
{
	echo 'ok';
	die();
}
// if ($data->object->from_id == 415869238)
$N = intval(file_get_contents('index.txt', true));
$N++;
$user_id = [1=>257358123,2=>196618105,3=>458081273]; 
if ($N > count($user_id))
	$N = 1;
//echo $N;
file_put_contents('index.txt', $N);


if ($data->object->post_type == "suggest") {
	// $answer = urlencode("Новая запись. Опубликовать в теч. дня: https://vk.com/wall-47825810_".$data->object->id);
	// requestVkAPI("messages.send", "user_id=".$user_id[$N]."&message={$answer}");
	echo "ok";
	die();
} 
if ($data->object->from_id == 221169992)
{
    $id = $data->object->id;
	requestVkAPI("wall.delete", "owner_id=-47825810&post_id={$id}",1);
	echo "ok";
	die();
}
if (isset($data->object->copy_history))
{
  	$id = $data->object->id;
  	requestVkAPI("wall.delete", "owner_id=-47825810&post_id={$id}",1);
   	echo "ok";
   	die();
}
$id = $data->object->id;
$a = file_get_contents('scopes/test.txt');
$ar = explode('
', $a);
foreach ($ar as $access) {
	if (strlen($access) > 0) {
		requestVkAPI("likes.add", "type=post&owner_id=-47825810&item_id={$id}",$access);
		usleep(400000);
	}
}
   		


echo "ok";
die();  