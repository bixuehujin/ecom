<?php namespace ecom\image;

use ecom\file\FileManagedInterface;

interface ImageManagedInterface extends FileManagedInterface
{
    public function getThumbUrl($params);

    public function getWidth();

    public function getHeight();
}
