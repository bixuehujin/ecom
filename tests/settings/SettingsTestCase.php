<?php namespace ecom\tests\settings;
/**
 * SettingsTestCase class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

abstract class SettingsTestCase extends \CDbTestCase {
	
	abstract public function getTarget();
	
	public function testGet() {
		$target = $this->getTarget();
		$this->assertEquals('value1', $target->get('key1'));
		$this->assertEquals(null, $target->get('non-exist'));
	
		$this->assertEquals(0, $target->get('non-exist', 0));
	}
	
	public function testMget() {
		$target = $this->getTarget();
		$this->assertEquals(array(
			'key1' => 'value1',
		), $target->mget(array('key1', 'unexist')));
	}
	
	public function testSet() {
		$target = $this->getTarget();
		$this->assertTrue($target->set('key10', 'value10'));
		$this->assertTrue($target->set('key10', 'value10'));
	
		$this->assertEquals('value10', $target->get('key10'));
	}
	
	public function testMset() {
		$target = $this->getTarget();
		$this->assertEquals(1, $target->mset(array('key11'=>'value11')));
		$this->assertEquals('value11', $target->get('key11'));
	}
	
	public function testExists() {
		$target = $this->getTarget();
		$this->assertEquals(true, $target->exists('key1'));
		$this->assertEquals(false, $target->exists('non-exist'));
	}
	
	public function testDel() {
		$target = $this->getTarget();
		$this->assertEquals(1, $target->del('key1'));
		$this->assertEquals(0, $target->del('key1'));
	
		$this->setUp();
	}
	
	public function testMdel() {
		$target = $this->getTarget();
		$this->assertEquals(2, $target->mdel(array('key1', 'key2', 'non-exist')));
		$this->setUp();
	}
	
	public function testDeleteAll() {
		$this->setUp();
		$target = $this->getTarget();
		$this->assertEquals(3, $target->deleteAll());
		$this->assertEquals(0, $target->deleteAll());
		$this->setUp();
	}
	
}
