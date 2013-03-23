<?php
/**
 * Mailgun Message class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

namespace Mailgun;

class Message {
	
	private $sender;
	
	private $recipients;
	
	private $ccs;
	
	private $bccs;
	
	private $content;
	
	private $subject;
	
	private $options;
	
	private $isHtmlMessage;
	
	public function __construct($senderAddress = null, $senderName = null) {
		if ($senderAddress != null) {
			$this->sender = array($senderAddress, $senderName);
		}
	}
	
	/**
	 * Set the sender information.
	 * 
	 * @param string $address  The mail address of the sender.
	 * @param string $name     The sender name.
	 * @return \Mailgun\Message
	 */
	public function setSender($address, $name = null) {
		$this->sender = array($address, $name);
		return $this;
	}
	
	/**
	 * Get the sender information.
	 * 
	 * @return array|null
	 */
	public function getSender() {
		return $this->sender;
	}
	
	/**
	 * Add a recipient to recive the message.
	 * 
	 * @param string $recipients
	 * @return \Mailgun\Message
	 */
	public function addRecipient($address, $name) {
		$this->recipients[] = array($address, $name);
		return $this;
	}
	
	/**
	 * Get all the recipients.
	 * @return array
	 */
	public function getRecipients() {
		return $this->recipients;
	}
	
	
	public function addCcRecipient($address, $name) {
		$this->ccs[] = array($address, $name);
		return $this;
	}
	
	
	public function getCcRecipients() {
		return $this->ccs;
	}
	
	public function addBccRecipient($address, $name) {
		$this->bccs[] = array($address, $name);
		return $this;
	}
	
	public function getBccRecipients() {
		return $this->bccs;
	}
	
	/**
	 * Set the subject of the message.
	 * 
	 * @param string $subject
	 * @return \Mailgun\Message
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}
	
	/**
	 * Get the subject of the message.
	 * 
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}
	
	/**
	 * Set the content of the message.
	 * 
	 * @param string  $text   
	 * @param boolean $isHtml   
	 * @return \Mailgun\Message
	 */
	public function setContent($content, $isHtml = false) {
		$this->content = $content;
		$this->isHtmlMessage = $isHtml;
		return $this;
	}
	
	/**
	 * Get the content of the message.
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * Whether the message is html message.
	 * 
	 * @return boolean
	 */
	public function isHtmlMessage() {
		return $this->isHtmlMessage;
	}
	
	public function setOptions($options) {
		$this->options = $options;
		return $this;
	}
	
	public function getOptions() {
		return $this->options;
	}
	
	/**
	 * Build a array to send use client.
	 * 
	 * @return array
	 */
	public function buildMessage() {
		if (!$this->sender || !$this->recipients) {
			return false;
		}
		list($formAddress, $fromName) = $this->sender;
		$ret = array();
		
		$ret['from'] = sprintf('%s <%s>', $fromName ?: '', $formAddress);
		$ret['to'] = $this->buildRecipients($this->recipients);
		if ($this->bccs) {
			$ret['bcc'] = $this->buildRecipients($this->bccs);
		}
		if ($this->ccs) {
			$ret['cc'] = $this->buildRecipients($this->ccs);
		}
		$ret['subject'] = $this->subject;
		if ($this->isHtmlMessage) {
			$ret['html'] = $this->content;
		}else {
			$ret['text'] = $this->content;
		}
		return $ret;
	}
	
	
	protected function buildRecipients($recipients) {
		$ret = array();
		foreach ($recipients as $recipient) {
			list($address, $name) = $recipient;
			$ret[] = sprintf('%s <%s>', $name, $address);
		}
		return implode(',', $ret);
	}
}
