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

	public function preload($vid, $force = false) {
		$cacheChildren = &self::$cacheChildren[$vid];
		$cacheParents  = &self::$cacheParents[$vid];
		
		if ($force) {
			$cacheChildren = $cacheParents = null;
		}
		
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
	
	protected function removeFormCache($tid, $parent, $vid) {
		$cacheChildren = &self::$cacheChildren[$vid];
		if (!isset($cacheChildren)) {
			return;
		}
		
		if (isset($cacheChildren[$parent])) {
			if (($key = array_search($tid, $cacheChildren[$parent])) !== false) {
				unset($cacheChildren[$parent][$key]);
			}
		}
		
		$cacheParents = &self::$cacheParents[$vid];
		if (isset($cacheParents[$tid])) {
			if (($key = array_search($parent, $cacheParents[$tid])) !== false) {
				unset($cacheParents[$tid][$key]);
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
	 * Get all sub nodes of a term.
	 * 
	 * @param integer $tid
	 * @param integer $vid
	 * @return array array tid => parent
	 */
	public function getChildrenRecursively($tid, $vid) {
		$ret = array();
		
		$children = $this->getChildren($tid, $vid);
		$nodes = array();
		foreach ($children as $child) {
			$nodes[$child] = $tid;
		}
		
		$ret += $nodes;
		
		foreach ($children as $child) {
			$ret += $this->getChildrenRecursively($child, $vid);
		}
		
		return $ret;
	}
	
	/**
	 * Remove a term and all its sub terms.
	 * 
	 * @param unknown $tid
	 * @param unknown $parent
	 * @param unknown $vid
	 */
	public function removeAll($tid, $parent, $vid) {
		$nodes = $this->getChildrenRecursively($tid, $vid);
		$ret = 0;
		foreach ($nodes as $nodeId => $nodeParent) {
			$ret += $this->removeInternal($nodeId, $nodeParent, $vid);
		}
		$ret += $this->removeInternal($tid, $parent, $vid);
		return $ret;
	}
	
	protected function removeInternal($tid, $parent, $vid) {
		$this->removeFormCache($tid, $parent, $vid);
		return $this->deleteAllByAttributes(array(
			'vid' => $vid,
			'tid' => $tid,
			'parent' => $parent
		));
	}
	
	/**
	 * Returns whether a term has children.
	 * 
	 * @param integer $tid
	 * @param integer $vid
	 * @return boolean
	 */
	public function hasChildren($tid, $vid) {
		$this->preload($vid);
		return isset(self::$cacheChildren[$vid][$tid]);
	}
	
	/**
	 * Move a term to a new parent.
	 * 
	 * @param integer $id
	 * @param integer $parent
	 * @param integer $targetParent
	 * @param integer $vid
	 * @return boolean
	 */
	public function move($id, $parent, $targetParent, $vid) {
		if ($parent == $targetParent) {
			return true;
		}
		
		$hierarchy = $this->findByAttributes(array(
			'vid' => $vid, 'tid' => $id, 'parent' => $parent
		));
		if ($hierarchy) {
			$hierarchy->parent = $targetParent;
			if ($hierarchy->save(false, array('parent'))) {
				$this->removeFormCache($id, $parent, $vid);
				$this->addToCache($id, $targetParent, $vid);
				return true;
			}
		}
		return false;
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
