<?php
/**
 * RedirectFilter class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * Filter for redirect on certain conditions.
 */
class RedirectFilter extends CFilter {
	/**
	 * callback function whose return value indicates whether redirect or not.
	 * true for redirect, otherwise not.
	 * 
	 * @var callable
	 */
	public $callback;
	/**
	 * PHP expression whose value indicates whether redirect or not.
	 * 
	 * @var string
	 */
	public $expression;
	/**
	 * @var string
	 */
	public $url;
	
	
	protected function preFilter($filterChain) {
		$controller = Yii::app()->controller;
		if (is_callable($this->callback)) {
			if (call_user_func($this->callback)) {
				$controller->redirect($this->url);
			}
		}
		if($controller->evaluateExpression($this->expression)) {
			$controller->redirect($this->url);
		}
 		return true;
	}
}
