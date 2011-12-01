<?php

defined('C5_EXECUTE') or die('Access Denied.');

define('STOP_FORUM_SPAM_URL', 'http://stopforumspam.com/api');

class ValidationStopForumSpamHelper {

	protected $response;
	protected $score = 0;
	protected $min_score = 0;
	protected $username = '';
	protected $ip = '';
	protected $email = '';

	public function check($username = false, $email = false, $ip = false, $min_score = false) {
		if($username) $this->username = $username;
		if($ip) $this->ip = $ip;
		if($email) $this->email = $email;
		if($min_score === false) {
			if($username) $this->min_score++;
			if($ip) $this->min_score++;
			if($username) $this->min_score++;
		}
	
		$this->getResponse();
		$this->addResponse();
		if($this->is_spam()) {
			return false;
		} else {
			return true;
		}
	}	

	protected function getResponse() {
		$uh = Loader::helper('url');
		$url = $uh->buildQuery(
			STOP_FORUM_SPAM_URL,
			array(
			'ip' => $this->ip,
			'email' => $this->email,
			'username' => $this->username,
			'f' => 'json')
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$json_response = curl_exec($ch);
		curl_close($ch);

		$this->response = json_decode($json_response);
	}

	protected function addResponse() {
		$this->score = $this->response->username->appears +
			$this->response->email->apprears +
			$this->response->ip->appears;
	}

	protected function is_spam() {
		return ($this->score >= $this->min_score ? true : false);
	}

}

?>
