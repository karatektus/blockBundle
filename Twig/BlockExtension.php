<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 11.06.2017
 * Time: 16:17
 */

namespace Pluetzner\BlockBundle\Twig;

use Pluetzner\BlockBundle\Entity\ImageBlock;
use Pluetzner\BlockBundle\Entity\TextBlock;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

/**
 * Class BlockExtension
 *
 * @package Pluetzner\BlockBundle\Twig
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $rootdir;

    /**
     * @var SecurityExtension
     */
    private $twig;

    /**
     * BlockExtension constructor.
     *
     * @param RegistryInterface $doctrine
     * @param Router $router
     * @param string $rootdir
     * @param SecurityExtension $twig
     */
    public function __construct(RegistryInterface $doctrine, Router $router, $rootdir = '', SecurityExtension $twig)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->rootdir = $rootdir;
        $this->twig = $twig;
    }

    /**
     * @return RegistryInterface
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return string
     */
    public function getRootdir()
    {
        return $this->rootdir;
    }

    /**
     * @return SecurityExtension
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('imageblock', [$this, 'getImageBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('textblock', [$this, 'getTextBlock'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $slug
     * @return string
     */
    public function getTextBlock($slug)
    {
        $textblock = $this->getDoctrine()->getRepository(TextBlock::class)->findOneBy(['slug' => $slug]);

        if (null === $textblock) {
            $textblock = new TextBlock();
            $textblock
                ->setSlug($slug)
                ->setText('No such Textblock');

            $this->getDoctrine()->getManager()->persist($textblock);
            $this->getDoctrine()->getManager()->flush();
        }


        $editData = '"';
        if ($this->getTwig()->isGranted('ROLE_ADMIN')) {
            $route = $this->getRouter()->generate('pluetzner_block_textblock_editajax', ['id' => $textblock->getId()]);
            $editData = sprintf(' textblock" data-href="%s"', $route);
        }

        return sprintf('<p class="%s>%s</p>', $editData, $textblock->getText());
    }

    /**
     * @param string $slug
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function getImageBlock($slug, $width = 0, $height = 0)
    {
        $imageBlock = $this->getDoctrine()->getRepository(ImageBlock::class)->findOneBy(['slug' => $slug]);

        if (null === $imageBlock) {
            $path = $this->getRootdir() . '/../src' . '/Pluetzner/BlockBundle/Resources/public/img/no_image_thumb.gif';
            $data = file_get_contents($path);

            $imageBlock = new ImageBlock();
            $imageBlock
                ->setSlug($slug)
                ->setImage(base64_encode($data))
                ->setMimeType('image/gif');

            $this->getDoctrine()->getManager()->persist($imageBlock);
            $this->getDoctrine()->getManager()->flush();
        }

        $guesser = new MimeTypeExtensionGuesser();
        $imageRoute = $this->getRouter()->generate('pluetzner_block_image_show', [
            'slug' => $imageBlock->getSlug(),
            'height' => $height,
            'width' => $width,
            '_type' => $guesser->guess($imageBlock->getMimeType())

        ]);

        $imagePath = sprintf('%s/../web%s', $this->getRootdir(), $imageRoute);
        if (false === file_exists($imagePath)) {
            $this->resizeImage(imagecreatefromstring(base64_decode($imageBlock->getImage())), $imageBlock->getMimeType(), $width, $height, $imagePath);
        }

        $editData = '"';
        if ($this->getTwig()->isGranted('ROLE_ADMIN')) {
            $route = $this->getRouter()->generate('pluetzner_block_imageblock_editajax', ['id' => $imageBlock->getId()]);
            $editData = sprintf(' imageblock" data-href="%s"', $route);
        }

        return sprintf('<img class="%s src="%s">', $editData, $imageRoute);
    }

    /**
     * Resize an image and copy it
     *
     * @param resource $src_img
     * @param string $mimeType
     * @param int $new_width
     * @param int $new_height
     * @param string $moveTo
     */
    private function resizeImage($src_img, $mimeType, $new_width, $new_height, $moveTo)
    {
        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if (0 === $new_height) {
            $new_height = $old_y;
        }
        if (0 === $new_width) {
            $new_width = $old_x;
        }

        if ($old_x > $old_y) {
            $thumb_w = $new_width;
            $thumb_h = $old_y / $old_x * $new_width;
        } elseif ($old_x < $old_y) {
            $thumb_w = $old_x / $old_y * $new_height;
            $thumb_h = $new_height;
        } else {
            $thumb_w = $new_width;
            $thumb_h = $new_height;
        }

        $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);

        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

        if (!file_exists(dirname($moveTo))) {
            mkdir(dirname($moveTo));
        }
        if ($mimeType == 'image/png') {
            $result = imagepng($dst_img, $moveTo, 8);
        } elseif ($mimeType == 'image/jpg' || $mimeType == 'image/jpeg' || $mimeType == 'image/pjpeg') {
            $result = imagejpeg($dst_img, $moveTo, 80);
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);
    }
}