<?php

class Authenticate
{

	public $channel;

	public function __construct($channel)
	{
		$this->channel = $channel;
	}

	public function Login($username, $password)
	{
		$url = 'https://accounts.majorleaguegaming.com/session';
		$data = array('authenticity_token' => md5(uniqid($username, true)), 'login' => $username, 'view_type' => 'mini', 'password' => $password, 'remember_me' => 'yes', 'login_button' => 'Log+in', 'channel_id' => $this->channel);

		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    ),
		);
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$return = array();

		foreach($http_response_header as $setcookies) {
            if (strpos($setcookies, 'mlg_login=;') !== false) {
                die("ERROR: Login Credentials seem to be incorrect!\n");
            }

            preg_match('~mlg_login=(.*?);~', $setcookies, $output);
            if(isset($output[1])) {
                $return['mlg_login'] = $output[1];
            }

            preg_match('~mlg_id=(.*?);~', $setcookies, $output);
            if(isset($output[1])) {
                $return['mlg_id'] = $output[1];
            }

            preg_match('~account-token=(.*?);~', $setcookies, $output);
            if(isset($output[1])) {
                $return['account_token'] = $output[1];
            }

            preg_match('~remember_me=(.*?);~', $setcookies, $output);
            if(isset($output[1])) {
                $return['remember_me'] = $output[1];
            }
		}

		return $return;
	}

	public function joinChannel($mod, $color, $remember_me, $account_token, $mlg_id, $mlg_login)
	{
		if($mod == true){
			$mod = '3';
		} else {
			$mod = '0';
		}

		$ch1 = curl_init("https://chat.majorleaguegaming.com/$this->channel/user_joined");
		$cook = 'Cookie: remember_me='.$remember_me.'; mlg_login='.$mlg_login.'; mlg_id='.$mlg_id.'; _steam_id=; mlg_streaming_partner=; _mlg_follows=; mlg_follows=; account-token='.$account_token.'; _mlg_subscribes=';
		curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch1, CURLOPT_HTTPHEADER,array($cook));
		$result1 = curl_exec($ch1);

		$return = array();
		$return['SendData'] = 'user=%7B%22uuid%22%3A%22'.$account_token.'%22%2C%22mlg_id%22%3A'.$mlg_id.'%2C%22username%22%3A%22'.$mlg_login.'%22%2C%22entitlements%22%3A%5B%5D%7D&moderator_role='.$mod.'&username_color='.$color.'&icon_id=0&message=';
		$return['Cookie'] = 'chat_user=%7B%22uuid%22%3A%22'.$account_token.'%22%2C%22mlg_id%22%3A'.$mlg_id.'%2C%22username%22%3A%22'.$mlg_login.'%22%2C%22entitlements%22%3A%5B%5D%2C%22moderator_role%22%3A'.$mod.'%2C%22is_naughty%22%3Afalse%2C%22username_color%22%3A%22'.$color.'%22%2C%22icon_id%22%3A0%7D; _mlg_follows=; mlg_login='.$mlg_login.'; mlg_id='.$mlg_id;

		return $return;
	}

	public function sendMsg($msg, $SendData, $Cookie)
	{
		$data = $SendData.urlencode($msg);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://chat.majorleaguegaming.com/$this->channel/send_message");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($Cookie));
		$result = curl_exec($ch);
		return $result;
		curl_close($ch);
	}
}
