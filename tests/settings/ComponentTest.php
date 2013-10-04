<?php namespace ecom\tests\settings;
/**
 * ComponentTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class ComponentTest extends SettingsTestCase {
	
	protected $fixtures = array(
		'setting' => 'ecom\settings\storage\DatabaseStorage'
	);
	
	public function getTarget() {
		return \Yii::createComponent(array(
			'class' => 'ecom\settings\Settings',
		));
	}
	
	public function testArrayGet() {
		$target = $this->getTarget();
		$this->assertEquals('value1', $target['key1']);
		$this->assertEquals(null, $target['non-exist']);
	}
	
	public function testArraySet() {
		$target = $this->getTarget();
		$target['key10'] = 'value10';
		$this->assertEquals('value10', $target['key10']);
	}
	
	public function testArrayIsset() {
		$this->setUp();
		$target = $this->getTarget();
		$this->assertEquals(true, isset($target['key1']));
		$this->assertEquals(false, isset($target['key11']));
	}
	
	public function testArrayUnset() {
		$this->setUp();
		$target = $this->getTarget();
		unset($target['key1']);
		$this->assertEquals(false, isset($target['key1']));
	}
}
