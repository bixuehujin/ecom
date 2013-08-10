<?php
namespace AdvLinker\adapter;
use \AdvLinker\IAdvLinker;

class LinktechLinker implements IAdvLinker {
	
	public $baseUrl = 'http://click.linktech.cn/?';
	
	private $options;
	
	
	/**
	 * Set the linker options.
	 * 
	 * @param array $options
	 *  + merchant_name: merchant name in linktech
	 *  + feed_back: the feed back tag.
	 *  + site_id: 
	 */
	public function setOptions($options = array()) {
		if (!isset($options['merchant_name'])) {
			throw new \InvalidArgumentException('Undefined key "mercant_name" in options.');
		}
		if (!isset($options['site_id'])) {
			throw new \InvalidArgumentException('Undefined key "site_id" in options.');;
		}
		$this->options = $options;
	}
	
	public function getAdvUrl($toUrl) {
		if (in_array($this->options['merchant_name'], array('amazon'))) {
			$tmpl = '{to_url}?tag=lktrb-23&ascsubtag={site_id}{feed_back}';
		}else {
			$tmpl = '{base_url}m={merchant_name}&a={site_id}&l=99999&l_cd1=0&l_cd2=1&u_id={feed_back}&tu={to_url}';
		}
		return strtr($tmpl, $this->getTrMap($toUrl));
	}
	
	protected function getTrMap($toUrl) {
		$map = $this->options + array(
			'to_url' => $toUrl,
			'feed_back' => 0,
			'base_url' => $this->baseUrl,
		);
		$nmap = array();
		foreach ($map as $key => $value) {
			$nmap['{' . $key . '}'] = $value;
		}
		return $nmap;
	}
}
