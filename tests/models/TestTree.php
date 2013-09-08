<?php
/**
 * TestTree class for test usage.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TestTree extends Tree {
	
	public function vocabulary() {
		return TermVocabulary::load(1);		
	}
	
}
