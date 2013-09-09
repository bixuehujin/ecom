<?php
/**
 * TreeNodesAction class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TreeNodesAction extends CAction {
	
	public $paramName = 'node';
	public $treeSchema;	
	private $_vocabulary;
	
	/**
	 * Set the vocabulary used by this action.
	 * 
	 * @param mixed $vocabulary vid or TermVocabulary object.
	 */
	public function setVocabulary($vocabulary) {
		if ($vocabulary instanceof TermVocabulary) {
			$this->_vocabulary = $vocabulary;
		}else {
			$this->_vocabulary = TermVocabulary::load($vocabulary);
			if (!$this->_vocabulary) {
				throw new CException('The specified vocabulary do not existed in database');
			}
		}
	}
	
	public function getVocabulary() {
		if ($this->_vocabulary === null) {
			throw new CException("The vocabulary attribute cann't be empty");
		}
		return $this->_vocabulary;
	}
	
	public function getTreeSchema() {
		if ($this->treeSchema === null) {
			throw new CException(Yii::t('yii.common', "The 'treeSchema' attribute must not be empty"));
		}
		return $this->treeSchema;
	}
	
	public function run() {
		$ajax = new AjaxReturn();
		$ajax->showStatusOnSuccess = (boolean)Yii::app()->request->getQuery('show_status', true);
		
		$requestNode = $this->getRequestNode();
		if ($requestNode) {
			$node = Tree::load($requestNode);
			if (!$node) {
				$ajax->setCode(AjaxReturn::RESOUCE_NOT_FOUND)->send();
				return;
			}
			$children = $node->children();
		} else{ //return the toplevel nodes
			$vocabulary = $this->getVocabulary();
			$children = Tree::fetchChildren(0, $vocabulary->vid);
		}
		
		$ajax->setData($this->formatRequests($children))->send();
	}
	
	protected function formatRequests($children) {
		$data = array();
		foreach ($children as $node) {
			$tmp = Utils::fetchProperties($node, $this->getTreeSchema());
			$tmp['load_on_demand'] = $node->getHasChildren();
			$data[] = $tmp;
		}
		return $data;
	}
	
	protected function getRequestNode() {
		return Yii::app()->request->getQuery($this->paramName);
	}
}
