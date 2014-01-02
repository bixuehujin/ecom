<?php namespace ecom\tests\file;
/**
 * FileValidatorTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ecom\file\FileManager;

class FileValidatorTest extends \CTestCase {
	
	protected function setUp() {
		parent::setUp();
		
		$_FILES['name']=array(
			'name'=>'test_file.dat',
			'type'=>'somemime/type',
			'tmp_name'=>'/tmp/test_file',
			'error'=>UPLOAD_ERR_OK,
			'size'=>100,
		);
	}
	
	public function testFileValidatorSimple() {
		Yii::app()->fileManager->setDomains(array(
			'testDomain' => array(
				'validateRule' => array(
					'minSize' => 200
				),
			)
		));
		
		$model = new TestFileForm();
		$model->file = \CUploadedFile::getInstanceByName('name');
		$this->assertTrue($model->validate());
		
		
		$model = new TestFileForm('creation');
		$model->file = \CUploadedFile::getInstanceByName('name');
		$this->assertFalse($model->validate());
		
		$this->assertEquals('The file "test_file.dat" is too small. Its size cannot be smaller than 200 bytes.', $model->getError('file'));
	}
}

class TestFileForm extends \CFormModel {
	
	public $file;
	
	public function rules() {
		return [
			['file', 'ecom\file\FileValidator', 'domain' => 'testDomain', 'on' => 'creation']
		];
	}
}
