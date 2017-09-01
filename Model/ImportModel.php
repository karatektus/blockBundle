<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 31.08.2017
 * Time: 15:35
 */

namespace Pluetzner\BlockBundle\Model;


/**
 * Class ImportModel
 * @package Pluetzner\BlockBundle\Model
 */
class ImportModel
{
  /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     *
     */
    private $uploadedFile;

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @param $uploadedFile
     *
     * @return ImportModel
     */
    public function setUploadedFile($uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }
}