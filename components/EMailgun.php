<?php
/**
 * EMailgun class file.
 * 
 * @author Jin Hu <bxieuhujin@gmail.com>
 */

Yii::setPathOfAlias('Mailgun', Yii::getPathOfAlias('ecom.libs.Mailgun'));

use Mailgun\Client;
use Mailgun\Message;
use Mailgun\MailerException;

class EMailgun extends CApplicationComponent {
	
	/**
	 * The config array for mailgun client.
	 * 
	 * @var array
	 */
	private $config;
	
	/**
	 * The default sender.
	 * 
	 * @var string
	 */
	private $defaultSender;
	
	/**
	 * @var \Mailgun\Client
	 */
	private $client;
	
	/**
	 * Create a new Message.
	 * 
	 * @return \Mailgun\Message
	 */
	public function createMessage() {
		$message = new Message();
		if ($this->defaultSender != null) {
			$message->setSender($this->defaultSender);
		}
		return $message;
	}
	
	
	/**
	 * 
	 * @param Message $message
	 * 
	 * @return boolean
	 */
	public function send(Message $message) {
		try {
			$this->getClient()->send($message);
			return true;
		}catch (MailerException $e) {
			//To something there.
			return false;
		}
	}
	
	
	public function getClient() {
		if ($this->client === null) {
			$this->client = new Client($this->config);
		}
		return $this->client;
	}
	
	/**
	 * Set the config used by Mailgun Client.
	 * 
	 * @param array $config
	 * @throws CException
	 * @return EMailgun
	 */
	public function setConfig($config) {
		foreach (array('domain', 'password') as $field) {
			if (!isset($config[$field])) {
				throw new CException("Mailgun require the '$field' config to work well!");
			}
		}
		$this->config = $config;
		return $this;
	}
	
	/**
	 * Get the configuraton.
	 * 
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * Set the default sender.
	 * 
	 * @param string $sender  The default sender, such as 'Commelp <service@commelp.com>'
	 * @return EMailgun
	 */
	public function setDefaultSender($sender) {
		$this->defaultSender = $sender;
		return $this;
	}
	
	public function getDefaultSender() {
		return $this->defaultSender;
	}
}
