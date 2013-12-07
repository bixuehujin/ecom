<?php namespace ecom\tests\image;
/**
 * ImageManagerTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;

class ImageManagerTest extends \CDbTestCase {
	
	protected function setUp() {
		parent::setUp();
	
		mkdir(__DIR__ . '/misc');
		mkdir(__DIR__ . '/misc/basePath');
		mkdir(__DIR__ . '/misc/thumbBasePath');
	
		$fileManager = Yii::createComponent(array(
			'class' => 'ecom\image\ImageManager',
			'basePath' => __DIR__ . '/misc/basePath',
			'thumbBasePath' => __DIR__ . '/misc/thumbBasePath',
		));
		Yii::app()->setComponent('fileManager', $fileManager, false);
	}
	
	public function testGetBasePaths() {
		$thumbBasePath = Yii::app()->fileManager->getThumbBasePath();
		$this->assertEquals(__DIR__ . '/misc/thumbBasePath', $thumbBasePath);
	}
	
	protected function tearDown() {
		parent::tearDown();
		\CFileHelper::removeDirectory(__DIR__ . '/misc');
	}
}
