<?php
/*
 * tuentiAPI Class
 * Sergio Cruz aka scromega (scr.omega at gmail dot com) http://scromega.net
 *
 * More info:
 * http://scromega.net/7-accediendo-a-la-api-cerrada-de-tuenti.html
 */

class tuentiAPI {
	private $email, $password, $userData;
	function __construct($email, $password) {
		$this->email = $email;
		$this->password = $password;
		$tmp = $this->json('getChallenge', array('type'=>'login'));
		$tmp = self::http($tmp);
		$tmp = json_decode($tmp, true);
		$passcode = md5($tmp[0]['challenge'].md5($password));
		$appkey = 'MDI3MDFmZjU4MGExNWM0YmEyYjA5MzRkODlmMjg0MTU6MC4xMzk0ODYwMCAxMjYxMDYwNjk2';
		$tmp = $this->json('getSession', array('passcode'=>$passcode,'application_key'=>$appkey,'timestamp'=>$tmp[0]['timestamp'],'seed'=>$tmp[0]['seed'],'email'=>$email));
		$tmp = self::http($tmp);
		$tmp = json_decode($tmp, true);
		$this->userData = $tmp[0];
	}
	private static function http($post) {
		$headers[] = "Content-length: ".strlen($post)."\r\n";
		$headers[] = $post;
		$ch = curl_init('http://api.tuenti.com/api/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		$result = curl_exec($ch);
		if($result === false) die('Cannot execute request: '.curl_error($ch));
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $result;
	}
	private function json($method, $parameters) {
		$array['requests'][0][0] = $method;
		$array['requests'][0][1] = $parameters;
		if(!empty($this->userData['session_id'])) {
			$array['session_id'] = $this->userData['session_id'];
		}
		$array['version'] = '0.4';
		return json_encode($array);
	}
	public function request($method, $parameters=array()) {
		$tmp = $this->json($method, $parameters);
		$tmp = self::http($tmp);
		$tmp = json_decode($tmp, true);
		return $tmp[0];
	}
}
