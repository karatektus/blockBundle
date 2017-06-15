<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 12.06.2017
 * Time: 13:26
 */

namespace Pluetzner\BlockBundle\Framework\Traits;


use DateTime;

trait EditableEntityTrait
{
    /**
     * Created
     *
     * @var DateTime
     *
     * @ORM\Column(name="created",type="datetime")
     */
    private $created;

    /**
     * Last Update
     *
     * @var DateTime
     *
     * @ORM\Column(name="updated",type="datetime")
     */
    private $updated;

    /**
     * Deleted
     *
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     *
     * @return EditableEntityTrait
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     *
     * @return EditableEntityTrait
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     *
     * @return EditableEntityTrait
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Set Editable Fields
     *
     * @ORM\PreFlush()
     */
    public function setEditableOnFlush()
    {
        if (null === $this->isDeleted()) {
            $this->setDeleted(false);
        }

        if (null === $this->getCreated()) {
            $this->setCreated(new DateTime());
        }

        $this->setUpdated(new DateTime());
    }
}