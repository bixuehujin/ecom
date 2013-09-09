<?php
/**
 * TermHierarchyTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TermHierarchyTest extends CDbTestCase {
	
	public $fixtures = array(
		'term_hierarchy' => 'TermHierarchy',
		'term_vocabulary' => 'TermVocabulary',
	);
	
	public function testGetChildrenRecursively() {
		
		
		$model = TermHierarchy::model();
		$nodes = $model->getChildrenRecursively(1, 1);
		$this->assertEquals(array(3 => 1, 4 => 1, 5 => 3), $nodes);
		
		$nodes = $model->getChildrenRecursively(0, 1);
		$this->assertEquals(array(1 => 0, 2 => 0, 3 => 1, 4 => 1, 5 => 3), $nodes);
	}
	
	public function testRemoveAll() {
		
		$model = TermHierarchy::model();
		$removed = $model->removeAll(3, 1, 1);
		$this->assertEquals(2, $removed);
		
		$this->setUp();
		$model->preload(1, true);
	}
}
