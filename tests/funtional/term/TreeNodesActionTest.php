<?php
/**
 * TreeNodesActionTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

Yii::import('common.test.*');

class TreeNodesActionTest extends ApiTestCase {

	public $baseUrl = 'http://localhost:7777';
	
	public $fixtures = array(
		'term_hierarchy' => 'TermHierarchy',
		'term_vocabulary' => 'TermVocabulary',
		'term' => 'Term',
	);
	
	public function testNodesOfToplevel() {
		$response = $this->get('tree/nodes');
		$this->assertEquals(200, $response->getStatusCode());
		$json = $response->json();
		
		$this->assertEquals(0, $json['code']);
		$this->assertCount(2, $json['data']);
		$this->assertEquals('level_1', $json['data'][0]['name']);
		$this->assertEquals('level_2', $json['data'][1]['name']);
	}
	
	public function testNodesThatNotToplevel() {
		$response = $this->get(array('tree/nodes', 'node' => 1));
		$this->assertEquals(200, $response->getStatusCode());
		
		$json = $response->json();
		$this->assertEquals(0, $json['code']);
		$this->assertCount(2, $json['data']);
		$this->assertEquals('level_1.1', $json['data'][0]['name']);
		$this->assertEquals('level_1.2', $json['data'][1]['name']);
	}
}
