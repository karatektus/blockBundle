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
use Pluetzner\BlockBundle\Entity\OptionBlock;
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
            new \Twig_SimpleFunction('optionBlock', [$this, 'getOptionBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('entityBlocks', [$this, 'getEntityBlocks']),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('addCount', [$this, 'addCount'])
        ];
    }


    /**
     * @param             $slug
     * @param EntityBlock $entityBlock
     * @return string
     */
    public function getTextBlock($slug, $entityBlock = null, $length = 0)
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
            $editData = sprintf('<text title="Slug: %s" class="%s textblock" data-href="%s">', $textblock->getSlug(), $textblock->getSlug(), $route);
            $returnText = sprintf($returnText, $editData . '%s' . '</text>');
        }

        $text = $textblock->getText();
        if (0 < $length) {
            $text = substr($text, 0, $length) . '...';
        }

        $parse = new \Parsedown();
        return sprintf($returnText, $parse->text($text));
    }

    /**
     * @param             $slug
     * @param EntityBlock $entityBlock
     * @return string
     */
    public function getStringBlock($slug, $entityBlock = null, $raw = false, $wrap = '%s')
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


        $returnText = $wrap;

        if ($this->getTwig()->isGranted('ROLE_ADMIN') && false === $raw && $wrap === '%s') {
            $route = $this->getRouter()->generate('pluetzner_block_stringblock_editajax', ['id' => $stringBlock->getId()]);
            $editData = sprintf('<string title="Slug: %s" class="%s stringblock" data-href="%s">', $stringBlock->getSlug(), $stringBlock->getSlug(), $route);
            $returnText = sprintf($returnText, $editData . '%s' . '</string>');
        }

        return sprintf($returnText, $stringBlock->getText());
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
    public function getImageBlock($slug, $width = 0, $height = 0, $entityBlock = null, $classes = '', $alt = '', $urlOnly = false)
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

        if (true === $urlOnly) {
            return $imageRoute;
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
        return sprintf('<img%s%s class="%s %s src="%s" alt="%s">', $w, $h, $classes, $editData, $imageRoute, $alt);
    }

    /**
     * @param string|EntityBlockType $slug
     * @param string                 $icon
     * @param string                 $text
     * @param null|string|boolean    $entityBlock
     * @param string                 $wrap
     *
     * @return string
     * @internal param string $color
     */
    public function getButtonBlock($slug, $icon = 'edit', $text = '', $entityBlock = null, $wrap = '%s')
    {
        if (false === $this->getTwig()->isGranted('ROLE_ADMIN')) {
            return '';
        }
        $classes = 'buttonblock';
        $iconHtml = sprintf('<i class="fa fa-%s"></i> ', $icon);

        $editData = '';
        if (null !== $entityBlock) {
            if (true === $entityBlock) {
                $entityBlock = $this->getDoctrine()->getRepository(EntityBlock::class)->findOneBy(['slug' => $slug]);
                if (null !== $entityBlock) {
                    $route = $this->getRouter()->generate('pluetzner_block_entityblock_editajax', ['type' => $slug, 'id' => $entityBlock->getId()]);
                    return sprintf("<a href='javascript:void(0)' data-href='%s' class='%s' data-slug='%s'>%s%s</a>", $route, $classes, $slug, $iconHtml, $text);
                }
                return 'error';
            }
            $slug = sprintf('%s_%s_%s', $entityBlock->getEntityBlockType()->getSlug(), $entityBlock->getId(), $slug);
        } else {
            $type = $this->getDoctrine()->getRepository(EntityBlockType::class)->findOneBy(['slug' => $slug]);

            if (null !== $type) {
                $slug = $type->getSlug();
                $classes = 'addEntityButtonBlock';

                $route = $this->getRouter()->generate('pluetzner_block_entityblock_editajax', ['id' => 0, 'type' => $type->getSlug()]);
                $editData = sprintf('data-href="%s"', $route);

                $returnvalue = sprintf("<a href='javascript:void(0)' %s class='%s' data-slug='%s'>%s%s</a>", $editData, $classes, $slug, $iconHtml, $text);

                return sprintf($wrap, $returnvalue);
            }
        }

        //tripplexorvodoo :o
        $imageblock = $this->getDoctrine()->getRepository(ImageBlock::class)->findOneBy(['slug' => $slug]);
        $stringblock = $this->getDoctrine()->getRepository(StringBlock::class)->findOneBy(['slug' => $slug]);
        $textblock = $this->getDoctrine()->getRepository(TextBlock::class)->findOneBy(['slug' => $slug]);
        $optionblock = $this->getDoctrine()->getRepository(OptionBlock::class)->findOneBy(['slug' => $slug]);

        $a = $imageblock instanceof ImageBlock;
        $b = $stringblock instanceof StringBlock;
        $c = $textblock instanceof TextBlock;
        $d = $optionblock instanceof OptionBlock;

        if (true === $a && false === $b && false === $c && false === $d) {
            $path = 'pluetzner_block_imageblock_editajax';
            $id = $imageblock->getId();
        } elseif (true === $b && false === $a && false === $c && false === $d) {
            $path = 'pluetzner_block_stringblock_editajax';
            $id = $stringblock->getId();
        } elseif (true === $c && false === $b && false === $a && false === $d) {
            $path = 'pluetzner_block_textblock_editajax';
            $id = $textblock->getId();
        } elseif (true === $d && false === $b && false === $c && false === $a) {
            $path = 'pluetzner_block_optionblock_editajax';
            $id = $optionblock->getId();
        } else {
            $returnvalue = sprintf('You used the slug "%s" in more than one blocktype. This breaks the Button', $slug);
            return sprintf($wrap, $returnvalue);
        }
        $route = $this->getRouter()->generate($path, ['id' => $id]);
        $editData = sprintf('data-href="%s"', $route);

        $returnvalue = sprintf("<a href='javascript:void(0)' %s class='%s' data-slug='%s'>%s%s</a>", $editData, $classes, $slug, $iconHtml, $text);
        return sprintf($wrap, $returnvalue);
    }

    /**
     * @param string $type
     * @param int    $limit
     * @param int    $offset
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|EntityBlock[]
     */
    public function getEntityBlocks($type, $limit = 0, $offset = 0, $search = null, $order = EntityBlock::ORDER_ORDERID, $direction = EntityBlock::DIRECTION_DESC)
    {
        $blockType = $this->getDoctrine()->getRepository(EntityBlockType::class)->findOneBy(['slug' => $type]);

        if (null === $blockType) {
            $blockType = new EntityBlockType();
            $blockType->setSlug($type);

            $this->getDoctrine()->getManager()->persist($blockType);
            $this->getDoctrine()->getManager()->flush();
        }

        $dire = 'ASC';
        if ($direction = EntityBlock::DIRECTION_DESC) {
            $dire = 'DESC';
        }
        switch ($order) {
            case EntityBlock::ORDER_COUNT:
                $orderBy = 'count';
                break;
            case EntityBlock::ORDER_CREATED:
                $orderBy = 'created';
                break;
            case EntityBlock::ORDER_EDITED:
                $orderBy = 'edited';
                break;
            case EntityBlock::ORDER_PUBLISHED:
                $orderBy = 'published';
                break;
            default:
                $orderBy = 'orderId';
        }

        $qb = $this->getDoctrine()->getRepository(EntityBlock::class)->createQueryBuilder('b');
        $qb
            ->join('b.entityBlockType', 'type')
            ->where('b.deleted = :showDeleted')
            ->andWhere('type.id = :typeId')
            ->orderBy('b.' . $orderBy, $dire)
            ->setFirstResult($offset)
            ->setParameters([
                'showDeleted' => false,
                'typeId' => $blockType->getId(),
            ]);

        if (null !== $search) {
            $qb
                ->andWhere('b.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if (0 === $limit) {
            return $qb->getQuery()->getResult();
        }

        $pageKey = sprintf('%s%sPage', $type, $limit);
        $page = $this->getRequest()->getCurrentRequest()->get($pageKey);

        if (null === $page) {
            $page = 1;
        }

        $paginator = $this->getPaginator();
        $pagination = $paginator->paginate(
            $qb->getQuery()->getResult(),
            $page,
            $limit,
            [
                'pageParameterName' => $pageKey,
            ]
        );

        return $pagination;
    }

    /**
     * @param string      $slug
     * @param array       $options
     * @param string      $default
     * @param EntityBlock $entityBlock
     * @param bool        $raw
     * @return string
     */
    public function getOptionBlock($slug, $options, $default, $entityBlock = null, $raw = false)
    {
        if (null !== $entityBlock) {
            $oldslug = $slug;
            $slug = sprintf('%s_%s_%s', $entityBlock->getEntityBlockType()->getSlug(), $entityBlock->getId(), $slug);
        }

        $optionBlock = $this->getDoctrine()->getRepository(OptionBlock::class)->findOneBy(['slug' => $slug]);

        if (null === $optionBlock) {
            if (null !== $entityBlock) {
                $type = $entityBlock->getEntityBlockType();
                $blocks = $type->getOptionBlocks();
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

            $optionBlock = new OptionBlock();
            $optionBlock
                ->setEntityBlock($entityBlock)
                ->setSlug($slug)
                ->setOptions($options)
                ->setValue($default);

            $this->getDoctrine()->getManager()->persist($optionBlock);
            $this->getDoctrine()->getManager()->flush();
        } else {
            if ($optionBlock->getOptions() !== $options) {
                $optionBlock->setOptions($options);
                $this->getDoctrine()->getManager()->persist($optionBlock);
                $this->getDoctrine()->getManager()->flush();
            }
        }


        $returnText = '%s';

        if (true === $raw) {
            if ($this->getTwig()->isGranted('ROLE_ADMIN')) {
                $route = $this->getRouter()->generate('pluetzner_block_optionblock_editajax', ['id' => $optionBlock->getId(), 'options' => $options]);
                $editData = sprintf('<string title="Slug: %s" class="%s optionblock" data-href="%s">', $optionBlock->getSlug(), $optionBlock->getSlug(), $route);
                $returnText = sprintf($returnText, $editData . '%s' . '</string>');
                return sprintf($returnText, $optionBlock->getOptions()[$optionBlock->getValue()]);
            }

            return '';
        }

        return $optionBlock->getValue();
    }

    /**
     * @param EntityBlock $entityBlock
     * @param int|string  $count
     *
     * @return EntityBlock
     */
    public function addCount($entityBlock, $count = 0)
    {
        if (0 === $count) {
            $entityBlock->setCount($entityBlock->getCount() + 1);
        } elseif (true === is_int($count)) {
            $entityBlock->setCount($count);
        } else {
            $entityBlock->setCount($entityBlock->getCount() + intval($count));
        }
        $this->getDoctrine()->getManager()->persist($entityBlock);
        $this->getDoctrine()->getManager()->flush();

        return $entityBlock;
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