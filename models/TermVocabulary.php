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
	
	/**
	 * Load a term vocabulary by its identifier.
	 * 
	 * @param mixed $identifier
	 * @return TermHierarchy
	 */
	public static function load($identifier) {
		if (is_numeric($identifier)) {
			return self::model()->findByPk($identifier);
		}else if (is_string($identifier)){
			return self::loadByMName($identifier);
		}else if ($identifier instanceof self) {
			return $identifier;
		}else {
			throw new InvalidArgumentException('Invalid argument for TermVocabulary::load().');
		}
	}
	
	/**
	 * Load vocabulary by its mname.
	 *
	 * @param string $mname
	 * @return TermVocabulary
	 */
	public static function loadByMName($mname) {
		return self::model()->findByAttributes(array(
				'mname' => $mname,
		));
	}
	
}
