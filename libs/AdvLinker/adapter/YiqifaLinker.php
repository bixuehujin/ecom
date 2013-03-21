<?php
namespace AdvLinker\adapter;
use \AdvLinker\IAdvLinker;

class YiqifaLinker implements IAdvLinker {
	
	public $baseUrl = 'http://p.yiqifa.com/c?';
	
	private $tempalte;
	
	private $feedBack;
	
	
	/**
	 * @param array $options
	 *  + template: the template of url.
	 *  + feed_back: the feed back tag.
	 */
	public function setOptions($options = array()) {
		if (!isset($options['template'])) {
			throw new \InvalidArgumentException('Undefined key "template" in options.', 1, null);
		}else {
			$this->tempalte = $options['template'];
		}
		$this->feedBack = isset($options['feed_back']) ? urlencode($options['feed_back']) : null;
	}
	
	public function getAdvUrl($toUrl) {
		$feedBack = $this->feedBack ?: '';
		$template = strtr($this->tempalte, array('<feed_back>' => $feedBack));
		return $this->baseUrl . $template . '&t=' . $toUrl;
	}
}
