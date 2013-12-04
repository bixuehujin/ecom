<?php namespace ecom\tests\file;
/**
 * FileManagedTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ecom\file\FileManager;
use ecom\file\FileAttachable;
use ecom\file\model\FileUsage;
use ecom\file\model\FileManaged;


class FileManagedTest extends \CDbTestCase {
	
	protected $fixtures = array(
		'file_managed' => 'ecom\file\model\FileManaged',
		'file_usage' => 'ecom\file\model\FileUsage',
	);
	
	protected function setUp() {
		parent::setUp();
		Yii::app()->user->setId(1);
	}
	
	public function testGetAccessUrl() {
		$fileManaged = Yii::app()->fileManager->createManagedObject('avatar');
	
		$fileManaged->domain = 'avatar';
		$fileManaged->name = 'test.jpg';
		$fileManaged->hash = 'qwerqwer';
		$this->assertEquals(__DIR__ . '/avatar/qw/er/qwer.jpg', $fileManaged->getRealPath());
		
		var_dump($fileManaged->getAccessUrl(true));
	}
	
	protected function getUploadedFileStub($fileName = 'test.jpg') {
		$builder = $this->getMockBuilder('CUploadedFile');
		$args = array($fileName, '/tmp/xxx', 'image/jpeg', 100, 0);
		$builder->setConstructorArgs($args);
		$builder->setMethods(array('saveAs'));
		
		$stub = $builder->getMock();
		$stub->expects($this->any())
			->method('saveAs')
			->will($this->returnValue(true));
		
		return $stub;
	}
	
	protected function createManagedObject($rule) {
		return Yii::createComponent(array(
			'class' => 'ecom\file\FileManager',
			'basePath' => __DIR__,
			'domains' => array(
				'avatar' => array(
					'validateRule' => $rule,
				),
			),
		))->createManagedObject('avatar');
	} 
	
	public function testValidate() {
		$rules = array(
			array('minSize' => 200), //too small error
			array('maxSize' => 50), //too large error
		);
		$excepts = array(
			array(false, 'The file "test.jpg" is too small. Its size cannot be smaller than 200 bytes.'),
			array(false, 'The file "test.jpg" is too large. Its size cannot exceed 50 bytes.'),
		);
		
		foreach ($rules as $key => $rule) {
			$fileManaged = $this->createManagedObject($rule);
			$result = $fileManaged->upload($this->getUploadedFileStub());
			list($retval, $message) = $excepts[$key];
			
			$this->assertEquals($retval, $result);
			$this->assertEquals($message, $fileManaged->getUploadError());
		}
	}
	
	protected function uploadTempFile() {
		$stub = $this->getUploadedFileStub();
		$fileManaged = $this->createManagedObject(array());
		
		return $fileManaged->upload($stub);
	}
	
	public function testUpload() {
		$newFile = $this->uploadTempFile();
		
		$this->assertInstanceOf('ecom\file\model\FileManaged', $newFile);
		
		$this->assertEquals(FileManaged::STATUS_TEMPORARY, $newFile->status);
		$this->assertGreaterThan(0, $newFile->fid);
		
		$this->assertEquals(1, FileManaged::model()->count());
		
		$entity = new TestEntity(1);
		$this->assertEquals(true, $newFile->attach($entity));
		$this->assertEquals(FileManaged::STATUS_PERSISTENT, $newFile->status);
		$this->assertEquals(1, FileUsage::model()->count());
	}
	
	public function testReplace() {
		$newFile = $this->uploadTempFile();
		$entity1 = new TestEntity(1);
		$newFile->attach($entity1);
		
		
		$replacedFile = $newFile->replace($entity1, $this->getUploadedFileStub('2.jpg'));
		$this->assertEquals($newFile->fid, $replacedFile->fid);
		$this->assertEquals('2.jpg', $replacedFile->name);
		$this->assertEquals(1, FileUsage::model()->count());
		
		$entity2 = new TestEntity(2);
		$newFile->attach($entity2);
		$this->assertEquals(2, FileUsage::model()->count());
		$this->assertEquals(1, FileManaged::model()->count());
		
		
		$replacedFile = $newFile->replace($entity1, $this->getUploadedFileStub('3.jpg'));
		$this->assertNotEquals($replacedFile->fid, $newFile->fid);
		$this->assertEquals(2, FileUsage::model()->count());
		
		$this->assertEquals(true, $replacedFile->detach($entity1));
		$this->assertEquals(false, $newFile->detach($entity1));
		$this->assertEquals(true, $newFile->detach($entity2));
	}
}


class TestEntity implements FileAttachable {
	
	private $_id;
	
	public function __construct($id) {
		$this->_id = $id;
	}
	
	public function getEntityId() {
		return $this->_id;
	}
	
	public function getEntityType() {
		return 'test';
	}
	
	public function updateAttachedFileCounter($usageType, $step) {
		
	}
	
	public function getAttachedFileCount() {
		return null;
	}
}
