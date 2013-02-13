<?php
/**
 * TermVocabulary AR class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TermVocabulary extends CActiveRecord {
	
	/**
	 * @return TermVocabulary
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'term_vocabulary';
	}
	
	public function getIdByMName($mname) {
		$re = $this->findByAttributes(array('mname' => $mname));
		return $re ? $re->vid : false;
	}
	
}
