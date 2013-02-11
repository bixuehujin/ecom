<?php
/**
 * Setting class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class Setting extends CActiveRecord {
	
	public function tableName() {
		return 'setting';
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
