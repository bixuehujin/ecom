<?php
/**
 * File Usage class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * Records where a file is used.
 * 
 */
class FileUsage extends CActiveRecord {

	public $sum;
	
	/**
	 * @return FileUsage
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'file_usage';
	}
	
	public function delete() {
		if (self::getUsageCount($this->fid) <= $this->count) {
			FileManaged::remove($this->fid);
		}
		return parent::delete();
	}
	
	
	/**
	 * Add usage tracking to a file.
	 * 
	 * @param integer $entityId
	 * @param string $entityType
	 * @param integer|FileManaged $file
	 * @param integer $count
	 * @return boolean
	 */
	public static function add($entityId, $entityType, $file, $count = 1) {
		if (!$file instanceof FileManaged) {
			$file = FileManaged::model()->findByPk($file);
		}
		$usage = new FileUsage();
		$usage->entity_id = $entityId;
		$usage->entity_type = $entityType;
		$usage->fid = $file->fid;
		$usage->count = $count;
		if ($usage->save(false)) {
			if ($file->status != FileManaged::STATUS_PERSISTENT) {
				$file->status = FileManaged::STATUS_PERSISTENT;
				$file->save(false, array('status'));
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Remove usage of a file.
	 * 
	 * @param integer $entityId
	 * @param string $entityType
	 * @param integer|FileManaged $file
	 * @return boolean
	 */
	public static function remove($entityId, $entityType, $file) {
		$allUsages = self::getAllUsage($file);
		$currIndex = $entityType . '-' . $entityId;
		if (!isset($allUsages[$currIndex])) {//The entity do not use the file.
			return false;
		}
		$currUsage = $allUsages[$currIndex];
		unset($allUsages[$currIndex]);
		if (empty($allUsages)) {
			if ($file instanceof FileManaged) {
				$file = $file->fid;
			}
			FileManaged::remove($file);
		}
		$currUsage->delete();
		return true;
	}
	
	/**
	 * Remove all usage of a file in a entity type.
	 * 
	 * @param string $entityType
	 * @param integer|FileManaged $file
	 * @return integer
	 */
	public static function removeAll($entityType, $file) {
		$allUsages = self::getAllUsage($file);
		$removing = array();
		foreach ($allUsages as $key => $usage) {
			if ($usage->entity_type == $entityType) {
				$removing[] = $usage;
				unset($allUsages[$key]);
			}
		}
		
		if (empty($allUsages)) {
			FileManaged::remove($file);
		}
		$n = 0;
		foreach ($removing as $usage) {
			if ($usage->delete()) {
				$n ++;
			}
		}
		return $n;
	}
	
	/**
	 * Remove all files attached to an entity.
	 * 
	 * @param integer $entityId
	 * @param string $entityType
	 * @return integer
	 */
	public static function removeAllAttached($entityId, $entityType) {
		$usages = self::model()->getAllAttached($entityId, $entityType);
		foreach ($usages as $usage) {
			$usage->delete();
		}
		return count($usages);
	}
	
	public function updateUsageCounter($file, $count) {
		
	}
	
	public function change($file, $count = 1) {
		list($eid, $domain, $fid) = $this->getIdentifier($file);
		
		$allUsages = $this->getAllUsage($fid);
		$currIndex = $domain . '-' . $eid;
		if (!isset($allUsages[$currIndex])) {
			return $this->addUsage($file, $count);
		}
		$currUsage = $allUsages[$currIndex];
		unset($allUsages[$currIndex]);
		if (empty($allUsages)) {
			if (!isset($this->_fm)) {
				throw new CException('Property {fileManaged} is unset.');
			}
			$this->_fm->remove($currUsage->fid);
		}
		$currUsage->fid = $fid;
		$currUsage->count = $count;
		return $currUsage->save(false, array('fid', 'count'));
	}
	
	/**
	 * Get all usage of a file.
	 * 
	 * @param integer|FileManaged $file
	 * @return FileUsage[]
	 */
	public static function getAllUsage($file) {
		if ($file instanceof FileManaged) {
			$file = $file->fid;
		}
		$usages = self::model()->findAllByAttributes(array('fid' => $file));
		return Utils::arrayColumns($usages, null, array('entity_type', 'entity_id'));
	}
	
	/**
	 * Get usage attached to an entity.
	 *
	 * @param integer $entityId
	 * @param string $entityType
	 * @return FileUsage[]
	 */
	public static function getAllAttached($entityId, $entityType) {
		return self::model()->findAllByAttributes(array(
			'entity_type' => $entityType,
			'entity_id' => $entityId,
		));
	}
	
	/**
	 * 
	 * @param integer $fid
	 */
	public static function getUsageCount($fid) {
		$criteria = new CDbCriteria();
		$criteria->select = 'SUM(count) as sum';
		$criteria->addColumnCondition(array('fid' => $fid));
		$res = self::model()->find($criteria);
		return (int)$res['sum'];
	}
	
	public function clearAllUsage($file) {
		
	}
	
}
