<?php

namespace Ifgm\ACLInterfaceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Ifgm\ACLInterfaceBundle\Model\AclInterface;

/**
 * AclRepository
 *
 * @author Jérémy Hubert <jeremy.hubert@ifgm.fr>
 */
class AclRepository extends EntityRepository
{
    /**
     * Find an ACL by user and object
     *
     * @param $user
     * @param $object
     *
     * @return AclInterface|null
     */
    public function findByUserAndObject($user, $object)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->where($qb->expr()->eq('a.userId', ':userId'))
            ->andWhere($qb->expr()->eq('a.objectType', ':objectType'))
            ->andWhere($qb->expr()->eq('a.objectId', ':objectId'))
            ->setParameters(array(
                'userId' => $user->getId(),
                'objectType' => get_class($object),
                'objectId' => $object->getId()
            ))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Revoke all roles of an user to an object
     *
     * @param $user
     * @param $object
     */
    public function revokeForUserAndObject($user, $object)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->delete()
            ->where($qb->expr()->eq('a.userId', ':userId'))
            ->andWhere($qb->expr()->eq('a.objectType', ':objectType'))
            ->andWhere($qb->expr()->eq('a.objectId', ':objectId'))
            ->setParameters(array(
                'userId' => $user->getId(),
                'objectType' => get_class($object),
                'objectId' => $object->getId()
            ))
            ->getQuery()
            ->execute();
    }

    /**
     * Revoke all roles of an user to all object (user deletion)
     *
     * @param $user
     */
    public function revokeAllFromUser($user)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->delete()
            ->where($qb->expr()->eq('a.userId', ':userId'))
            ->setParameters(array(
                'userId' => $user->getId(),
            ))
            ->getQuery()
            ->execute();
    }


    /**
     * Revoke all roles on an object
     *
     * @param $object
     */
    public function revokeAllOnObject($object)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->delete()
            ->where($qb->expr()->eq('a.objectType', ':objectType'))
            ->andWhere($qb->expr()->eq('a.objectId', ':objectId'))
            ->setParameters(array(
                'objectType' => get_class($object),
                'objectId' => $object->getId()
            ))
            ->getQuery()
            ->execute();
    }

    /**
     * Find ACLs by users and object
     *
     * @param array<User> $user
     * @param $object
     *
     * @return Collection
     */
    public function getUsersRolesOnObject(array $users, $object)
    {
        $userList = array();
        foreach ($users as $user) {
            $userList[] = $user->getId();
        }

        $qb = $this->createQueryBuilder('a');

        return $qb->where($qb->expr()->in('a.userId', ':userIds'))
            ->andWhere($qb->expr()->eq('a.objectType', ':objectType'))
            ->andWhere($qb->expr()->eq('a.objectId', ':objectId'))
            ->setParameters(array(
                'userIds' => $userList,
                'objectType' => get_class($object),
                'objectId' => $object->getId()
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * Find ACLs by all users on an object
     *
     * @param array<User> $user
     * @param $object
     *
     * @return Collection
     */
    public function getAllRolesOnObject($object)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->where($qb->expr()->eq('a.objectType', ':objectType'))
            ->andWhere($qb->expr()->eq('a.objectId', ':objectId'))
            ->setParameters(array(
                'objectType' => get_class($object),
                'objectId' => $object->getId()
            ))
            ->getQuery()
            ->getResult();
    }
}