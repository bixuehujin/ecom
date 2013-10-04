<?php namespace ecom\tests\settings;
/**
 * DatabaseStorageTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use ecom\settings\storage\DatabaseStorage;

class DatabaseStorageTest extends SettingsTestCase {
	
	protected $fixtures = array(
		'setting' => 'ecom\settings\storage\DatabaseStorage'
	);
	
	public function getTarget() {
		return new DatabaseStorage();
	}
}
