<?php
/**
 * ApiRequest class file.
 *
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since 2013-07-01
 */

/**
 * $guarder = new ApiRequest();
 * $guarder->setRules($rules);
 * $guarder->setAttributes($attributes);
 * if ($guarder->valitate()) { //success
 *   //business logic...
 * }else {
 * 	 $guarder->renderError();
 * }
 *
 * 
 */
class ApiRequest extends CModel {
	
	const STATUS_OK              = 200;
	const STATUS_INVALID_REQUEST = 400;
	const STATUS_UNAUTHORIZED    = 401;
	const STATUS_FAILED          = 402;
	const STATUS_FORBIDDEN       = 403;
	const STATUS_NOT_FOUND       = 404;
	
	/* 400 */
	const CODE_BAD_REQUEST_METHOD     = 40001;
	const CODE_BAD_REQUEST_URL        = 40002;
	const CODE_MISSING_REQUIRED_PARAM = 40003;
	const CODE_INVALID_PARAM_TYPE     = 40004;
	const CODE_NO_FILE_UPLOADED       = 40005;
	
	/* 401 */
	const CODE_UNAUTHORIZED_OPERATION = 40101;
	
	/* 402 */
	const CODE_OPERATION_FAILED = 40201;
	
	/* 403 */
	const CODE_PERMISSION_NOT_ALLOWED = 40301;
	
	/* 404 */
	const CODE_OPERATION_RESOUCE_NOT_FOUND = 40401;
	
	
	private $_attributeNames;
	private $_attributes;
	private $_rules = array();
	
	private $_code;
	private $_message;
	
	private $_allowMethod = array('GET');
	private $_allowRole = 'all';
	
	/**
	 * Set the validation rules.
	 * 
	 * @param array $rules
	 */
	public function setConfigure($configure) {
		if (isset($configure['rules'])) {
			$this->_rules = $configure['rules'];
		}
		if (isset($configure['allowMethod'])) {
			$this->setAllowMethod($configure['allowMethod']);
		}
		if (isset($configure['allowRole'])) {
			$this->_allowRole = $configure['allowRole'];
		}
	}
	
	/**
	 * @see CModel::rules()
	 */
	public function rules() {
		return $this->_rules;
	}
	
	/**
	 * Returns whether has error.
	 * 
	 * @return boolean
	 */
	public function hasError() {
		return $this->_code !== null || $this->_message !== null;
	}
	
	/**
	 * Clear the error code and message.
	 */
	public function clearError() {
		$this->_code = null;
		$this->_message = null;
	}
	
	/**
	 * Get the error message.
	 * 
	 * @return string
	 */
	public function getMessage() {
		return $this->_message;
	}
	
	/**
	 * Get the error code.
	 * 
	 * @return integer
	 */
	public function getCode() {
		return $this->_code;
	}

	/**
	 * Set the allow method of the current request.
	 * 
	 * @param mixed $method
	 */
	public function setAllowMethod($method) {
		if (is_string($method)) {
			$this->_allowMethod = explode(',', str_replace(' ', '', $method));
		}else {
			$this->_allowMethod = $method;
		}
	}
	
	/**
	 * Get the allow method of the current request.
	 * 
	 * @return array
	 */
	public function getAllowMethod() {
		return $this->_allowMethod;
	}
	
	/**
	 * @see CModel::attributeNames()
	 */
	public function attributeNames() {
		if ($this->_attributeNames === null) {
			return $this->_attributeNames = $this->getSafeAttributeNames();
		}
		return $this->_attributeNames;
	}
	
	/**
	 * @see CModel::setAttributes()
	 */
	public function setAttributes($values, $safeOnly = true) {

	}
	
	protected function getRequestParam($name) {
		static $request;
		if (!$request) {
			$request = Yii::app()->getRequest();
		}
		foreach ($this->_allowMethod as $method) {
			switch ($method) {
				case 'GET':
					return $request->getQuery($name);
				case 'POST':
					return $request->getPost($name);
				case 'PUT':
					return $request->getPut($name);
				case 'DELETE':
					return $request->getDelete($name);
			}
		}
	}
	
