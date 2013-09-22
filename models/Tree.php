<?php
/**
 * Tree AR class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class Tree extends Term {

	private $_parent;
	
	/**
	 * Create a new tree term.
	 * 
	 * @param array $attributes
	 * @return boolean
	 */
	public function create(array $attributes) {
		if (!isset($attributes['vid'])) {
			$attributes['vid'] = $this->getVocabularyId();
		}
		$parent = 0;
		if (isset($attributes['parent'])) {
			$parent = $attributes['parent'];
			unset($attributes['parent']);
		}
		$this->setIsNewRecord(true);
		$this->setAttributes($attributes);

		if ($this->save()) {
			TermHierarchy::add($this->tid, $parent, $this->vid);
			$newTerm = clone $this;
			if ($parent) {
				$newTerm->setParent($parent);
			}
			return $newTerm;
		}
		return false;
	}
	
	/**
	 * Move the item to new node.
	 * 
	 * @param integer $target The target parent
	 * @return boolean
	 */
	public function move($target) {
		if ($this->getParent() == $target) {
			return true;
		}
		if (TermHierarchy::model()->move($this->tid, $this->getParent(), $target, $this->vid)) {
			$this->setParent($target);
			return true;
		}else {
			return false;
		}
	}

	
	public function setParent($parent) {
		$this->_parent = $parent;
	}
	
	/**
	 * Get the parent tid of the node.
	 * 
	 * @return integer
	 */
	public function getParent() {
		if ($this->_parent === null) {
			$paernts = TermHierarchy::model()->getParents($this->tid, $this->vid);
			$this->_parent = $paernts[0];
		}
		return $this->_parent;
	}
	
	/**
	 * Remove the node and all its children.
	 * 
	 * @return boolean
	 */
	public function remove() {
		$hierarchy = TermHierarchy::model();
		$chilren = $hierarchy->getChildrenRecursively($this->tid, $this->vid);
		$removeIds = array_keys($chilren);
		$removeIds[] = $this->tid;
		$removed = $hierarchy->removeAll($this->tid, $this->getParent(), $this->vid);
		if ($removed) {
			$criteria = new CDbCriteria();
			$criteria->addInCondition('tid', $removeIds);
			$this->deleteAll($criteria);
		}
		return $removed;
	}
	
	/**
	 * Get the Route from root node to current node.
	 */
	public function getPath() {
		return self::fetchPath($this->tid, $this->getVocabularyId());
	}
	
	/**
	 * Get the tree path from root to current.
	 *
	 * @return Tree[]
	 */
	public static function fetchPath($termId, $vid) {
		$path = array(static::load($termId));
		$parents = TermHierarchy::model()->getParents($termId, $vid);
		while (!empty($parents[0])) {
			$path[] = static::load($parents[0]);
			$parents = TermHierarchy::model()->getParents($parents[0], $vid);
		}
		return array_reverse($path);
	}
}
