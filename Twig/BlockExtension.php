<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 11.06.2017
 * Time: 16:17
 */

namespace Pluetzner\BlockBundle\Twig;

use Knp\Component\Pager\Paginator;
use Pluetzner\BlockBundle\Entity\EntityBlock;
use Pluetzner\BlockBundle\Entity\EntityBlockType;
use Pluetzner\BlockBundle\Entity\ImageBlock;
use Pluetzner\BlockBundle\Entity\StringBlock;
use Pluetzner\BlockBundle\Entity\TextBlock;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var Paginator
     */
    private $paginator;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * BlockExtension constructor.
     *
     * @param RegistryInterface $doctrine
     * @param Router            $router
     * @param string            $rootdir
     * @param SecurityExtension $twig
     */
    public function __construct(RegistryInterface $doctrine, Router $router, $rootdir = '', SecurityExtension $twig, Paginator $paginator, RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->rootdir = $rootdir;
        $this->twig = $twig;
        $this->paginator = $paginator;
        $this->request = $requestStack;
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
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return RequestStack
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('imageBlock', [$this, 'getImageBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('stringBlock', [$this, 'getStringBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('textBlock', [$this, 'getTextBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('buttonBlock', [$this, 'getButtonBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('entityBlocks', [$this, 'getEntityBlocks']),
        ];
    }

    /**
     * @param             $slug
     * @param EntityBlock $entityBlock
     * @return string
     */
    public function getTextBlock($slug, $entityBlock = null)
    {
        if (null !== $entityBlock) {
            $oldslug = $slug;
            $slug = sprintf('%s_%s_%s', $entityBlock->getEntityBlockType()->getSlug(), $entityBlock->getId(), $slug);
        }

        $textblock = $this->getDoctrine()->getRepository(TextBlock::class)->findOneBy(['slug' => $slug]);

        if (null === $textblock) {

            if (null !== $entityBlock) {
                $type = $entityBlock->getEntityBlockType();
                $blocks = $type->getTextBlocks();
                $exists = false;
                foreach ($blocks as $block) {
                    if ($oldslug === $block['name']) {
                        $exists = true;
                    }
                }

                if (false === $exists) {
                    $blocks[] = ['name' => $oldslug];
                    $type->setTextBlocks($blocks);
                    $this->getDoctrine()->getManager()->persist($type);
                }
            }

            $textblock = new TextBlock();
            $textblock
                ->setEntityBlock($entityBlock)
                ->setSlug($slug)
                ->setText('No such Textblock');

            $this->getDoctrine()->getManager()->persist($textblock);
            $this->getDoctrine()->getManager()->flush();
        }

        $returnText = '%s';
        if ($this->getTwig()->isGranted('ROLE_ADMIN')) {
            $route = $this->getRouter()->generate('pluetzner_block_textblock_editajax', ['id' => $textblock->getId()]);
            $editData = sprintf('<text class="%s textblock" data-href="%s">', $textblock->getSlug(), $route);
            $returnText = sprintf($returnText, $editData . '%s' . '</text>');
        }

        $parse = new \Parsedown();
        return sprintf($returnText, $parse->text($textblock->getText()));
    }

    /**
     * @param             $slug
     * @param EntityBlock $entityBlock
     * @return string
     */
    public function getStringBlock($slug, $entityBlock = null)
    {
        if (null !== $entityBlock) {
            $oldslug = $slug;
            $slug = sprintf('%s_%s_%s', $entityBlock->getEntityBlockType()->getSlug(), $entityBlock->getId(), $slug);
        }

        $stringBlock = $this->getDoctrine()->getRepository(StringBlock::class)->findOneBy(['slug' => $slug]);

        if (null === $stringBlock) {

            if (null !== $entityBlock) {
                $type = $entityBlock->getEntityBlockType();
                $blocks = $type->getStringBlocks();
                $exists = false;
                foreach ($blocks as $block) {
                    if ($oldslug === $block['name']) {
                        $exists = true;
                    }
                }

                if (false === $exists) {
                    $blocks[] = ['name' => $oldslug];
                    $type->setTextBlocks($blocks);
                    $this->getDoctrine()->getManager()->persist($type);
                }
            }

            $stringBlock = new StringBlock();
            $stringBlock
                ->setEntityBlock($entityBlock)
                ->setSlug($slug)
                ->setText('No such Textblock');

            $this->getDoctrine()->getManager()->persist($stringBlock);
            $this->getDoctrine()->getManager()->flush();
        }


        $editData = '"';
        if ($this->getTwig()->isGranted('ROLE_ADMIN')) {
            $route = $this->getRouter()->generate('pluetzner_block_stringblock_editajax', ['id' => $stringBlock->getId()]);
            $editData = sprintf('%s stringblock" data-href="%s"', $stringBlock->getSlug(), $route);
        }

        return sprintf('<string class="%s>%s</string>', $editData, $stringBlock->getText());
    }

    /**
     * @param string           $slug
     * @param int              $width
     * @param int              $height
     * @param EntityBlock|null $entityBlock
     * @param string           $classes
     *
     * @return string
     */
    public function getImageBlock($slug, $width = 0, $height = 0, $entityBlock = null, $classes = '')
    {
        if (null !== $entityBlock) {
            $oldslug = $slug;
            $slug = sprintf('%s_%s_%s', $entityBlock->getEntityBlockType()->getSlug(), $entityBlock->getId(), $slug);
        }

        $imageBlock = $this->getDoctrine()->getRepository(ImageBlock::class)->findOneBy(['slug' => $slug]);

        if (null === $imageBlock) {

            if (null !== $entityBlock) {
                $type = $entityBlock->getEntityBlockType();
                $blocks = $type->getImageBlocks();
                $exists = false;
                foreach ($blocks as $block) {
                    if ($oldslug === $block['name']) {
                        $exists = true;
                    }
                }

                if (false === $exists) {
                    $blocks[] = ['name' => $oldslug];
                    $type->setImageBlocks($blocks);
                    $this->getDoctrine()->getManager()->persist($type);
                }
            }

            $path = __DIR__ . '/../Resources/img/no_image_thumb.gif';
            $data = file_get_contents($path);

            $imageBlock = new ImageBlock();
            $imageBlock
                ->setEntityBlock($entityBlock)
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
            if ('image/svg+xml' === $imageBlock->getMimeType()) {
                if (!file_exists(dirname($imagePath))) {
                    mkdir(dirname($imagePath), 0755, true);
                }
                file_put_contents($imagePath, base64_decode($imageBlock->getImage()));
            } else {
                $this->resizeImage(imagecreatefromstring(base64_decode($imageBlock->getImage())), $imageBlock->getMimeType(), $width, $height, $imagePath);
            }
        }

        $editData = '"';
        if ($this->getTwig()->isGranted('ROLE_ADMIN')) {
            $route = $this->getRouter()->generate('pluetzner_block_imageblock_editajax', ['id' => $imageBlock->getId()]);
            $editData = sprintf('%s imageblock" data-href="%s"', $imageBlock->getSlug(), $route);
        }

        $w = $h = '';
        if (0 < $width) {
            $w = sprintf(' width=%s', $width);
        }

        if (0 < $height) {
            $h = sprintf(' height=%s', $height);
        }
        return sprintf('<img%s%s class="%s %s src="%s">', $w, $h, $classes, $editData, $imageRoute);
    }

    /**
     * @param string|EntityBlockType $slug
     * @param string                 $icon
     * @param string                 $text
     * @param string                 $color
     *
     * @return string
     */
    public function getButtonBlock($slug, $icon = 'edit', $text = '', $color = 'black')
    {
        if (false === $this->getTwig()->isGranted('ROLE_ADMIN')) {
            return '';
        }
        $classes = 'buttonblock';
        $iconHtml = sprintf('<i class="fa %sfa-%s"></i>', $color == 'white' ? 'icon-white ' : '', $icon);

        $editData = '';
        $type = $this->getDoctrine()->getRepository(EntityBlockType::class)->findOneBy(['slug' => $slug]);

        if (null !== $type) {
            $slug = $type->getSlug();
            $classes = 'addEntityButtonBlock';

            $route = $this->getRouter()->generate('pluetzner_block_entityblock_editajax', ['id' => 0, 'type' => $type->getSlug()]);
            $editData = sprintf('data-href="%s"', $route);
        }

        return sprintf("<a href='javascript:void(0)' %s class='%s' data-slug='%s'>%s%s</a>", $editData, $classes, $slug, $iconHtml, $text);
    }

    /**
     * @param string $type
     * @param int    $limit
     * @param bool   $returnType
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|EntityBlock[]
     */
    public function getEntityBlocks($type, $limit = 0)
    {
        $blockType = $this->getDoctrine()->getRepository(EntityBlockType::class)->findOneBy(['slug' => $type]);

        if (null === $blockType) {
            $blockType = new EntityBlockType();
            $blockType->setSlug($type);

            $this->getDoctrine()->getManager()->persist($blockType);
            $this->getDoctrine()->getManager()->flush();
        }

        if (0 === $limit) {
            return $blockType->getEntityBlocks();
        }

        $pageKey = sprintf('%sPage', $type);
        $page = $this->getRequest()->getCurrentRequest()->get($pageKey);

        if (null === $page) {
            $page = 1;
        }

        $paginator = $this->getPaginator();
        $pagination = $paginator->paginate(
            $blockType->getEntityBlocks(),
            $page,
            $limit,
            [
                'pageParameterName' => $pageKey,
            ]
        );

        return $pagination;
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