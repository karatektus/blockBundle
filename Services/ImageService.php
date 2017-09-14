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
     */
    public function saveImage($imageblock)
    {
        /** @var UploadedFile $fileRef */
        $fileRef = $imageblock->getUploadedFile();
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
        $files = scandir($dir);
        foreach ($files as $file) {
            $fp = sprintf('%s/%s', $dir, $file);
            if (is_file($fp)) {
                $res = unlink($fp); //delete file
            }
        }
    }
}