<?php
/**
 * Location class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class Location extends CActiveRecord {
	
	public function tableName() {
		return 'location';
	}
	
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	
}
