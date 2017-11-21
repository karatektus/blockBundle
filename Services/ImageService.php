<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 13.09.2017
 * Time: 16:25
 */

namespace Pluetzner\BlockBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Pluetzner\BlockBundle\Entity\ImageBlock;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Router;

/**
 * Class ImageService
 */
class ImageService
{

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * ImageService constructor.
     * @param Registry $doctrine
     * @param Router   $router
     * @param string   $rootDir
     */
    public function __construct(Registry $doctrine, Router $router, $rootDir)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->rootDir = $rootDir;
    }

    /**
     * @return Registry
     */
    private function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return Router
     */
    private function getRouter()
    {
        return $this->router;
    }

    /**
     * @return string
     */
    private function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @param ImageBlock $imageblock
     *
     * @return boolean
     */
    public function saveImage($imageblock)
    {
        /** @var UploadedFile $fileRef */
        $fileRef = $imageblock->getUploadedFile();
        if (null === $fileRef) {
            return false;
        }
        $file = file_get_contents($fileRef->getPath() . "/" . $fileRef->getFilename());
        $file = base64_encode($file);

        $imageblock
            ->setMimeType($fileRef->getMimeType())
            ->setImage($file);


        $manager = $this->getDoctrine()->getManager();
        $manager->persist($imageblock);
        $manager->flush();

        $guesser = new MimeTypeExtensionGuesser();
        $imageRoute = $this->getRouter()->generate('pluetzner_block_image_show', [
            'slug' => $imageblock->getSlug(),
            'height' => 0,
            'width' => 0,
            '_type' => $guesser->guess($imageblock->getMimeType())
        ]);

        $dir = dirname(sprintf('%s/../web%s', $this->getRootDir(), $imageRoute));
        if (false === file_exists($dir)) {
            mkdir($dir);
        }
        $files = scandir($dir);
        foreach ($files as $file) {
            $fp = sprintf('%s/%s', $dir, $file);
            if (is_file($fp)) {
                $res = unlink($fp); //delete file
            }
        }
        return true;
    }

    /**
     * Resize an image and copy it
     *
     * @param resource $src_img
     * @param string   $mimeType
     * @param int      $new_width
     * @param int      $new_height
     * @param string   $moveTo
     */
    public function resizeImage($src_img, $mimeType, $new_width, $new_height, $moveTo)
    {
        //make sure everything is actually an int value
        $new_width = intval($new_width);
        $new_height = intval($new_height);

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if ($new_width === 0 && $new_height == 0 && $new_width !== 0 && $new_height !== 0) {
            $thumb_w = $new_width;
            $thumb_h = $new_height;
        } elseif (($new_width === 0 && $new_height !== 0) || ($new_width !== 0 && $new_height === 0)) {
            if ($new_width === 0 && $new_height !== 0) {
                $thumb_w = $old_x / $old_y * $new_height;
                $thumb_h = $new_height;
            } else {
                $thumb_w = $new_width;
                $thumb_h = $old_y / $old_x * $new_width;
            }
        } else {
            $thumb_w = $old_x;
            $thumb_h = $old_y;
        }

        $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
        $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
        imagefilledrectangle($dst_img, 0, 0, $old_x, $old_y, $transparent);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        if (!file_exists(dirname($moveTo))) {
            mkdir(dirname($moveTo), 0755, true);
        }

        if ($mimeType == 'image/png') {
            $result = imagepng($dst_img, $moveTo, 0);
        } elseif ($mimeType == 'image/jpg' || $mimeType == 'image/jpeg' || $mimeType == 'image/pjpeg') {
            $result = imagejpeg($dst_img, $moveTo, 80);
        } elseif ($mimeType == 'image/gif') {
            $result = imagegif($dst_img, $moveTo);
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);
    }
}