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
	
	protected $_internalState = array(
		'0'=>'Success',
		
		/*request arguments errors*/
		'100'=>'Wrong Arguments Applied',
		'101'=>'',
		
		/*permission errors*/
		'200'=>'user not logged in',
		'201'=>'have ',
		
		/*application logic errors 3xx*/
		
		/*other errors*/
		'400'=>'unexpected error',
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
