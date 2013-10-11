<?php namespace ecom\settings\storage;
/**
 * DatabaseStorage class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use CActiveRecord;
use CDbCriteria;

/**
 * The RDBMS setting storage backend implementation.
 *  
 * @property string $db The database connection id used for storing and retrieving data from.
 */
class DatabaseStorage extends CActiveRecord implements StorageInterface {
	
	public function tableName() {
		if ($this->getDbConnection()->tablePrefix !== null) {
			return '{{setting}}';
		}else {
			return 'setting';
		}
	}
	
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	protected function beforeSave() {
		$this->value = serialize($this->value);
		return true;
	}
	
	protected function afterFind() {
		$this->value = unserialize($this->value);
	}
	
	/**
	 * @inheritdoc
	 */
	public function get($key, $default = null) {
		if ($result = $this->findByPk($key)) {
			return $result->value;
		}else {
			return $default;
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function mget(array $keys) {
		if (empty($keys)) {
			return array();
		}
		$ret = array();
 		$criteria = new CDbCriteria();
		$criteria->addInCondition('name', $keys);
		$criteria->index = 'name';
		foreach ($this->findAll($criteria) as $name => $value) {
			$ret[$name] = $value->value;
		}
		return $ret;
	}
	
	/**
	 * @inheritdoc
	 */
	public function set($key, $value) {
		$table = $this->tableName();
		$sql = "INSERT INTO {$table}(name, value) VALUES (:name, :value) ON DUPLICATE KEY UPDATE value = :value";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindValue(":name", $key, \PDO::PARAM_STR);
		$command->bindValue(":value", serialize($value), \PDO::PARAM_STR);
		$command->execute();
		return true;
	}
	
	public function mset(array $values) {
		$count = 0;
		foreach ($values as $key => $value) {
			if ($this->set($key, $value)) {
				$count ++;
			}
		}
		return $count;
	}
	
	public function del($key) {
		return $this->deleteByPk($key);
	}
	
	public function mdel(array $keys) {
		$criteria = new CDbCriteria();
		$criteria->addInCondition('name', $keys);
		return parent::deleteAll($criteria);
	}
	
	public function exists($key = '', $_ = null) {
		return parent::exists("name=:key", array(':key' => $key));
	}
	
	public function deleteAll($_ = null, $__ = null) {
		return parent::deleteAll();
	}
}
