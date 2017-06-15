<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 12.06.2017
 * Time: 00:08
 */
namespace Pluetzner\BlockBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Class ImageBlockModel
 * @package Pluetzner\BlockBundle\Model
 */
class ImageBlockModel
{
    /**
     * @var UploadedFile
     */
    private $uploadedFile;
    /**
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }
    /**
     * @param UploadedFile $uploadedFile
     * @return ImageBlockModel
     */
    public function setUploadedFile($uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
        return $this;
    }
}