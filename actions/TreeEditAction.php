<?php
/**
 * TreeEditAction class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TreeEditAction extends CAction {
	
	public $action;
	
	public $treeSchema;
	/**
	 * @var Tree
	 */
	private $_model;
	
	public function setModel($term) {
		if (is_string($term)) {
			if (!class_exists($term)) {
				throw new CException(Yii::t('yii.common', "Term '{term}' is not a valid className", array('{term}' => $term)));
			}
			$this->_model = $term::model();
		}else if ($term instanceof Term){
			$this->_model = $term;
		}else {
			throw new CException(Yii::t('yii.common', "Invalid value for attribute model."));
		}
	}
	
	public function getModel() {
		if ($this->_model === null) {
			throw new CException(Yii::t('yii.common', "The 'model' attribute must not be empty"));
		}
		return $this->_model;
	}
	
	public function getTreeSchema() {
		if ($this->treeSchema === null) {
			throw new CException(Yii::t('yii.common', "The 'treeSchema' attribute must not be empty"));
		}
		return $this->treeSchema;
	}
	
	public function run() {
		switch ($this->action) {
			case 'addchild':
				$this->actionAddChild();
				break;
			case 'move':
				$this->actionMove();
				break;
			case 'remove':
				$this->actionRemove();
				break;
			default:
				throw new CException(Yii::t('yii.common', "The action '{action}' can not be recognized", array('{action}' => $this->action)));
				break;
		}
	}
	
	protected function actionAddChild() {
		$request = Yii::app()->getRequest();
		$ajax = new AjaxReturn();
		
		if (!$request->getIsPostRequest()) {
			$ajax->setCode(AjaxReturn::BAD_REQUEST_METHOD)->send();
		}
		
		$term = $this->getModel();
		$newTerm = $term->create($_POST);
		if (!$newTerm) {
			$ajax->setCode(AjaxReturn::OPERATION_FAILED)->setMsg($term->getErrors())->send();
		}
		$data = Utils::fetchProperties($newTerm, $this->getTreeSchema());
		$ajax->setData($data)->send();
	}
	
	protected function actionMove() {
		$request = Yii::app()->getRequest();
		$ajax = new AjaxReturn();
		
		$id = $request->getQuery('id');
		$parent = $request->getQuery('parent');
		$target = $request->getQuery('target');
		
		if (!$id) {
			return $ajax->setCode(AjaxReturn::PARAM_TYPE_INVALID)->send();
		}
		
		if ($parent == $target) {
			return $ajax->send();
		}
		$model = $this->getModel();
		$term  = $model->load($id);
		
		if (!$term) {
			return $ajax->setCode(AjaxReturn::RESOUCE_NOT_FOUND)->send();
		}

		if (!$term->move($target)) {
			$ajax->setCode(AjaxReturn::OPERATION_FAILED);
		}
		$ajax->send();
	}
	
	protected function actionRemove() {
		$request = Yii::app()->getRequest();
		$ajax = new AjaxReturn();
		
		$nodeId = $request->getQuery('node');
		if (!$nodeId) {
			$ajax->setCode(AjaxReturn::PARAM_MISSING_REQUIRED)->send();
		}
		$node = $this->getModel()->load($nodeId);
		if (!$node) {
			$ajax->setCode(AjaxReturn::RESOUCE_NOT_FOUND)->send();
		}
		if (!$node->remove()) {
			$ajax->setCode(AjaxReturn::OPERATION_FAILED);
		}
		$ajax->send();
	}
}
