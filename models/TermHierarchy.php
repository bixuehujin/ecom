<?php
/**
 * TermHierarchy AR class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TermHierarchy extends CActiveRecord {
	
	/**
	 * @return TermHierarchy
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'term_hierarchy';
	}

	/**
	 * Get all parents of all tids.
	 * 
	 * @param mixed $tids
	 * @return array All parents indexed with tid.
	 */
	public function getParents($tids) {
		if (!is_array($tids)) {
			$tids = array($tids);
		}
		$criteria = new CDbCriteria();
		$criteria->addInCondition('tid', $tids);
		$res = $this->findAll($criteria);
		$ret = array();
		if ($res) {
			foreach ($res as $item) {
				if (isset($ret[$item->tid])) {
					$ret[$item->tid][] = $item->parent;
				}else {
					$ret[$item->tid] = array($item->parent);
				}
			}
		}
		return $ret;
	}
	
	/**
	 * Get all children of all tids.
	 *
	 * @param mixed $tids
	 * @return array All children indexed with tid.
	 */
	public function getChildren($tids) {
		if (!is_array($tids)) {
			$tids = array($tids);
		}
		$criteria = new CDbCriteria();
		$criteria->addInCondition('parent', $tids);
		$res = $this->findAll($criteria);
		$ret = array();
		if ($res) {
			foreach ($res as $item) {
				if (isset($ret[$item->parent])) {
					$ret[$item->parent][] = $item->tid;
				}else {
					$ret[$item->parent] = array($item->tid);
				}
			}
		}
		return $ret;
	}
	
	/**
	 * Add a term hierarchy record.
	 * 
	 * @param integer $tid
	 * @param integer $parent
	 * @return boolean
	 */
	public static function add($tid, $parent) {
		$model = self::model();
		$model->setIsNewRecord(true);
		$model->tid = $tid;
		$model->parent = $parent;
		try {
			return $model->save(false);
		}catch (CDbException $e) {
			return false;
		}
	}
	
	/**
	 * Remove a term hierarchy record.
	 * 
	 * @param integer $tid
	 * @param integer $parent
	 * @return boolean
	 */
	public static function remove($tid, $parent) {
		return (bool)self::model()->deleteAllByAttributes(array(
			'tid' => $tid,
			'parent' => $parent,
		));
	}
	
	/**
	 * Fetch children of a termId.
	 * 
	 * @param integer $tid
	 * @return array  array of tid.
	 */
	public static function fetchChildren($tid) {
		$res = self::model()->findAll('parent=' . $tid);
		return Utils::arrayColumns($res, 'tid');
	}
	
	public static function fetchParents($tid) {
		
	}
}
