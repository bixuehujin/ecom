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
		
		$toplevel = Term::fetchChildren(0, 1);
		$this->assertCount(2, $toplevel);
		$this->assertEquals(array(1, 2), array_keys($toplevel));
	}
	
	public function testCreate() {
		$model = Term::model();
		$term = $model->create(array(
			'name' => 'test term',
			'vid' => 1,
		));
		$this->assertInstanceOf('Term', $term);
		$this->assertCount(3, Term::fetchChildren(0, 1));
		
		$term->delete();
		$this->assertCount(2, Term::fetchChildren(0, 1));
		
		$term = Term::model()->create(array(
			'name' => 'sub term of 2',
			'vid' => 1,
			'parent' => 2,
		));
		$this->assertCount(1, Term::fetchChildren(2, 1));
		$this->assertEquals(array($term->tid), array_keys(Term::fetchChildren(2, 1)));
	}
}
