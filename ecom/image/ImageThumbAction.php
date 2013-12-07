<?php namespace ecom\image;
/**
 * ImageThumbAction class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since 2013-04-21
 */

use Yii;
use ecom\image\model\ImageManaged;

class ImageThumbAction extends \CAction {
	
	
	public function run() {
		list($hash, $options) = ImageManaged::resolveThumbOptions(Yii::app()->request->getRequestUri());
		
		$image = ImageManaged::loadByHash($hash);
		
		if (!$image) {
			throw new \CHttpException(404, 'Image Not Found.');
		}
		
		$image->renderThumb($options, true, true);
		
	}
}
