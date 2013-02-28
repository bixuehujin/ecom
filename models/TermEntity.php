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
	
	/**
	 * Get terms attached to an entity.
	 * 
	 * @param integer $entidyId
	 * @param Term[]
	 */
	public static function getAttachedTerms($entidyId, $entityType) {
		$tids = self::model()->findByAttributes(array(
			'entity_id' => $entidyId,
			'entity_type' => $entityType,
		));
		if (!$tids) {
			return array();
		}
		$tids = Utils::arrayColumns($tids, 'tid');
		return Term::loadByIds($tids);
	}
	
	/**
	 * Add a Term-Entity relation.
	 * 
	 * @param integer $tid
	 * @param integer $entityId
	 * @param string $entityType
	 * @return TermEntity|boolean
	 */
	public static function add($tid, $entityId, $entityType) {
		$model = new self();
		$model->tid = $tid;
		$model->entity_id = $entityId;
		$model->entity_type = $entityType;
		try {
			if ($model->save(false)) {
				return $model;
			}else {
				return false;
			}
		}catch (CDbException $e) {
			return false;
		}
	}
}
