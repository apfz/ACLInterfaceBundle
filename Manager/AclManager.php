<?php

/*
 * This file is part of the ACLInterfaceBundle package.
 *
 * (c) Jérémy Hubert <jeremy.hubert@infogroom.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ifgm\ACLInterfaceBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Ifgm\ACLInterfaceBundle\Form\Handler\AclFormHandler;
use Ifgm\ACLInterfaceBundle\Form\Type\AclFormType;
use Ifgm\ACLInterfaceBundle\Security\Acl\Permission\MaskBuilderInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AclManager
 *
 * @author Jérémy Hubert <jeremy.hubert@infogroom.fr>
 */
class AclManager
{
    /**
     * @var ObjectManager $objectManager
     */
    protected $objectManager;

    /**
     * @var string $aclEntity Acl namespace
     */
    protected $aclEntity;

    /**
     * @var string $maskBuilder maskbuilder namespace
     */
    protected $maskBuilder;

    /**
     * Construct the AclManager
     *
     * @param Container $container
     * @param ObjectManager $objectManager
     * @param string        $aclEntity
     * @param string        $maskBuilder
     */
    public function __construct(Container $container, ObjectManager $objectManager, $aclEntity, $maskBuilder)
    {
        $this->container = $container;
        $this->objectManager = $objectManager;
        $this->aclEntity = $aclEntity;
        $this->maskBuilder = $maskBuilder;
    }

    /**
     * Add role to a user on an object
     *
     * @param $role
     * @param $user
     * @param $object
     */
    public function addRole($role, $user, $object)
    {
        $acls = $this->objectManager->getRepository($this->aclEntity)
            ->findByUserAndObject($user, $object);

        if (!$acls) {
            $acls = new $this->aclEntity();
            $acls->setUserId($user->getId());
            $acls->setObjectType(get_class($object));
            $acls->setObjectId($object->getId());
            $acls->setBitmask(0);
        }

        $maskBuilder = $this->createMaskBuilder($acls->getBitmask());
        $maskBuilder->add($role);

        $acls->setBitmask($maskBuilder->get());

        $this->objectManager->persist($acls);
    }

    /**
     * Remove role from a user on an object
     *
     * @param $role
     * @param $user
     * @param $object
     */
    public function revokeRole($role, $user, $object)
    {
        $acls = $this->objectManager->getRepository($this->aclEntity)
            ->findByUserAndObject($user, $object);

        if ($acls) {
            $maskBuilder = $this->createMaskBuilder($acls->getBitmask());

            $maskBuilder->remove($role);

            $acls->setBitmask($maskBuilder->get());

            $this->objectManager->persist($acls);
        }
    }

    /**
     * Set roles to a user on an object
     *
     * @param array $roles
     * @param $user
     * @param $object
     */
    public function setRoles(array $roles, $user, $object)
    {
        $acls = $this->objectManager->getRepository($this->aclEntity)
            ->findByUserAndObject($user, $object);

        if (!$acls) {
            $acls = new $this->aclEntity();
            $acls->setUserId($user->getId());
            $acls->setObjectType(get_class($object));
            $acls->setObjectId($object->getId());
        }

        $maskBuilder = $this->createMaskBuilder(0);
        foreach ($roles as $role) {
            $maskBuilder->add($role);
        }

        $acls->setBitmask($maskBuilder->get());

        $this->objectManager->persist($acls);
    }

    /**
     * Set roles (via bitmask) to a user on an object
     *
     * @param int $bitmask
     * @param $user
     * @param $object
     */
    public function setBitmask($bitmask, $user, $object)
    {
        $acls = $this->objectManager->getRepository($this->aclEntity)
            ->findByUserAndObject($user, $object);

        if (!$acls) {
            $acls = new $this->aclEntity();
            $acls->setUserId($user->getId());
            $acls->setObjectType(get_class($object));
            $acls->setObjectId($object->getId());
        }

        $acls->setBitmask($bitmask);

        $this->objectManager->persist($acls);
    }

