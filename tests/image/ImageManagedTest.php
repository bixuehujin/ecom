<?php namespace ecom\tests\image;
/**
 * ImageManagedTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ecom\image\model\ImageManaged;

class ImageManagedTest extends \CDbTestCase {

	protected function setUp() {
		parent::setUp();
	
		mkdir(__DIR__ . '/misc');
		mkdir(__DIR__ . '/misc/basePath');
		mkdir(__DIR__ . '/misc/thumbBasePath');
	
		$fileManager = Yii::createComponent(array(
			'class' => 'ecom\image\ImageManager',
			'basePath' => __DIR__ . '/misc/basePath',
			'thumbBasePath' => __DIR__ . '/misc/thumbBasePath',
			'domains' => array(
				'avatar' => array(),
			),
		));
		Yii::app()->setComponent('fileManager', $fileManager, false);
	}
	
	/**
	 * @return ImageManaged
	 */
	protected function getImageStub() {
		$stub = $this->getMock('ecom\image\model\ImageManaged', array('getRealPath'));
		
		$stub->expects($this->any())
			->method('getRealPath')
			->will($this->returnValue(__DIR__ . '/test.jpg'));
		
		return $stub;
	}
	
	public function testGetWidthAndHeight() {
		$image = $this->getImageStub();
		
		$this->assertEquals(1440, $image->getWidth());
		$this->assertEquals(900, $image->getHeight());
	}
	
	public function testResolveThumbOptions() {
		$url = '/path/to/thumb/avatar/12/23/1234|c-0-0-12-12.jpg';
		list($hash, $options) = ImageManaged::resolveThumbOptions($url);
		$this->assertEquals('12231234', $hash);
		$this->assertEquals(array('c-0-0-12-12'), $options);
		
		
		$url = '/path/to/thumb/avatar/12/23/1234%7Cc-0-0-12-12.jpg';
		list($hash, $options) = ImageManaged::resolveThumbOptions($url);
		$this->assertEquals('12231234', $hash);
		$this->assertEquals(array('c-0-0-12-12'), $options);
	}
	
	public function testGetThumbUrl() {
		$image = new ImageManaged();
		$image->hash = 'qwertyuio';
		$image->name = '1.jpg';
		$image->domain = 'avatar';
		
		$except = '/tests/image/misc/thumbBasePath/avatar/qw/er/tyuio|c-0-0-10-10.jpg';
		$this->assertEquals($except, $image->getThumbUrl('c-0-0-10-10'));
	}
	
	protected function tearDown() {
		parent::tearDown();
		\CFileHelper::removeDirectory(__DIR__ . '/misc');
	}
}
