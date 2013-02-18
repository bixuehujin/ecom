<?php

class Console extends CApplicationComponent {

	const TYPE_ERROR 	= 'error';
	const TYPE_SUCCESS 	= 'success';
	const TYPE_INFO		= 'info';
	const TYPE_WARNING 	= 'warning';
	/**
	 * instance of CHttpSeccion
	 * @var CHttpSession
	 */
	private $_session;
	/**
	 * reference to $_SESSION[$this->sessionKey]
	 * @var array
	 */
	private $_messages;
	
	private $_models = array();
	/**
	 * @var callable
	 */
	private $_htmlRenderer = array('CHtml', 'errorSummary');
	
	/**
	 * index used to store infomation to $_SESSION
	 * @var string
	 */
	public $sessionKey;
	
	/**
	 * initalizes variables.
	 */
	public function __construct() {
		$this->_session = Yii::app()->session;
		if($this->sessionKey === null) {
			$this->sessionKey = __CLASS__;
		}
		$this->_messages = &$this->getStorage();
	}
	
	
	/**
	 * get a reference of session storage.
	 *
	 * @return array
	 */
	protected function &getStorage() {
		if (!($message = $this->_session->get($this->sessionKey, false))) {
			$this->_session->add($this->sessionKey, array());
		}
		return $_SESSION[$this->sessionKey];
	}
	
	/**
	 * set a function to render model error summary.
	 * 
	 * @param callable $renderer
	 */
	public function setRenderer($renderer) {
		if (is_callable($renderer)) {
			$this->_htmlRenderer = $renderer;
		}else {
			$this->_htmlRenderer = array('CHtml', 'errorSummary');
		}
	}
	
	/**
	 * add a message to session.
	 *
	 * @param string $message
	 * @param string $type
	 * @return Console
	 */
	public function add($message, $type) {
		$this->_messages[$type][] = $message;
		return $this;
	}
	
	/**
	 * 
	 * @param object $model
	 * @return Console
	 */
	public function addModel($model) {
		$this->_models[] = $model;
		return $this;
	}
	
	/**
	 * remove all or a certain type of messages.
	 *
	 * @param mixed $type the type of messages to remove, null for all
	 */
	public function remove($type = null) {
	
		if ($type === null) {
			$this->_messages = array();
		}else {
			unset($this->_messages[$type]);
		}
	}
	
	/**
	 * get all or a certain type of persistent messages.
	 *
	 * @param mixed $type the type of messages to get, null for all
	 * @return array
	 */
	public function getAll($type = null){
	
		$ret = array();
		if($type == null) {
			$ret = $this->_messages;
		}
		if(isset($this->_messages[$type])) {
			$ret = $this->_messages[$type];
		}
	
		$this->remove($type);
		return $ret;
	}
	
	/**
	 * add persistent error message.
	 *
	 * @param string $message
	 */
	public function addError($message) {
		$this->add($message, self::TYPE_ERROR);
	}
	
	/**
	 * get all error messages.
	 *
	 * @return array
	 */
	public function getErrors() {
		return $this->getAll(self::TYPE_ERROR);
	}
	
	/**
	 * addd persistent info message.
	 *
	 * @param string $message
	 */
	public function addInfo($message) {
		$this->add($message, self::TYPE_INFO);
	}
	
	/**
	 * get persistent info messages.
	 *
	 * @return array
	 */
	public function getInfos() {
		return $this->getAll(self::TYPE_INFO);
	}
	
	/**
	 * add persistent warning message.
	 *
	 * @param string $message
	 */
	public function addWarning($message) {
		$this->add($message, self::TYPE_WARNING);
	}
	
	/**
	 * get persistent warning messages.
	 *
	 * @return array
	 */
	public function getWarnings() {
		return $this->getAll(self::TYPE_WARNING);
	}
	
	/**
	 * add persistent success message.
	 *
	 * @param string $message
	 */
	public function addSuccess($message) {
		$this->add($message, self::TYPE_SUCCESS);
	}
	
	/**
	 * get persistent success messages.
	 *
	 * @return array
	 */
	public function getSuccesses() {
		return $this->getAll(self::TYPE_SUCCESS);
	}
	
	/**
	 * get the number of error messages.
	 *
	 * @return number
	 */
	public function errorCount() {
		return isset($this->_messages[self::TYPE_ERROR])
		? count($this->_messages[self::TYPE_ERROR]) : null;
	}
	
	/**
	 * get the number of success messages.
	 *
	 * @return number
	 */
	public function successCount() {
		return isset($this->_messages[self::TYPE_SUCCESS])
		? count($this->_messages[self::TYPE_SUCCESS]) : null;
	}
	
	/**
	 * get the number of warning messages.
	 *
	 * @return number
	 */
	public function warningCount() {
		return isset($this->_messages[self::TYPE_WARNING])
		? count($this->_messages[self::TYPE_WARNING]) : null;
	}
	
	/**
	 * get the number of info messages.
	 *
	 * @return number
	 */
	public function infoCount() {
		return isset($this->_messages[self::TYPE_INFO])
		? count($this->_messages[self::TYPE_INFO]) : null;
	}
	
	/**
	 * whether have message.
	 *
	 * @return boolean
	 */
	public function getHasMessages() {
		return !empty($this->_messages);
	}
	
	/**
	 * render messages to HTML.
	 *
	 * @param array $options array indexed by error, warning, info, success and model.
	 * the value is html options used to render messages widgets.
	 */
	public function render($options = array()) {
		
		$ret = '';
		if ($this->successCount()) {
			$ret .= $this->_renderMessage($this->getSuccesses(), 'success',
					isset($options['success']) ? $options['success'] : array());
		}
		if ($this->infoCount()) {
			$ret .= $this->_renderMessage($this->getInfos(), 'info',
					isset($options['info']) ? $options['info'] : array());
		}
		if($this->warningCount()) {
			$ret .= $this->_renderMessage($this->getWarnings(), 'warning',
					isset($options['warning']) ? $options['warning'] : array());
		}
		if($this->errorCount()) {
			$ret .= $this->_renderMessage($this->getErrors(), 'error',
					isset($options['error']) ? $options['error'] : array());
		}
		
		foreach ($this->_models as $model) {
			$ret .= call_user_func($this->_htmlRenderer, $model, null, null,
					isset($options['model']) ? $options['model'] : array('class'=>'alert alert-error'));
		}
		
		echo $ret;
	}
	
	private function _renderMessage($messages, $type, $htmlOptions = array()) {
		if (!isset($htmlOptions['class'])) {
			$htmlOptions['class'] = 'alert alert-' . $type;
		}
		$html = CHtml::openTag('div', $htmlOptions);
		if (count($messages) > 1) {
			$html .= CHtml::openTag('ul');
			foreach ($messages as $message) {
				$html .= '<li>' . $message . '</li>';
			}
			$html .= '</ul>';
		}else {
			$html .= array_shift($messages);
		}
		return $html . '</div>';
	}
	
}
