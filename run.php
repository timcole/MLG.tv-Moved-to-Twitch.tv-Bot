<?php

require 'classes/chat.class.php';
require 'classes/authenticate.class.php';
require 'login_credentials.php';

global $Chat;
global $Authenticate;
$Chat = new Chat($channel, $twitch, $username);
$Authenticate = new Authenticate($channel);

while(true)
{
	checkChat($Chat->getChat(), $username, $password, $mod, $color);
	sleep(2);
}

function checkChat($message, $username, $password, $mod, $color)
{
	global $Chat;
	global $Authenticate;

	$Login = $Authenticate->Login($username, $password);

	$joinChannel = $Authenticate->joinChannel($mod, $color, $Login['remember_me'], $Login['account_token'],  $Login['mlg_id'],  $Login['mlg_login']);

	if(isset($message['Reply'])) {
		if($Authenticate->sendMsg($message['Reply'], $joinChannel['SendData'], $joinChannel['Cookie']) != "OK") {
			echo "Message Failed to Send!\n";
		}
	}
}

?>
