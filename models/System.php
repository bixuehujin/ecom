<?php
/**
 * System class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class System extends CActiveRecord {
	
	private static $__tableName;
	
	public static function setTableName($name) {
		self::$__tableName = $name;
	}
	
	public function tableName() {
		if (isset(self::$__tableName)) {
			return self::$__tableName;
		}else {
			return 'system';
		}
	}
	
	static public function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	protected function beforeSave() {
		$this->value = serialize($this->value);
		return true;
	}
	
	protected function afterFind() {
		$this->value = unserialize($this->value);
	}
}
