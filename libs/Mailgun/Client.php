<?php
/**
 * Mailer class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

namespace Mailgun;

/**
 * The Mailgun client class file.
 */
class Client {
	
	private $baseUrl = 'https://api.mailgun.net/v2/';
	
	private $domain;
	
	private $user = 'api';
	
	private $password;
	
	private $timeout = 10;
	
	private $verbose = false;
	
	private $ch;
	
	/**
	 * @param array $config
	 *  + domain:
	 *  + password:
	 *  + timeout:
	 * @throws \InvalidArgumentException
	 */
	public function __construct($config) {
		foreach (array('domain', 'password') as $field) {
			if (!isset($config[$field])) {
				throw new \InvalidArgumentException("Lost the '$field' configuration!");
			}
		}
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}
	
	protected function getSendUrl() {
		return $this->baseUrl . $this->domain . '/messages'; 
	}
	
	/**
	 * Get the curl connection.
	 *
	 * @return resource
	 */
	protected function getConnection() {
		if (!$this->ch) {
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			if ($this->verbose) {
				curl_setopt($this->ch, CURLOPT_VERBOSE, true);
			}

		}
		return $this->ch;
	}
	
	/**
	 * Send mail.
	 * 
	 * @param Message $message
	 * @return true
	 * @throws MailerException
	 */
	public function send(Message $message) {
		$ch = $this->getConnection();
		
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->password);
		
		curl_setopt($ch, CURLOPT_URL, $this->getSendUrl());
		
		$data = $message->buildMessage();
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));

		$res = curl_exec($ch);
		
		if ($res === false) {
			throw new MailerException(curl_error($ch), 100);
		}
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode == 200) {
			return true;
		}elseif ($httpCode >= 500) {
			throw new MailerException('Servier error', $httpCode);
		}else {
			$rdata = json_decode($res);
			throw new MailerException($rdata ? $rdata->message : $res, $httpCode);
		}
	}
	
	
	public function __destruct() {
		if ($this->ch != null) {
			curl_close($this->ch);
		}
	}
}

class MailerException extends \Exception {
	
	public function __construct($message, $code) {
		if ($code >= 500) {
			$messages = array(
				'500' => '500 Internal Server Error',
				'502' => '502 Bad Gateway',
				'503' => '503 Service Unavailable',
				'504' => '504 Gateway Timeout',
			);
			$message = $messages[$code];
		}
		parent::__construct($message, $code);
	}
}
