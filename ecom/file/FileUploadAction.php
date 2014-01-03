<?php namespace ecom\file;
/**
 * FileUploadAction class file.
 *
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since  2012-04-09
 */

use Yii;

/**
 * Action used upload file.
 */
class FileUploadAction extends \CAction
{
    /**
     * The form name that holds the uploading file.
     *
     * @var string
     */
    public $name = 'file';
    /**
     * Whether save the uploaded file as persistent.
     *
     * @var boolean
     */
    public $savePersistent = false;
    /**
     * Specify which domain the file belongs to.
     *
     * @var string
     */
    public $domain;

    public function run()
    {
        $uploadedFile = \CUploadedFile::getInstanceByName($this->name);
        if (!$uploadedFile) {
            $this->render(array('status' => 1, 'message' => "No file uploaded in name: '{$this->name}'."));
        }
        $fileManaged = Yii::app()->fileManager->createManagedObject($this->domain);
        $newFile = $fileManaged->upload($uploadedFile, $this->savePersistent ? FileManaged::STATUS_PERSISTENT : FileManaged::STATUS_TEMPORARY);
        if ($newFile) {
            $this->render(array('status' => 0, 'message' => 'Ok', 'data' => $newFile->getAttributes()));
        } else {
            $this->render(array('status' => 2, 'message' => $fileManaged->getUploadError()));
        }
    }

    protected function render($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        Yii::app()->end();
    }
}
