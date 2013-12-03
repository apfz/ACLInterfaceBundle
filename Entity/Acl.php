<?php

namespace Ifgm\ACLInterfaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ifgm\ACLInterfaceBundle\Model\AclInterface;

/**
 * Acl entity
 *
 * @author Jérémy Hubert <jeremy.hubert@infogroom.fr>
 *
 * @ORM\Entity(repositoryClass="Ifgm\ACLInterfaceBundle\Repository\AclRepository")
 * @ORM\Table(name="ifgm_acl")
 * @ORM\MappedSuperclass
 */
class Acl implements AclInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=45, nullable=false)
     */
    protected $userId;


    /**
     * @var string
     *
     * @ORM\Column(name="object_type", type="string", length=64, nullable=false)
     */
    protected $objectType;

    /**
     * @var string
     *
     * @ORM\Column(name="object_id", type="string", length=45, nullable=false)
     */
    protected $objectId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=11, nullable=false)
     */
    protected $bitmask;

    /**
     * @param int $bitmask
     */
    public function setBitmask($bitmask)
    {
        $this->bitmask = $bitmask;
    }

    /**
     * @return int
     */
    public function getBitmask()
    {
        return $this->bitmask;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $objectId
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return mixed
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param string $objectType
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }
}