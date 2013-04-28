<?php
/**
 * FileAttachable interface file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since 2013-04-28
 */

/**
 * Interface to indicate an external entity can be attached to multiple files. 
 * The purpose of the interface is to tell (@see FileManaged) what the entity looks like.
 */
interface FileAttachable {
	/**
	 * Get the identifier of the entity.
	 * 
	 * @return integer
	 */
	public function getEntityId();
	
	/**
	 * Get the type of the entity.
	 * 
	 * @return string
	 */
	public function getEntityType();
	
	/**
	 * Get the number of files attached to the current entity.
	 * 
	 * @return integer|null  if the number is stored, an integer should be returned, otherwise null.
	 */
	public function getAttachedFileCount();
	
	/**
	 * Update the counter of how many files are attached to the entity. 
	 * If the counter do not stored, just leave the method blank.
	 * 
	 * @param integer $step
	 * @return boolean
	 */
	public function updateAttachedFileCounter($step);
}
