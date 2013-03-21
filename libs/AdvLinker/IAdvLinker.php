<?php
namespace AdvLinker;

interface IAdvLinker {
	
	public function setOptions($options);
	
	/**
	 * @param string $toUrl
	 * @return string
	 */
	public function getAdvUrl($toUrl);
	
}