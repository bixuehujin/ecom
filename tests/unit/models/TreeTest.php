<?php
/**
 * TreeTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class ExampleTree extends Tree {
	public function vocabulary() {
		return TermVocabulary::load(1);
	}
}

class TreeTest extends CDbTestCase {
	
	public $fixtures = array(
		'term_hierarchy' => 'TermHierarchy',
		'term_vocabulary' => 'TermVocabulary',
		'term' => 'Term',
	);
	
	public function testMove() {
		$node5 = Tree::load(5);
		$this->assertEquals(3, $node5->getParent());
		
		$result = $node5->move(1);
		$this->assertTrue($result);
		$this->assertEquals(1, $node5->getParent());
		
		$node1 = Tree::load(1);
		$children = $node1->children();
		$this->assertCount(3, $children);
		
		$node5->move(3);
		$this->assertEquals(3, $node5->getparent());
	}
	
	public function testRemove() {
		$tree = Tree::load(3);
		$removed = $tree->remove();
		$this->assertEquals(2, $removed);
		
		$this->setUp();
		TermHierarchy::model()->preload(1, true);
	}
	
	public function testGetPath() {
		$tree = ExampleTree::load(5);
		$path = $tree->getPath();
		$this->assertEquals(array(1, 3, 5), Utils::arrayColumns($path, 'tid'));
		
		$tree = ExampleTree::load(2);
		$path = $tree->getPath();
		$this->assertEquals(array(2), Utils::arrayColumns($path, 'tid'));
	}
}
