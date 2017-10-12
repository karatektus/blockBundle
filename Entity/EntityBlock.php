<?php

namespace Pluetzner\BlockBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Pluetzner\BlockBundle\Model\EntityBlockModel;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pluetzner\BlockBundle\Framework\Traits\EditableEntityTrait;

/**
 * EntityBlock
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
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;

    /**
     * @var EntityBlockType
     *
     * @ORM\ManyToOne(targetEntity="Pluetzner\BlockBundle\Entity\EntityBlockType", inversedBy="entityBlocks")
     */
    private $entityBlockType;


    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

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
     * @var integer
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

    /**
     * @var array
     *
     * @ORM\Column(name="visible_languages", type="simple_array")
     */
    private $visibleLanguages;

    /**
     * @var ArrayCollection|StringBlock[]
     *
     * @ORM\OneToMany(targetEntity="Pluetzner\BlockBundle\Entity\StringBlock", mappedBy="entityBlock")
     */
    private $stringBlocks;

        /**
     * @var ArrayCollection|OptionBlock[]
     *
     * @ORM\OneToMany(targetEntity="Pluetzner\BlockBundle\Entity\OptionBlock", mappedBy="entityBlock")
     */
    private $optionBlock;

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

        if (null === $this->getCount()){
            $this->setCount(0);
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
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     * @return EntityBlock
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
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
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     * @return EntityBlock
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
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
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return EntityBlock
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
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
     * @return ArrayCollection|OptionBlock[]
     */
    public function getOptionBlock()
    {
        return $this->optionBlock;
    }

    /**
     * @param ArrayCollection|OptionBlock[] $optionBlock
     * @return EntityBlock
     */
    public function setOptionBlock($optionBlock)
    {
        $this->optionBlock = $optionBlock;
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
     * @return array
     */
    public function getVisibleLanguages()
    {
        return $this->visibleLanguages;
    }

    /**
     * @param array $visibleLanguages
     * @return EntityBlock
     */
    public function setVisibleLanguages($visibleLanguages)
    {
        $this->visibleLanguages = $visibleLanguages;
        return $this;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setOptionalFields()
    {
    }
}

