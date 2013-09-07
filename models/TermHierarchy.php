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
	public function getParents($tids, $vid) {
		if (!is_array($tids)) {
			$tids = array($tids);
		}
		$criteria = new CDbCriteria();
		$criteria->addInCondition('tid', $tids);
		$criteria->addCondition('vid=' . $vid);
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
	public function getChildren($tids, $vid) {
		if (!is_array($tids)) {
			$tids = array($tids);
		}
		$criteria = new CDbCriteria();
		$criteria->addInCondition('parent', $tids);
		$criteria->addCondition('vid=' . $vid);
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
	public static function add($tid, $parent, $vid) {
		$model = self::model();
		$model->setIsNewRecord(true);
		$model->tid = $tid;
		$model->parent = $parent;
		$model->vid = $vid;
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
	public static function remove($tid, $parent, $vid) {
		return (bool)self::model()->deleteAllByAttributes(array(
			'tid' => $tid,
			'parent' => $parent,
			'vid' => $vid
		));
	}
	
	/**
	 * Fetch children of a termId.
	 * 
	 * @param integer $tid
	 * @return array  array of tid.
	 */
	public static function fetchChildren($tid, $vid) {
		$criteria = new CDbCriteria();
		$criteria->addColumnCondition(array(
			'parent' => $tid,
			'vid' => $vid
		));
		$res = self::model()->findAll($criteria);
		return Utils::arrayColumns($res, 'tid');
	}
	
	/**
	 * Fetch parents of a termId
	 * 
	 * @param integer $tid
	 * @return array 
	 */
	public static function fetchParents($tid, $vid) {
		$criteria = new CDbCriteria();
		$criteria->addColumnCondition(array(
			'tid' => $tid,
			'vid' => $vid
		));
		$res = self::model()->findAll($criteria);
		return Utils::arrayColumns($res, 'parent');
	}
	
	/**
	 * Fetch all ancestors of a term.
     *
	 * @param integer $tid
	 * @param integer $vid
	 */
	public static function fetchAncestors($tid, $vid) {
		$ret = array();
		while (true) {
			$parents = self::fetchParents($tid, $vid);
			if (!isset($parents[0])) {
				break;
			}
			$tid = $parents[0];
			$ret[] = $tid;
		}
		return array_reverse($ret);
	}
}
