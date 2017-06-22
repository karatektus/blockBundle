<?php

namespace Pluetzner\BlockBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Pluetzner\BlockBundle\Model\EntityBlockModel;
use Doctrine\ORM\Mapping as ORM;
use Pluetzner\BlockBundle\Framework\Traits\EditableEntityTrait;

/**
 * ImageBlock
 *
 * @ORM\Table(name="pl_cms_entity_block")
 * @ORM\Entity(repositoryClass="Pluetzner\BlockBundle\Repository\EntityBlockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EntityBlock extends EntityBlockModel
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
     * @var EntityBlockType
     *
     * @ORM\ManyToOne(targetEntity="Pluetzner\BlockBundle\Entity\EntityBlockType", inversedBy="entityBlocks")
     */
    private $entityBlockType;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published", type="datetime")
     */
    private $published;

    /**
     * @var ArrayCollection|StringBlock[]
     *
     * @ORM\OneToMany(targetEntity="Pluetzner\BlockBundle\Entity\StringBlock", mappedBy="entityBlock")
     */
    private $stringBlocks;

    /**
     * @var ArrayCollection|TextBlock[]
     *
     * @ORM\OneToMany(targetEntity="Pluetzner\BlockBundle\Entity\TextBlock", mappedBy="entityBlock")
     */
    private $textBlocks;

    /**
     * @var ArrayCollection|ImageBlock[]
     *
     * @ORM\OneToMany(targetEntity="Pluetzner\BlockBundle\Entity\ImageBlock", mappedBy="entityBlock")
     */
    private $imageBlocks;

    public function __construct()
    {
        if (null == $this->getPublished()) {
            $this->setPublished(new \DateTime());
        }
    }

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
     * @return EntityBlockType
     */
    public function getEntityBlockType()
    {
        return $this->entityBlockType;
    }

    /**
     * @param EntityBlockType $entityBlockType
     */
    public function setEntityBlockType($entityBlockType)
    {
        $this->entityBlockType = $entityBlockType;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param \DateTime $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return ArrayCollection|StringBlock[]
     */
    public function getStringBlocks()
    {
        return $this->stringBlocks;
    }

    /**
     * @param ArrayCollection|StringBlock[] $stringBlocks
     * @return EntityBlock
     */
    public function setStringBlocks($stringBlocks)
    {
        $this->stringBlocks = $stringBlocks;
        return $this;
    }

    /**
     * @return ArrayCollection|TextBlock[]
     */
    public function getTextBlocks()
    {
        return $this->textBlocks;
    }

    /**
     * @param ArrayCollection|TextBlock[] $textBlocks
     */
    public function setTextBlocks($textBlocks)
    {
        $this->textBlocks = $textBlocks;
    }

    /**
     * @return ArrayCollection|ImageBlock[]
     */
    public function getImageBlocks()
    {
        return $this->imageBlocks;
    }

    /**
     * @param ArrayCollection|ImageBlock[] $imageBlocks
     */
    public function setImageBlocks($imageBlocks)
    {
        $this->imageBlocks = $imageBlocks;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setOptionalFields()
    {

    }
}

