<?php

namespace Ifgm\ACLInterfaceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Ifgm\ACLInterfaceBundle\Entity\EntityInterface;
use Ifgm\ACLInterfaceBundle\Entity\UserInterface;
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
     * @param UserInterface   $user
     * @param EntityInterface $object
     *
     * @return AclInterface|null
     */
    public function findByUserAndObject(UserInterface $user, EntityInterface $object)
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
     * @param UserInterface   $user
     * @param EntityInterface $object
     */
    public function revokeForUserAndObject(UserInterface $user, EntityInterface $object)
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
     * @param UserInterface $user
     */
    public function revokeAllFromUser(UserInterface $user)
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
     * @param EntityInterface $object
     */
    public function revokeAllOnObject(EntityInterface $object)
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
     * @param EntityInterface $object
     *
     * @return Collection
     */
    public function getUsersRolesOnObject(array $users, EntityInterface $object)
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
     * @param EntityInterface $object
     *
     * @return Collection
     */
    public function getAllRolesOnObject(EntityInterface $object)
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