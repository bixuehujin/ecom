<?php

class System extends CActiveRecord {
	
	public function tableName() {
		return 'system';
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
