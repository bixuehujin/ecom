<?php namespace ecom\file;

interface FileManagedInterface
{
    public static function load($fid);

    public static function loadByHash($hash);

    public static function fetchAllAttachedOf(FileAttachable $entity, $usageType);

    public static function fetchAttachedProviderOf(FileAttachable $entity, $usageType);

    public function isAttachedTo(FileAttachable $entity, $usageType);

    public function attach(FileAttachable $entity, $usageType);

    public function detach(FileAttachable $entity, $usageType);

    public function replace(FileAttachable $entity, \CUploadedFile $uploadedFile, $usageType);

    public function getRealPath($force = false);

    public function getAccessUrl($force = false);

    public function getExtension();

    public function upload(\CUploadedFile $uploadedFile, $status);

    public function getUploadError();
}
