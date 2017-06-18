<?php

namespace Pluetzner\BlockBundle\Entity;

use Pluetzner\BlockBundle\Model\ImageBlockModel;
use Doctrine\ORM\Mapping as ORM;
use Pluetzner\BlockBundle\Framework\Traits\EditableEntityTrait;

/**
 * ImageBlock
 *
 * @ORM\Table(name="image_block")
 * @ORM\Entity(repositoryClass="Pluetzner\BlockBundle\Repository\ImageBlockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ImageBlock extends ImageBlockModel
{
    use EditableEntityTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string")
     */
    private $mimeType;

    /**
     * @var EntityBlock
     *
     * @ORM\ManyToOne(targetEntity="Pluetzner\BlockBundle\Entity\EntityBlock", inversedBy="imageBlocks")
     */
    private $entityBlock;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return ImageBlock
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return ImageBlock
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        if(null === $this->image){
            return null;
        }

        if(true === is_string($this->image)){
            return $this->image;
        }

        return stream_get_contents($this->image);
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return ImageBlock
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return EntityBlock
     */
    public function getEntityBlock()
    {
        return $this->entityBlock;
    }

    /**
     * @param EntityBlock $entityBlock
     *
     * @return $this
     */
    public function setEntityBlock($entityBlock)
    {
        $this->entityBlock = $entityBlock;
        return $this;
    }
}

