<?php
/**
 * FileManaged class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class FileManaged extends CActiveRecord {
	
	const STATUS_TEMPORARY = 0;
	const STATUS_PERSISTENT = 1;
	
	/**
	 * @return FileManaged
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'file_managed';
	}
	
	public function upload($persistent = false) {
		
	}
	
	public function remove() {
		
	}
	
	
}
