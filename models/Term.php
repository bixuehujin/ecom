<?php
/**
 * Term AR class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class Term extends CActiveRecord {

	/**
	 * @return Term
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'term';
	}
	
	public function relations() {
		return array(
			'vacabulary'=>array(self::HAS_ONE, 'TermVacabulary', array('vid'=>'vid')),
		);
	}
	
	/**
	 * Returns the default vocabulary the current term related.
	 * Custom Term should override this method and return a TermVocabulary object.
	 * 
	 * @return TermVocabulary
	 */
	public function vocabulary() {
		return false;
	}
	
	/**
	 * Load all terms of a vocabulary from database.
	 * 
	 * @param mixed $vid
	 * @return Term[]
	 */
	public function loadAll($vid = null) {
		if ($vid === null) {
			$vocabulary = $this->vocabulary();
			if (!$vocabulary instanceof TermVocabulary) {
				throw new CException('Using default vocabulary should override the vocabulary() method.');
			}
			$vid = $vocabulary->vid;
		}
		static $cache;
		if (!is_numeric($vid)) {
			$vid = TermVocabulary::model()->getIdByMName($vid);
		}
		if (!isset($cache[$vid])) {
			$res =  $this->findAllByAttributes(array('vid' => $vid));
			$cache[$vid] = Utils::arrayColumns($res, null, 'tid');
		}
		return $cache[$vid];
	}
	
	/**
	 * Build a tree structure from a list of term.
	 * 
	 * @param integer|string $vid
	 * @return array
	 */
	public function buildTree($vid = null) {
		if ($vid === null) {
			$vocabulary = $this->vocabulary();
			if (!$vocabulary instanceof TermVocabulary) {
				throw new CException('Using default vocabulary should override the vocabulary() method.');
			}
			$vid = $vocabulary->vid;
		}
		
		if (!is_numeric($vid)) {
			$vid = TermVocabulary::model()->getIdByMName($vid);
		}
		static $treeCache;
		if (isset($treeCache[$vid])) {
			return $treeCache[$vid];
		}
		
		$terms = $this->loadAll($vid);
		$tids = Utils::arrayColumns($terms, 'tid', null);
		
		foreach ($terms as &$tmpTerm) {
			$tmpTerm = $tmpTerm->toStdClass();
		}
		//print_r($terms);
		$backups = $terms;
		$childrenMaps = TermHierarchy::model()->getChildren($tids);
		
		foreach ($childrenMaps as $tid => $children) {
			$term = $backups[$tid];
			foreach ($children as $child) {
				
				if (!isset($term->children)) {
					$term->hasChildren = true;
					$term->children = array($backups[$child]);
				}else {
					$term->children[] = $backups[$child];
				}
				unset($terms[$child]);
			}
		}
		return $treeCache[$vid] = $terms;
	}
	
	/**
	 * Walk throuth a term tree.
	 * 
	 * @param array $tree
	 * @param callable $callback
	 */
	public function termTreeWalk($tree, $callback) {
		$tree2 = $tree;
		$this->termTreeWalkHelper($tree2, $callback);
		return $tree2;
	}
	
	protected function termTreeWalkHelper(&$tree, $callback) {
		foreach ($tree as $key => &$child) {
			if (!$child instanceof stdClass) {
				continue;
			}
			if ($child->hasChildren) {
				$this->termTreeWalkHelper($child->children, $callback);
			}
			if (is_callable($callback)) {
				if ($n = call_user_func($callback, $child)) {
					$child = $n;
				}
			}
		}
	}
	
	/**
	 * Check if a term is exist.
	 * 
	 * @param integer $tid
	 * @param integer $vid
	 */
	public function checkExist($tid, $vid) {
		$list = $this->loadAll($vid);
		return isset($list[$tid]);
	}
	
	/**
	 * Add a child term for current term.
	 * 
	 * @param integer|Term $child
	 * @return boolean
	 */
	public function addChild($child) {
		if ($child instanceof self){
			$child = $child->tid;
		}
		return TermHierarchy::add($child, $this->tid);
	}
	
	/**
	 * Remove a child term for current term.
	 *
	 * @param integer|Term $child
	 * @return boolean
	 */
	public function removeChild($child) {
		if ($child instanceof self) {
			$child = $child->tid;
		}
		return TermHierarchy::remove($child, $this->tid);
	}
	
	public function toStdClass() {
		$ret = (object)$this->getAttributes();
		$ret->hasChildren = false;
		return $ret;
	}
	
	/**
	 * Attach the category to a entity.
	 *
	 * @param integer $entityId
	 * @param string $entityType
	 */
	public function attachTo($entityId, $entityType) {
		return (boolean)TermEntity::add($this->tid, $entityId, $entityType);
	}
	
	/**
	 * Load term by ids.
	 * 
	 * @param array $tids
	 * @return Term[]
	 */
	public static function loadByIds($tids) {
		$criteria = new CDbCriteria();
		$criteria->addInCondition('tid', $tids);
		return $terms = self::model()->findAll($criteria);
	}
}
