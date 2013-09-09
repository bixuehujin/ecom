<?php
/**
 * TreeController class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class TreeController extends CController {

	public function actions() {
		$treeSchema = array('tid:id', 'name', 'description', 'weight', 'parent');
		return array(
			'addchild' => array(
				'class' => 'common.actions.TreeEditAction',
				'action' => 'addchild',
				'treeSchema' => $treeSchema,
				'model' => TestTree::model(),
			),
			'nodes' => array(
				'class' => 'common.actions.TreeNodesAction',
				'vocabulary' => 1,
				'treeSchema' => $treeSchema,
			),
			'move' => array(
				'class' => 'common.actions.TreeEditAction',
				'action' => 'move',
				'model' => TestTree::model(),
			),
			'remove' => array(
				'class' => 'common.actions.TreeEditAction',
				'action' => 'remove',
				'model' => TestTree::model(),
			),
		);
	}
}
