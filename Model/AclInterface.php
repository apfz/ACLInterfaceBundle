<?php

namespace Ifgm\ACLInterfaceBundle\Model;

interface AclInterface
{
    /**
     * Set Bitmask
     *
     * @param int
     */
    public function setBitmask($bitmask);

    /**
     * Get Bitmask
     *
     * @return int
     */
    public function getBitmask();

    /**
     * Get Id
     *
     * @return int|string $id
     */
    public function getId();

    /**
     * Set ObjectId
     *
     * @param mixed $objectId
     */
    public function setObjectId($objectId);

    /**
     * Get ObjectId
     *
     * @return mixed
     */
    public function getObjectId();

    /**
     * Set ObjectType
     *
     * @param string $objectType
     */
    public function setObjectType($objectType);

    /**
     * Get Object Type
     *
     * @return string
     */
    public function getObjectType();

    /**
     * Set UserId
     *
     * @param mixed $userId
     */
    public function setUserId($userId);

    /**
     * Get UserId
     *
     * @return mixed
     */
    public function getUserId();
}