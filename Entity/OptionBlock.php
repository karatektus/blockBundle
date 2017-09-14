<?php

namespace Pluetzner\BlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pluetzner\BlockBundle\Framework\Traits\EditableEntityTrait;

/**
 * TextBlock
 *
 * @ORM\Table(name="pl_cms_option_block")
 * @ORM\Entity(repositoryClass="Pluetzner\BlockBundle\Repository\OptionBlockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class OptionBlock
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var array
     *
     * @ORM\Column(name="options", type="array")
     */
    private $options;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var EntityBlock
     *
     * @ORM\ManyToOne(targetEntity="Pluetzner\BlockBundle\Entity\EntityBlock", inversedBy="optionBlock")
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
     * @return OptionBlock
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return OptionBlock
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return OptionBlock
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return OptionBlock
     */
    public function setValue($value)
    {
        $this->value = $value;
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

