<?php namespace ecom\file\model;
/**
 * FileManaged class file.
 *
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ecom\file\FileManager;
use ecom\file\FileAttachable;
use ecom\file\FileManagedInterface;

/**
 *
 * @property string $accessUrl  Web accessable url of this file.
 * @property string $realPath   The saving path.
 *
 * @property string $domain     The domain the file belongs to.
 * @property string $name       The origining uploading name of the file
 * @property string $hash
 * @property string $mime
 * @property string $size
 * @property integer $status
 * @property integer $created
 */
class FileManaged extends \CActiveRecord implements FileManagedInterface
{
    const STATUS_TEMPORARY  = 0;
    const STATUS_PERSISTENT = 1;

    private $_realPath;
    private $_accessUrl;

    public $validateRule = array();
    public $behaviors = array();
    /**
     * The domain the file belongs to.
     * @var string
     */
    public $domainBelongs;

    public $uploaded;

    /**
     * @return FileManaged
     */
    final public static function model($className = null)
    {
        return  parent::model(get_called_class());
    }

    public function tableName()
    {
        return 'file_managed';
    }

    public function rules()
    {
        $rules = array();
        if ($this->validateRule) {
            $rules[] = array('uploaded', 'file') + $this->validateRule;
        }

        return $rules;
    }

