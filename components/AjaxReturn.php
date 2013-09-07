<?php
/**
 * AjaxReturn class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * Class for render ajax returns.
 *
 */
class AjaxReturn extends CComponent {
	
	const SUCCESS = 0;
	
	/* 400 invalid request */
	const BAD_REQUEST_METHOD     = 40001;
	const BAD_REQUEST_URL        = 40002;
	const PARAM_MISSING_REQUIRED = 40003;
	const PARAM_TYPE_INVALID     = 40004;
	const PARAM_NO_FILE_UPLOADED = 40005;
	
	/* 401 unauthorized */
	const UNAUTHORIZED_OPERATION = 40101;
	
	/* 402 */
	const OPERATION_FAILED = 40201;
	
	/* 403 */
	const PERMISSION_NOT_ALLOWED = 40301;
	
	/* 404 */
	const RESOUCE_NOT_FOUND = 40401;
	
	
	protected $_internalState = array(
		self::SUCCESS => 'Success',
		
		self::BAD_REQUEST_METHOD => 'Bad request method',
		self::BAD_REQUEST_URL    => 'Bad request url',
		self::PARAM_MISSING_REQUIRED => 'Param missing required',
		self::PARAM_TYPE_INVALID     => 'Param type invalid',
		self::PARAM_NO_FILE_UPLOADED => 'No file uploaded',
		
		self::UNAUTHORIZED_OPERATION => 'Unauthorized',
		
		self::OPERATION_FAILED       => 'Failed',
		
		self::PERMISSION_NOT_ALLOWED => 'Permission not allowed',
		
		self::RESOUCE_NOT_FOUND      => 'Resouce not found',
	);
	
	private $_code = 0;
	
	private $_msg;
	
	private $_data;
	
	public function setCode($code) {
		$this->_code = $code;
		return $this;
	}
	
	public function setMsg($msg) {
		$this->_msg = $msg;
		return $this;
	}
	
	public function setData($data) {
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * End the application and send data to client.
	 */
	public function send(){
		$return = array();
		$return['code'] = $this->_code;
		$return['msg'] = $this->_msg ?: 
			(isset($this->_internalState[$this->_code]) ? $this->_internalState[$this->_code] : '');
		if ($this->_data) {
			$return['data'] = $this->_data;
		}
		
		header('Content-Type: application/json');
		echo json_encode($return);
		Yii::app()->end();
	}
}
