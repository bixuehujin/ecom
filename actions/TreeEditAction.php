<?php
/**
 * TreeEditAction class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TreeEditAction extends CAction {
	
	public $action;
	
	public $treeSchema;
	
	private $_termModel;
	
	public function setTermModel($term) {
		if (is_string($term)) {
			if (!class_exists($term)) {
				throw new CException(Yii::t('yii.common', "Term '{term}' is not a valid className", array('{term}' => $term)));
			}
			$this->_termModel = $term::model();
		}else if ($term instanceof Term){
			$this->_termModel = $term;
		}else {
			throw new CException(Yii::t('yii.common', "Invalid value for attribute termModel."));
		}
	}
	
	public function getTermModel() {
		if ($this->_termModel === null) {
			throw new CException(Yii::t('yii.common', "The 'termModel' attribute must not be empty"));
		}
		return $this->_termModel;
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
		
		$term = $this->getTermModel();
		$newTerm = $term->create($_POST);
		if (!$newTerm) {
			$ajax->setCode(AjaxReturn::OPERATION_FAILED)->setMsg($term->getErrors())->send();
		}
		$data = Utils::fetchProperties($newTerm, $this->getTreeSchema());
		$ajax->setData($data)->send();
	}
}
