<?php
/**
 * TreeEditActionTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

Yii::import('common.test.*');

class TreeEditActionTest extends ApiTestCase {
	
	public $baseUrl = 'http://localhost:7777';
	
	public $fixtures = array(
		'term_hierarchy' => 'TermHierarchy',
		'term_vocabulary' => 'TermVocabulary',
		'term' => 'Term',
	);
	
	
	public function testAddChild() {
		/* testing with wrong request method */
		$response = $this->get('tree/addchild');
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals(AjaxReturn::BAD_REQUEST_METHOD, $response->json()['code']);
		
		/* testing add new toplevel term */
		$response = $this->post('tree/addchild', array(
			'name' => 'new added term',
			'parent' => 0,
		));
		$this->assertEquals(200, $response->getStatusCode());
		$json = $response->json();

		$this->assertEquals(0, $json['code']);
		$this->assertEquals('new added term', $json['data']['name']);
		$this->assertEquals(0, $json['data']['parent']);
		
		/* testing add new sub term */
		$response = $this->post('tree/addchild', array(
			'name' => 'new sub term',
			'parent' => 2,
		));
		$this->assertEquals(200, $response->getStatusCode());
		$json = $response->json();
		
		$this->assertEquals(0, $json['code']);
		$this->assertEquals('new sub term', $json['data']['name']);
		$this->assertEquals(2, $json['data']['parent']);
	}
	
	public function testMove() {
		$response = $this->get(array('tree/move', 'id' => 5, 'parent' => 3, 'target' => 1));
		$this->assertEquals(200, $response->getStatusCode());
		$json = $response->json();
		
		$this->assertEquals(0, $json['code']);
		
		//get all children of node 1
		$response = $this->get(array('tree/nodes', 'node' => 1, 'show_status' => 1));
		$this->assertEquals(200, $response->getStatusCode());
		$json = $response->json();
		$this->assertEquals(0, $json['code']);
		$this->assertCount(3, $json['data']);
		
		//move back
		$response = $this->get(array('tree/move', 'id' => 5, 'parent' => 1, 'target' => 3));
		$this->assertEquals(200, $response->getStatusCode());
	}
	
	public function testRemove() {
		$response = $this->get(array('tree/remove', 'node' => 3));
		$this->assertEquals(200, $response->getStatusCode());
		
		$json = $response->json();
		$this->assertEquals(0, $json['code']);
		
		$this->setUp();
		TermHierarchy::model()->preload(1, true);
	}
}
