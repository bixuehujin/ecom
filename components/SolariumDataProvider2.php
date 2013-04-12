<?php
/**
 * SolariumDataProvider2 class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since 2013-04-12
 */

class SolariumDataProvider2 extends CDataProvider {
	
	private $_query;
	
	private $_resultSet;
	
	public $endpoint;
	
	/**
	 * @param object $query
	 * @param array  $config
	 */
	public function __construct($query, $config) {
		$this->_query = $query;
		foreach ($config as $name => $value) {
			$this->$name = $value;
		}
		$this->getPagination()->validateCurrentPage = false;
	}
	
	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData() {
		$pagination = $this->getPagination();
		$this->_query->setRows($pagination->getPageSize());
		$this->_query->setStart($pagination->getOffset());
		
		$client = Yii::app()->solarium->getClient();
		$this->_resultSet = $client->execute($this->_query, $this->endpoint);
		
		$pagination->setItemCount($this->calculateTotalItemCount());
		
		return $this->_resultSet;
	}
	
	/**
	 * Fetches the data item keys from the persistent data storage.
	 * @return array list of data item keys.
	*/
	protected function fetchKeys() {
	}
	
	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	*/
	protected function calculateTotalItemCount() {
		if (!$this->_resultSet) {
			$this->fetchData();
		}
		return $this->_resultSet->getNumFound();
	}
	
}