    /**
     * Remove all roles from an user on an object
     *
     * @param $user
     * @param $object
     */
    public function revokeAll($user, $object)
    {
        $this->objectManager->getRepository($this->aclEntity)
            ->revokeForUserAndObject($user, $object);
    }

    /**
     * Remove all roles from an user
     *
     * @param $user
     */
    public function revokeAllRolesFromUser($user)
    {
        $this->objectManager->getRepository($this->aclEntity)
            ->revokeAllFromUser($user);
    }

    /**
     * Remove all roles on an object
     *
     * @param $object
     */
    public function revokeAllRolesOnObject($object)
    {
        $this->objectManager->getRepository($this->aclEntity)
            ->revokeAllOnObject($object);
    }

    /**
     * Get all roles on an object for an array of user
     *
     * @param array $users
     * @param mixed $object the target entity
     *
     * @return array
     */
    public function getUsersRoles(array $users, $object)
    {
        $acls = $this->objectManager->getRepository($this->aclEntity)
            ->getUsersRolesOnObject($users, $object);

        $roles = array();
        foreach ($acls as $acl)
        {
            $maskBuilder = $this->createMaskBuilder($acl->getBitmask());
            $roles[$acl->getUserId()] = $maskBuilder->getMasks();
        }

        return $roles;
    }

    /**
     * Return list of users and theirs roles affected to an object
     *
     * @param $object
     *
     * @return array
     */
    public function getAllRolesOnObject($object)
    {
        $acls = $this->objectManager->getRepository($this->aclEntity)
            ->getAllRolesOnObject($object);

        $roles = array();
        foreach ($acls as $acl)
        {
            $maskBuilder = $this->createMaskBuilder($acl->getBitmask());
            $roles[$acl->getUserId()] = $maskBuilder->getMasks();
        }

        return $roles;
    }

    /**
     * Manage form (valid, persist, and optionally flush)
     *
     * @param array $users
     * @param mixed $object
     * @param bool $andFlush Should we flush after updates ?
     * @return AclFormType|\Symfony\Component\Form\Form|\Symfony\Component\Form\FormInterface
     */
    public function manageForm($users, $object, $andFlush = true)
    {
        $acls = $this->container->get('ifgm_acl_interface.config_manager_chain')->getFormConfig($object);
        $form = new AclFormType(null, null, array(
            'action' => $this->generateUrl(
                $this->get('request')->attributes->get('_route'),
                $this->get('request')->attributes->get('_route_params')
            ),
            'method' => 'POST',
        ));

        $form = $this->container->get('form.factory')->create($form, compact('users'), compact('acls'));

        foreach ($this->getUsersRoles($users, $object) as $userId => $roles) {
            $form->get('users')[$userId]->get('acls')->setData(array_keys($roles));
        }

        $aclFormHandler = new AclFormHandler($this, $this->objectManager, $form, $object, $users);
        $aclFormHandler->process($this->container->get('Request'), $andFlush);

        return $form;
    }

    /**
     * Render the form as a Response
     *
     * @param $form
     *
     * @return Response
     */
    public function renderForm($form)
    {
        return new Response($this->container->get('templating')->render(
            'IfgmACLInterfaceBundle:Default:index.html.twig',
            array('form' => $form->createView())
        ));
    }

    /**
     * Get a new instance of MaskBuilder
     *
     * @param $bitmask
     *
     * @return mixed
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     */
    protected function createMaskBuilder($bitmask)
    {
        $maskBuilder = new $this->maskBuilder($bitmask);

        if (!$maskBuilder instanceof MaskBuilderInterface) {

            throw new InvalidTypeException(sprintf(
                'MaskBuilder %s does not implements %s',
                get_class($maskBuilder),
                'Ifgm\ACLInterfaceBundle\Security\Acl\Permission\MaskBuilderInterface'
            ));
        }

        return $maskBuilder;
    }
}