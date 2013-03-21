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
		$template = $this->feedBack == null ? $this->tempalte : str_replace('<feed_back>', $this->feedBack, $this->tempalte);
		return $this->baseUrl . $template . '&t=' . $toUrl;
	}
}
