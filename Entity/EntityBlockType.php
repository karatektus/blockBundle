<?php

namespace Pluetzner\BlockBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Pluetzner\BlockBundle\Model\EntityBlockTypeModel;

/**
 * EntityBlockType
 *
 * @ORM\Table(name="entity_block_type")
 * @ORM\Entity(repositoryClass="Pluetzner\BlockBundle\Repository\EntityBlockTypeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EntityBlockType extends EntityBlockTypeModel
{
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
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var array
     *
     * @ORM\Column(name="image_blocks", type="array")
     */
    private $imageBlocks;

    /**
     * @var array
     *
     * @ORM\Column(name="text_blocks", type="array")
     */
    private $textBlocks;

    /**
     * @var array
     *
     * @ORM\Column(name="string_blocks", type="array")
     */
    private $stringBlocks;


    /**
     * @var ArrayCollection|EntityBlock[]
     *
     * @ORM\OneToMany(targetEntity="Pluetzner\BlockBundle\Entity\EntityBlock", mappedBy="entityBlockType")
     */
    private $entityBlocks;


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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return array
     */
    public function getImageBlocks()
    {
        if (null === $this->imageBlocks) {
            return [];
        }
        return $this->imageBlocks;
    }

    /**
     * @param array $imageBlocks
     */
    public function setImageBlocks($imageBlocks)
    {
        $this->imageBlocks = $imageBlocks;
    }

    /**
     * @return array
     */
    public function getTextBlocks()
    {
        if (null === $this->textBlocks) {
            return [];
        }
        return $this->textBlocks;
    }

    /**
     * @param array $textBlocks
     */
    public function setTextBlocks($textBlocks)
    {
        $this->textBlocks = $textBlocks;
    }

    /**
     * @return array
     */
    public function getStringBlocks()
    {
        if (null === $this->stringBlocks) {
            return [];
        }
        return $this->stringBlocks;
    }

    /**
     * @param array $stringBlocks
     * @return EntityBlockType
     */
    public function setStringBlocks($stringBlocks)
    {
        $this->stringBlocks = $stringBlocks;
        return $this;
    }


    /**
     * @return ArrayCollection|EntityBlock[]
     */
    public function getEntityBlocks()
    {
        return $this->entityBlocks;
    }

    /**
     * @param ArrayCollection|EntityBlock[] $entityBlocks
     */
    public function setEntityBlocks($entityBlocks)
    {
        $this->entityBlocks = $entityBlocks;
    }
}