    public function behaviors()
    {
        return $this->behaviors + array(
            'timestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => null,
            ),
        );
    }

    /**
     * Get the file extension of the file.
     *
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    /**
     * Upload a file and save to database.
     *
     * @param  CUploadedFile       $uploadedFile
     * @param  integer             $status
     * @return FileManaged|boolean
     */
    public function upload(\CUploadedFile $uploadedFile, $status = self::STATUS_TEMPORARY)
    {
        $this->clearErrors();

        $uploadedFile->getHasError();
        if ($uploadedFile->getHasError()) {
            $this->addError('uploaded', $uploadedFile->getError());

            return false;
        }

        $this->uploaded = $uploadedFile;

        if (!$this->validate(array('uploaded'))) {
            return false;
        }

        $newFile = clone $this;
        $newFile->setIsNewRecord(true);
        $newFile->attachBehaviors($newFile->behaviors());

        $newFile->name   = $uploadedFile->getName();
        $newFile->mime   = $uploadedFile->getType();
        $newFile->size   = $uploadedFile->getSize();
        $newFile->uid    = Yii::app()->user->getId();
        $newFile->status = $status;
        $newFile->domain = $this->domainBelongs;

        $hash = base64_encode(sha1(sprintf('%s:%s:%s', Yii::app()->user->getId(), time(), uniqid()), true));
        $hash = rtrim(strtr($hash, '+/', '-_'), '=');

        $newFile->hash   = $hash;

        if ($newFile->save(false)) {
            $path = $newFile->getRealPath();
            $dir = dirname($path);

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            if ($uploadedFile->saveAs($path)) {
                return $newFile;
            } else {
                $newFile->delete();
            }
        }
        $this->addError('uploaded', 'Failed to upload file');

        return false;
    }

    /**
     * @return array
     */
    public function getUploadError()
    {
        return $this->getError('uploaded');
    }

    /**
     * Delete database record and remove file.
     *
     * @param  integer $file
     * @return boolean
     */
    private function remove()
    {
        $realpath = $this->getRealPath();
        if (!file_exists($realpath) || unlink($realpath)) {
            $this->delete();

            return true;
        } else {
            return false;
        }
    }

    public function beforeSave()
    {
        if ($this->getIsNewRecord()) {
            $this->created = time();//TODO fix
        }

        return parent::beforeSave();
    }

    /**
     * Check whether the file is attached an external entity.
     *
     * @param  FileAttachable $entity
     * @return boolean
     */
    public function isAttachedTo(FileAttachable $entity, $usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $entityId = $entity->getEntityId();
        $entityType = $entity->getEntityType();

        $usage = FileUsage::model();
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            'fid' => $this->fid,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ));

        return $usage->exists($criteria);
    }

    /**
     * Attach the current file to an entity.
     *
     * @param  FileAttchable $entity
     * @param  integer       $count
     * @return boolean
     */
    public function attach(FileAttachable $entity, $usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $entityId = $entity->getEntityId();
        $entityType = $entity->getEntityType();

        $usage = new FileUsage();
        $usage->fid         = $this->fid;
        $usage->entity_id   = $entityId;
        $usage->entity_type = $entityType;
        $usage->type        = $usageType;

        if ($usage->save(false)) {
            $entity->updateAttachedFileCounter($usageType, 1);

            if ($this->status == self::STATUS_TEMPORARY) {
                $this->status = self::STATUS_PERSISTENT;
                $this->save(false, array('status'));
            }

            return true;
        }

        return false;
    }

    /**
     *
     * @param  FileAttachable $entity
     * @param  unknown        $usageType
     * @return boolean
     */
    public function detach(FileAttachable $entity, $usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $entityId = $entity->getEntityId();
        $entityType = $entity->getEntityType();

        $pk = array(
            'fid' => $this->fid,
            'entity_id' => $entityId,
            'entity_type' => $entityType,
            'type' => $usageType,
        );
        if (FileUsage::model()->deleteByPk($pk)) {
            if (!$this->getUsageCount($usageType)) {
                $this->remove();
            }

            return true;
        }

        return false;
    }

    /**
     * Replace the current file to another uploaded file.
     *
     * @param  FileAttachable    $entity
     * @param  \CUploadedFile    $uploadedFile
     * @param  integer           $usageType
     * @return FileManaged|false The new file object.
     */
    public function replace(FileAttachable $entity, \CUploadedFile $uploadedFile, $usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $usageCount = $this->getUsageCount($usageType);
        if ($usageCount == 1) {
            $this->name = $uploadedFile->getName();
            $this->mime = $uploadedFile->getType();
            $this->size = $uploadedFile->getSize();
            $realPath = $this->getRealPath();
            if ((!file_exists($realPath) || unlink($rea)) && $uploadedFile->saveAs($this->getRealPath(true))) {
                if ($this->save(false, array('name', 'mime', 'size'))) {
                    return $this;
                }
            }

            return false;
        }

        $fileManaged = Yii::app()->fileManager->createManagedObject($this->domain);
        if (($newFile = $fileManaged->upload($uploadedFile, self::STATUS_PERSISTENT)) && $newFile->attach($entity, $usageType)) {
            $this->detach($entity, $usageType);

            return $newFile;
        }

        return false;
    }

    /**
     * Update the usage information of the current file.
     *
     * @param  FileAttachable $entity
     * @param  integer        $step
     * @return boolean
     *//*
    public function updateUsage(FileAttachable $entity, $step, $usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $usage = FileUsage::model()->findByAttributes(array(
            'fid' => $this->fid,
            'entity_id' => $entity->getEntityId(),
            'entity_type' => $entity->getEntityType(),
            'type' => $usageType
        ));

        if (!$usage) {
            if ($step < 0) {
                return true;
            }

            $usage = new FileUsage();
            $usage->fid = $this->fid;
            $usage->entity_id = $entity->getEntityId();
            $usage->entity_type = $entity->getEntityId();
            $usage->type = $usageType;

            if (!$usage->save(false)) {
                return false;
            }

            $entity->updateAttachedFileCounter($usageType, 1);
            if ($this->status == self::STATUS_TEMPORARY) {
                $this->status = self::STATUS_PERSISTENT;
                $this->save(false, array('status'));
            }

            return true;
        }

        $usage->count += $step;

        if ($usage->count > 0) {
            return $usage->save(false, array('count'));
        } else {
            if ((boolean) $usage->delete()) {
                $entity->updateAttachedFileCounter($usageType, -1);

                return true;
            } else {
                return false;
            }
        }
    }

    public function replaceUsage(FileAttachable $entity, FileManaged $newFile, $count = 1, $usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
    }
    */

    /**
     * Get web accessable URL of the file.
     *
     * @return string
     */
    public function getAccessUrl($force = false)
    {
        if ($this->_accessUrl !== null && $force === false) {
            return $this->_accessUrl;
        }

        $hash = $this->hash;

        $url = Yii::app()->fileManager->getUrlOfDomain($this->domain) . '/'
            . substr($hash, 0, 2) . '/'
            . substr($hash, 2, 2) . '/'
            . substr($hash, 4);

        if ($ext = $this->getExtension()) {
            $url .= '.' . $ext;
        }

        return $this->_accessUrl = $url;
    }

    /**
     * @param  string $force
     * @return string
     */
    public function getAbsoluteAccessUrl($force = false)
    {
        return Yii::app()->request->getHostInfo() . $this->getAccessUrl($force);
    }

    /**
     * Get the saving path of the file.
     *
     * @return string
     */
    public function getRealPath($force = false)
    {
        if ($this->_realPath !== null && $force === false) {
            return $this->_realPath;
        }

        $hash = $this->hash;

        $path = Yii::app()->fileManager->getPathOfDomain($this->domain) . '/'
            . substr($hash, 0, 2) . '/'
            . substr($hash, 2, 2) . '/'
            . substr($hash, 4);

        if ($ext = $this->getExtension()) {
            $path .= '.' . $ext;
        }

        return $this->_realPath = $path;
    }

    public function getUsageCount($usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'fid' => $this->fid,
            'type' => $usageType,
        ));

        return FileUsage::model()->count($criteria);
    }

    /**
     * Load a file from database by fid.
     *
     * @param  string      $fid
     * @return FileManaged
     */
    public static function load($fid)
    {
        return static::model()->findByPk($fid);
    }

    /**
     * @param  string      $hash
     * @return FileManaged
     */
    public static function loadByHash($hash)
    {
        return static::model()->findByAttributes(array('hash' => $hash));
    }

    /**
     * Get the count of all attached files.
     *
     * @param FileAttachable $entity
     * @param integer $usageType
     * @return integer
     */
    public static function fetchAttachedCountOf(FileAttachable $entity, $usageType = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'entity_id' => $entity->getEntityId(),
            'entity_type' => $entity->getEntityType(),
            'type' => $usageType,
        ));

        return FileUsage::model()->count($criteria);
    }

    /**
     * Fetch all files attached to spefified entity and its type.
     *
     * @param FileAttachable     $entity
     * @param integer $type
     * @return FileManaged
     */
    public static function fetchAllAttachedOf(FileAttachable $entity, $type = FileAttachable::USAGE_TYPE_DEFAULT)
    {
        $criteria = new \CDbCriteria();
        $criteria->alias = 'f';
        $criteria->join  = 'left join file_usage as u on u.fid=f.fid';
        $criteria->addColumnCondition(array(
            'entity_id' => $entity->getEntityId(),
            'entity_type' => $entity->getEntityType(),
            'type' => $type,
        ));

        return static::model()->findAll($criteria);
    }

    /**
     * Fetch files attached to spefified entity and its type.
     *
     * @param FileAttachable $entity
     * @param integer $type
     * @param integer $pageSize
     * @return \CActiveDataProvider
     */
    public static function fetchAttachedProviderOf(FileAttachable $entity, $type = FileAttachable::USAGE_TYPE_DEFAULT, $pageSize = 20)
    {
        $criteria = new \CDbCriteria();

        $criteria->alias = 'f';
        $criteria->join  = 'left join file_usage as u on u.fid=f.fid';

        $criteria->addColumnCondition(array(
            'entity_id' => $entity->getEntityId(),
            'entity_type' => $entity->getEntityType(),
            'type' => $type,
        ));

        $provider = new \CActiveDataProvider(get_called_class(), array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $pageSize,
            ),
        ));

        return $provider;
    }

    /**
     * Returns whether the file is existed according hash.
     *
     * @return boolean
     */
    public static function isFileExist($hash)
    {
        return (bool) $this->findByAttributes(array('hash' => $hash));
    }
}
