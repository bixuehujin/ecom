<?php
/**
 * The AdvLinker generator test file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

Yii::setPathOfAlias('AdvLinker', Yii::getPathOfAlias('common.libs.AdvLinker'));

use AdvLinker\AdvLinker;

class AdvLinkerTest extends CTestCase {
	
	public function testYiqifa() {
		$linker = AdvLinker::instance('yiqifa', array(
			'template' => 's=4714ba7b&w=624413&c=6804&i=23503&l=0&e=<feed_back>',
			'feed_back' => 'test feed back',
		));
		$except = 'http://p.yiqifa.com/c?s=4714ba7b&w=624413&c=6804&i=23503&l=0&e=test+feed+back&t=http://www.baidu.com';
		$this->assertEquals($except, $linker->getAdvUrl('http://www.baidu.com'));
	}
	
	public function testJindong() {
		
	}
}
