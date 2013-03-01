<?php
/**
 * ThumbBehavior class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class ThumbBehavior extends CBehavior {
	
	/**
	 * Generate and show a thumbnail image.
	 * 
	 * @param string $rect default to false
	 */
	public function actionThumb() {
		$args = $this->resolveArgs();
		if (!$args) {
			throw new CHttpException(404, 'Image Not Found.');
		}
		list($fileName, $size) = $args;
		$image = Image::loadByName($fileName);
		if (!$image) {
			throw new CHttpException(404, 'Image Not Found');
		}
		if((boolean)Yii::app()->getRequest()->getQuery('rect', false)) {
			$dim = $image->getCurrentDimensions();
			$min = $dim['width'] > $dim['height'] ? $dim['height'] : $dim['width'];
			$image->cropFormCenter($min);
		}
		$image->resize($size[0], $size[1]);
		$thumbBasePath = Yii::app()->fileManager->getThumbBasePath();
		$image->saveThumbFile($image->getThumbPath($size[0], $size[1]));
		$image->show();
	}
	
	protected function resolveArgs() {
		$path = Yii::app()->getRequest()->getUrl();
		if (($pos = strpos($path, '?')) !== false) {
			$path = substr($path, 0, $pos);
		}
		$info = pathinfo($path);
		$fileName = $info['filename'];
		$extension = $info['extension'];
		if (strpos($fileName, '_._') === false) {
			return false;
		}
		list($base, $size) = explode('_._', $fileName);
		if (strpos($size, 'x') === false) {
			return false;
		}
		
		$ret = array();
		$ret[] = $base . '.' . $extension;
		$ret[] = explode('x', $size);
		return $ret;
	}
	
}
