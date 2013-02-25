<?php
/**
 * SolariumDataProvider class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class SolariumDataProvider extends CDataProvider {
	/**
	 * @var Solarium\Core\Query\Result\Result
	 */
	private $_resultSet;
	
	private $_model;
	
	private $_modelClass;
	
	private $_condition;
	
	private $_params;
	
	/**
	 * @param Solarium\Core\Query\Query $query
	 * @param array $config
	 */
	public function __construct($model, $config = array()) {
		if ($model instanceof CModel) {
			$this->_model = $model;
			$this->_modelClass = get_class($model);
		}else {
			$this->_model = new $model();
			$this->_modelClass = $model;
		}
		$this->setId($this->_modelClass);
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}
	
	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData() {
		$result = $this->fetchResultSet();
		$items = array();
		foreach ($result as $document) {
			$items[] = $document;
		}
		
		return $items;
	}
	
	/**
	 * Fetches the data item keys from the persistent data storage.
	 * @return array list of data item keys.
	*/
	protected function fetchKeys() {
		return array();
	}
	
	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	 */
	protected function calculateTotalItemCount() {
		$params = $this->getParams();
		$params['rows'] = 0;
		return $this->_model->count($this->getCondition(), $params);
	}
	
	/**
	 * @return \Solarium\Core\Query\Result\Result
	 */
	protected function fetchResultSet() {
		if (!isset($this->_resultSet)) {
			$pagination = $this->getPagination();
			$pagination->setItemCount($this->calculateTotalItemCount());
			$this->_params['start'] = $pagination->getOffset();
			$this->_resultSet = $this->_model->findAll($this->getCondition(), $this->getParams());
		}
		return $this->_resultSet;
	}
	
	
	public function setCondition($condition) {
		$this->_condition = $condition;
		return $this;
	}
	
	public function getCondition() {
		return $this->_condition;
	}
	
	public function setParams($params) {
		$this->_params = $params;
		return $this;
	}
	
	public function getParams() {
		return $this->_params;
	}
}
