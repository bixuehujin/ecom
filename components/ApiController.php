<?php
/**
 * ApiController class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since 2013-07-02
 */

class ApiController extends CController {
	
	protected $request;
	protected $api;
	
	public function init() {
		$this->request = Yii::app()->getRequest();
		$this->api = new ApiRequest();
	}
	
	/**
	 * @return array
	 *   array(
	 *     'actionId' => array(
	 *     	 'rules' => array(
	 *         array('configure array for buildin validator'),
	 *       ),
	 *       'allowMethod' => array('GET', 'POST'),
	 *       'allowRole' => 'all',//all, registered user, 
	 *     ),
	 *     ...
	 *   )
	 * @deprecated use ApiController::validateActionId() instead.
	 */
	public function requests() {
		return array();
	}
	
	protected function beforeAction($action) {
		
		$method = 'validate' . $action->id;
		if (method_exists($this, $method)) {
			$conf = $this->$method();
		}else {
			$confs = $this->requests();
			if (isset($confs[$action->id])) {
				$conf = $confs[$action->id];
			}
		}
		if (isset($conf)) {
			$this->api->setConfigure($conf);
		}
		
		if (!$this->api->validate()) {
			$code = $this->api->getCode();
			$this->renderJson($this->api->getMessage(), substr($code, 0, 3), $code);
		}
		return true;
	}
	
	/**
	 * @param array|string|boolean   $data      The data to render, false indicate no data.
	 * @param integer $status    The http status.
	 */
	public function renderJson($data = false, $status = 200, $subStatus = null) {
		header('Content-Type: application/json', true, $status);
		if ($subStatus != null) {
			$data = array(
				'code' => $subStatus,
				'message' => $data,
			);
		}
		if ($data) {
			$options = 0;
			if (YII_DEBUG) {
				$options |= JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
			}
			echo json_encode($data, $options);
		}
		Yii::app()->end();
	}
	
	
	
	
	public function isGet() {
		return $this->request->getRequestType() === 'GET';
	}
	
	public function isPost() {
		return $this->request->getIsPostRequest();
	}
	
	public function isPut() {
		return $this->request->getIsPutRequest();
	}
	
	public function isDelete() {
		return $this->request->getIsDeleteRequest();
	}
}
