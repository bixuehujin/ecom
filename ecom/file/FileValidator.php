<?php namespace ecom\file;
/**
 * FileValidator class file.
 *
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;

/**
 * This validator is a simple swrapper of Yii's CFileValidator.
 */
class FileValidator extends \CValidator
{
    public $domain = '';

    public $allowEmpty = false;


    protected function validateAttribute($object, $attribute)
    {
        $config = Yii::app()->fileManager->getDomain($this->domain);
        $rule = isset($config['validateRule']) ? $config['validateRule'] : array();

        if ($this->allowEmpty && !$object->$attribute) {
            return;
        }

        $validator = \CValidator::createValidator('file', $object, $attribute, $rule);
        $validator->validate($object, [$attribute]);
    }
}
