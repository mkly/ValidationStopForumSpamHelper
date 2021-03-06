<?php
/**
* Stop Forum Spam Helper
* A concrete5 Helper to check against stopforumspam.com db
*
* @author Mike Lay
* @copyright 2013 Mike Lay
* @link http://github.com/mkly/ValidationStopForumSpamHelper
* @license MIT
*/
defined('C5_EXECUTE') or die('Access Denied.');

if (!defined('STOP_FORUM_SPAM_URL')) {
	define('STOP_FORUM_SPAM_URL', 'http://stopforumspam.com/api');
}

class ValidationStopForumSpamHelper {

	protected $response;
	protected $score     = 0;
	protected $min_score = 0;
	protected $username  = '';
	protected $ip        = '';
	protected $email     = '';

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
		$this->scoreResponse();
		$this->reset();
		if($this->isSpam()) {
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

	protected function scoreResponse() {
		$this->score = $this->response->username->appears +
			$this->response->email->apprears +
			$this->response->ip->appears;
	}

	protected function isSpam() {
		return ($this->score >= $this->min_score ? true : false);
	}

	protected function reset() {
		$response  = null;
		$score     = 0;
		$min_score = 0;
		$username  = '';
		$ip        = '';
		$email     = '';
	}

}