	/**
	 * @see CModel::beforeValidate()
	 */
	protected function beforeValidate() {
		$requestType = Yii::app()->request->getRequestType();
		if (!in_array($requestType, $this->_allowMethod)) {
			$this->_code = 40001;
			$this->_message = Yii::t('yii', 'The request method {method} is invalid, only allow {allowMethod}.', 
				array('{method}' => $requestType, '{allowMethod}' => implode(', ', $this->_allowMethod)));
			return false;
		}
		if ($this->_allowRole === 'registered user') {
			if (Yii::app()->user->getIsGuest()) {
				$this->_code = self::CODE_UNAUTHORIZED_OPERATION;
				$this->_message = 'Authentication failed: user not logged in.';
				return false;
			}
		} else if ($this->_allowRole !== 'all') {
			if (!Yii::app()->user->checkAccess($this->_allowRole)) {
				$this->_code = self::CODE_PERMISSION_NOT_ALLOWED;
				$this->_message = 'Permission denied: required role' . $this->_allowRole;
				return false;
			}
		}
		return parent::beforeValidate();
	}
	
	/**
	 * @see CModel::getAttributes()
	 */
	public function getAttributes($names = null) {
		
		$values = &$this->_attributes;
		foreach($this->attributeNames() as $name) {
			if (!isset($this->_attributes[$name])) {
				$values[$name] = $this->getRequestParam($name);
			}
		}

		if(is_array($names)) {
			$values2 = array();
			foreach($names as $name) {
				$values2[$name] = isset($values[$name]) ? $values[$name] : null;
			}
			return $values2;
		}else {
			return $values;
		}
	}
	
	/**
	 * The error handler error message from validators.
	 */ 
	public function errorHandler($validator, $attribute, $message) {
		$this->_code = $validator->getState('code');
		$this->_message = $message;
	}
	
	/**
	 * @see CModel::validate()
	 */
	public function validate($attributes=null, $clearError=true) {
		if($clearError) {
			$this->clearError();
		}
		if($this->beforeValidate()) {
			foreach($this->getValidators() as $validator) {
				$validator->validate($this,$attributes);
				if ($this->hasError()) {
					break;
				}
			}
			$this->afterValidate();
			return !$this->hasError();
		}else {
			return false;
		}
	}
	
	/**
	 * @see CModel::createValidators()
	 */
	public function createValidators() {
		$validators=new CList;
		foreach($this->rules() as $rule) {
			if(isset($rule[0],$rule[1]))  // attributes, validator name
				$validators->add(CValidator::createValidator($rule[1],$this,$rule[0],array_slice($rule,2), array('code'), array($this, 'errorHandler')));
			else
				throw new CException(Yii::t('yii','{class} has an invalid validation rule. The rule must specify attributes to be validated and the validator name.',
						array('{class}'=>get_class($this))));
		}
		
		return $validators;
	}
	
	/**
	 * @see CModel::getAttributeLabel()
	 */
	public function getAttributeLabel($attribute) {
		return $attribute;
	}
	
	/**
	 * @see CComponent::__get()
	 */
	public function __get($name) {
		$attributes = $this->getAttributes();
		if (isset($attributes[$name])) {
			return $attributes[$name];
		}else if (in_array($name, $this->attributeNames())) {
			return null;
		}else {
			return parent::__get($name);
		}
	}
	
	/**
	 * @see CComponent::__set()
	 */
	public function __set($name, $value) {
		if (in_array($name, $this->attributeNames())) {
			$this->_attributes[$name] = $value;
		}else {
			parent::__set($name, $value);
		}
	}
	
	/**
	 * @see CComponent::__isset()
	 */
	public function __isset($name) {
		$attributes = $this->getAttributes();
		if (isset($attributes[$name])) {
			return true;
		}else if (in_array($name, $this->attributeNames())) {
			return false;
		}else {
			return parent::__isset($name);
		}
	}
	
	/**
	 * @see CComponent::__unset()
	 */
	public function __unset($name) {
		if (isset($this->_attributes[$name])) {
			$this->_attributes[$name] = null;
		}else {
			parent::__unset($name);
		}
	}
}

