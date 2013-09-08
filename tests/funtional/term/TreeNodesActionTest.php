<?php
/**
 * TreeNodesActionTest class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */
class TreeNodesActionTest extends ApiTestCase {

	public $baseUrl = 'http://localhost:7777';
	
	public function testNodes() {
		/* testing for toplevel nodes */
		$response = $this->get('tree/children');
		
	}
}
