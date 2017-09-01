<?php

namespace Pluetzner\BlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pluetzner\BlockBundle\Framework\Traits\EditableEntityTrait;
use Pluetzner\BlockBundle\Model\ImportModel;
use Pluetzner\BlockBundle\Model\UserModel;

/**
 * Import
 *
 * @ORM\Table(name="pl_cms_export")
 * @ORM\Entity(repositoryClass="Pluetzner\BlockBundle\Repository\ExportRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Export extends ImportModel
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
     * @ORM\Column(name="entity", type="string", length=255)
     */
    private $entity;

    /**
     * @var mixed
     *
     * @ORM\Column(name="data", type="blob")
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="mimetype", type="string", length=255)
     */
    private $mimetype;

    /**
     * @var string
     *
     * @ORM\Column(name="file_ending", type="string", length=255)
     */
    private $fileEnding;

    /**
     * @var int
     *
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

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
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return Export
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if (null === $this->data) {
            return null;
        }

        if (true === is_string($this->data)) {
            return $this->data;
        }

        return stream_get_contents($this->data);
    }

    /**
     * @param mixed $data
     * @return Export
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param string $mimetype
     * @return Export
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileEnding()
    {
        return $this->fileEnding;
    }

    /**
     * @param string $fileEnding
     * @return Export
     */
    public function setFileEnding($fileEnding)
    {
        $this->fileEnding = $fileEnding;
        return $this;
    }

    /**
     * @return UserModel
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserModel $user
     * @return Export
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}

