<?php

require_once('core.php');

$settings = (object) [
 'confirmation_token' => '205adf3e', 
 'token' => 'f17506e13c78c30bc6bc15514a1b0ecbcbf87f5e2296cd6835ccc4c8c5f1926ba9e6b2c56f212026b9157',
 'utoken' => 'b8c4be4bd29bf90c8ec0ff7b0c5fa2975a665a3ba339be2a2e357acee36bb61ec4b6fa17adf798d784caa',
 'data' => json_decode(file_get_contents('php://input'))
];

$vk = new bot_vk($settings);
$vk->start();

?>
