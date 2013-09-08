<?php
/**
 * TermHierarchy AR class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TermHierarchy extends CActiveRecord {
	
	private static $cacheChildren = array(); //hierarchy cache indexed by vid
	
	private static $cacheParents  = array();
	
	/**
	 * @return TermHierarchy
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'term_hierarchy';
	}

	protected function preload($vid) {
		$cacheChildren = &self::$cacheChildren[$vid];
		$cacheParents  = &self::$cacheParents[$vid];
		if (isset($cacheChildren)) {
			return;
		}
		
		$relations = self::model()->findAll('vid=' . $vid);
		foreach ($relations as $relation) {
			list($tid, $parent) = array($relation->tid, $relation->parent);
			
			if (!isset($cacheParents[$tid])) {
				$cacheParents[$tid] = array($parent);
			}else {
				$cacheParents[$tid][] = $parent;
			}
			
			if (!isset($cacheChildren[$parent])) {
				$cacheChildren[$parent] = array($tid);
			}else {
				$cacheChildren[$parent][] = $tid;
			}
		}
	}
	
	protected function addToCache($tid, $parent, $vid) {
		$cacheChildren = &self::$cacheChildren[$vid];
		if (!isset($cacheChildren)) {
			return;
		}
		if (isset($cacheChildren[$parent])) {
			$cacheChildren[$parent][] = $tid;
		}else {
			$cacheChildren[$parent] = array($tid);
		}
		
		$cacheParents = &self::$cacheParents[$vid];
		if (isset($cacheParents[$tid])) {
			$cacheParents[$tid][] = $parent;
		}else {
			$cacheParents[$tid] = array($parent);
		}
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
			if ($model->save(false)) {
				$model->addToCache($tid, $parent, $vid);
				return true;
			}
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
	
	public function getParents($tid, $vid) {
		$this->preload($vid);
		if (isset(self::$cacheParents[$vid][$tid])) {
			return self::$cacheParents[$vid][$tid];
		}
		return array();
	}
	
	public function getChildren($parentId, $vid) {
		$this->preload($vid);
		if (isset(self::$cacheChildren[$vid][$parentId])) {
			
			return self::$cacheChildren[$vid][$parentId];
		}
		return array();
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
			$parents = self::fetchParents($tid, $vid, false);
			if (empty($parents)) {
				break;
			}
			$parent = $parents[0];
			$ret[$parent->parent] = $parent;
		}
		return array_reverse($ret);
	}
}
