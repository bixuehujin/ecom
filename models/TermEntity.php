<?php
/**
 * TermEntity AR class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TermEntity extends CActiveRecord {
	
	/**
	 * @return TermEntity
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'term_entity';
	}
	
	public function beforeSave() {
		$this->created = time();
		return parent::beforeSave();
	}
	
}
