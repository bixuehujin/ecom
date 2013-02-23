<?php
/**
 * File Usage class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * Records where a file is used.
 * 
 */
class FileUsage extends CActiveRecord {

	private $_domain;
	private $_eid;
	private $_fm;
	
	/**
	 * @return FileUsage
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'file_usage';
	}
	
	public function setDomain($domain) {
		$this->_domain = $domain;
		return $this;
	}
	
	public function setEid($eid) {
		$this->_eid = $eid;
		return $this;
	}
	
	public function setFileManaged(FileManaged $fileManaged) {
		$this->_fm = $fileManaged;
		return $this;
	}
	
	/**
	 * Add usage tracking to a file.
	 * 
	 * @param integer|FileManaged $file
	 * @param string $domain
	 * @param integer $id
	 * @param integer $count
	 * @return mixed
	 */
	public function addUsage($file, $count = 1) {
		list($eid, $domain, $fid) = $this->getIdentifier($file);
		$usage = new FileUsage();
		$usage->id = $eid;
		$usage->domain = $domain;
		$usage->fid = $fid;
		$usage->count = $count;
		return $usage->save(false);
	}
	
	public function deleteUsage($file) {
		list($eid, $domain, $fid) = $this->getIdentifier($file);
		$allUsages = $this->getAllUsage($fid);
		$currIndex = $domain . '-' . $eid;
		if (!isset($allUsages[$currIndex])) {
			return false;
		}
		$currUsage = $allUsages[$currIndex];
		unset($allUsages[$currIndex]);
		if (empty($allUsages)) {
			if (!isset($this->_fm)) {
				throw new CException('Property {fileManaged} is unset.');
			}
			$this->_fm->remove($currUsage->fid);
		}
		$currUsage->delete();
		return true;
	}
	
	public function updateUsageCounter($file, $count) {
		
	}
	
	public function changeUsage($file, $count = 1) {
		list($eid, $domain, $fid) = $this->getIdentifier($file);
		
		$allUsages = $this->getAllUsage($fid);
		$currIndex = $domain . '-' . $eid;
		if (!isset($allUsages[$currIndex])) {
			return $this->addUsage($file, $count);
		}
		$currUsage = $allUsages[$currIndex];
		unset($allUsages[$currIndex]);
		if (empty($allUsages)) {
			if (!isset($this->_fm)) {
				throw new CException('Property {fileManaged} is unset.');
			}
			$this->_fm->remove($currUsage->fid);
		}
		$currUsage->fid = $fid;
		$currUsage->count = $count;
		return $currUsage->save(false, array('fid', 'count'));
	}
	
	public function getAllUsage($fid) {
		$usages = $this->findAllByAttributes(array('fid' => $fid));
		return Utils::arrayColumns($usages, null, array('domain', 'id'));
	}
	
	public function getAllUsageCount($fid) {
		
	}
	
	public function clearAllUsage($file) {
		
	}
	
	protected function getIdentifier($file) {
		if (!isset($this->_eid, $this->_domain)) {
			throw new CException("Property eid or domain is unset.");
		}
		$ret = array();
		$ret[] = $this->_eid;
		$ret[] = $this->_domain;
		$ret[] = $file instanceof FileManaged ? $file->fid : file;
		return $ret;
	}
}
