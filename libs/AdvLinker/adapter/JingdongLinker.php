<?php
/**
 * Jingdong AdvLinker generator class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

namespace AdvLinker\adapter;
use \AdvLinker\IAdvLinker;

class JingdongLinker implements IAdvLinker {
	
	public $baseUrl = 'http://click.union.360buy.com/JdClick/?';
	
	private $options;
	
	/**
	 * Set the linker options.
	 * 
	 * @param array $options 
	 *  + unionId: The unionId
	 *  + pageType: Page type of target url, one of 1->5, defaults 4.
	 * 
	 * @see \AdvLinker\IAdvLinker::setOptions()
	 */
	public function setOptions($options = array()) {
		if (!isset($options['unionId'])) {
			throw new \InvalidArgumentException('Undefained key "uniodId" in options.', 1, null);
		}else {
			$this->options['unionId'] = $options['unionId'];
		}
		if (!isset($options['pageType'])) {
			$options['t'] = 4;
		}else {
			$options['t'] = $options['pageType'];
		}
	}
	
	public function getAdvUrl($toUrl) {
		$str = http_build_query($this->options);
		return $this->baseUrl . $str . '&to=' . $toUrl;
	}
}
