<?php namespace ecom\file\model;
/**
 * File Usage class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * Records where a file is used.
 * 
 */
class FileUsage extends \CActiveRecord {

	/**
	 * @return FileUsage
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'file_usage';
	}
}
