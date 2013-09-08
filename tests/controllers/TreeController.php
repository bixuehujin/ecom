<?php
/**
 * TreeController class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TreeController extends CController {

	public function actions() {
		return array(
			'addchild' => array(
				'class' => 'common.actions.TreeEditAction',
				'action' => 'addchild',
				'treeSchema' => array('tid:id', 'name', 'description', 'weight', 'parents'),
				'termModel' => TestTree::model(),
			),
		);
	}
}
