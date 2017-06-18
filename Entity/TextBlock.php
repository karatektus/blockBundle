<?php

namespace Pluetzner\BlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pluetzner\BlockBundle\Framework\Traits\EditableEntityTrait;

/**
 * TextBlock
 *
 * @ORM\Table(name="text_block")
 * @ORM\Entity(repositoryClass="Pluetzner\BlockBundle\Repository\TextBlockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TextBlock
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
     * @Gedmo\Translatable()
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * @var EntityBlock
     *
     * @ORM\ManyToOne(targetEntity="Pluetzner\BlockBundle\Entity\EntityBlock", inversedBy="textBlocks")
     */
    private $entityBlock;

    /**
     * WTF? just for the form type - ignore it!
     */
    public $Speichern;

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
     * @return TextBlock
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
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

