<?php namespace ecom\tests\file;
/**
 * FileManagerTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ecom\file\FileManager;

class FileManagerTest extends \CTestCase {
	
	public function testBasePath() {
		$component = Yii::createComponent('ecom\file\FileManager');
		$component->setBasePath('/tmp');
		$this->assertEquals('/tmp', $component->getBasePath());
		
		$component->setBasePath('file');
		$this->assertEquals(__DIR__, $component->getBasePath());
	}
	
	public function testBaseUrl() {
		$manager = Yii::createComponent('ecom\file\FileManager');
	}
}
