<?php
/**
 * TermTest class file.
 *
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TermTest extends CDbTestCase {

	public $fixtures = array(
		'term_hierarchy' => 'TermHierarchy',
		'term_vocabulary' => 'TermVocabulary',
		'term' => 'Term',
	);

	public function testLoad() {
		$term = Term::load(1);
		$this->assertEquals(1, $term->vid);
		$this->assertEquals('level_1', $term->name);
	}
	
	public function testChildren() {
		$root = Term::load(1);
		$children = $root->children();
		
		$this->assertCount(2, $children);
		$this->assertEquals(array(3, 4), array_keys($children));
	}
	
	public function testGetPath() {
		$term = Term::load(5);
		$path = $term->getPath();
		
		$this->assertEquals(3, count($path));
		$this->assertEquals(array(1, 3, 5), Utils::arrayColumns($path, 'tid'));
	}
}
