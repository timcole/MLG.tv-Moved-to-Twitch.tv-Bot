<?php

class Chat
{

	private $channelid;
	private $redis;
	private $twitch;

	public function __construct($channelid, $twitch, $username)
	{
		$this->channelid = $channelid;
		$this->redis = new Redis();
		$this->redis->connect("localhost", 6379);

		$this->twitch = $twitch;
		$this->username = $username;
	}

	public function getChat()
	{
		$MLGChat = json_decode(file_get_contents('https://chat.majorleaguegaming.com/'.$this->channelid.'/messages'), true);

		if($this->getLastTimestamp() != $MLGChat['timestamp']){
			$this->storeTimestamp($MLGChat['timestamp']);
			foreach($MLGChat['messages'] as $chat) {
				return $this->checkMessage($this->parseChat($chat));
			}
		}
	}

	private function checkMessage($message)
	{
		echo $message['username'].' - '.$message['message']."\n";
		if($message['username'] != $this->username) {
			return array('Reply' => "@".$message['username'].", I've moved to TWlTCH! - https://tcole.me/ttv/$this->twitch");
		}
	}

	private function parseChat($message)
	{
		return json_decode($message, true);
	}

	private function storeTimestamp($timestamp)
	{
		$this->redis->set('ChatBot'.$this->channelid, $timestamp);
	}

	private function getLastTimestamp()
	{
		return $this->redis->get('ChatBot'.$this->channelid);
	}

}

?>
