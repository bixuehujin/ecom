<?php namespace ecom\tests\setting;
/**
 * DatabaseStorageTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use ecom\setting\storage\DatabaseStorage;

class DatabaseStorageTest extends SettingTestCase {
	
	protected $fixtures = array(
		'setting' => 'ecom\setting\storage\DatabaseStorage'
	);
	
	public function getTarget() {
		return new DatabaseStorage();
	}
}
