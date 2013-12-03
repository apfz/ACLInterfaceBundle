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
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Ifgm\ACLInterfaceBundle\Entity\UserInterface;
use Ifgm\ACLInterfaceBundle\Entity\EntityInterface;

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
     * @param Request            $request
     * @param ObjectManager      $objectManager
     * @param EngineInterface    $templating
     * @param FormFactory        $formFactory
     * @param ConfigManagerChain $configManagerChain
     * @param ObjectManager      $objectManager
     * @param string             $aclEntity
     * @param string             $maskBuilder
     */
    public function __construct(Request $request, EngineInterface $templating, FormFactory $formFactory, ConfigManagerChain $configManagerChain, ObjectManager $objectManager, $aclEntity, $userEntity, $maskBuilder)
    {
        $this->request = $request;
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->configManagerChain = $configManagerChain;
        $this->objectManager = $objectManager;
        $this->aclEntity = $aclEntity;
        $this->userClass = $userEntity;
        $this->maskBuilder = $maskBuilder;
    }

    /**
     * Add role to a user on an object
     *
     * @param string          $role
     * @param UserInterface   $user
     * @param EntityInterface $object
     */
    public function addRole($role, UserInterface $user, EntityInterface $object)
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
     * @param string          $role
     * @param UserInterface   $user
     * @param EntityInterface $object
     */
    public function revokeRole($role, UserInterface $user, EntityInterface $object)
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
     * @param array<string>   $roles
     * @param UserInterface   $user
     * @param EntityInterface $object
     */
    public function setRoles(array $roles, UserInterface $user, EntityInterface $object)
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
     * @param int             $bitmask
     * @param UserInterface   $user
     * @param EntityInterface $object
     */
    public function setBitmask($bitmask, UserInterface $user, EntityInterface $object)
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
     * @param UserInterface   $user
     * @param EntityInterface $object
     */
    public function revokeAll(UserInterface $user, EntityInterface $object)
    {
        $this->objectManager->getRepository($this->aclEntity)
            ->revokeForUserAndObject($user, $object);
    }

    /**
     * Remove all roles from an user
     *
     * @param UserInterface $user
     */
    public function revokeAllRolesFromUser(UserInterface $user)
    {
        $this->objectManager->getRepository($this->aclEntity)
            ->revokeAllFromUser($user);
    }

    /**
     * Remove all roles on an object
     *
     * @param EntityInterface $object
     */
    public function revokeAllRolesOnObject(EntityInterface $object)
    {
        $this->objectManager->getRepository($this->aclEntity)
            ->revokeAllOnObject($object);
    }

    /**
     * Get all roles on an object for an array of user
     *
     * @param array<UserInterface> $users
     * @param EntityInterface      $object the target entity
     *
     * @return array
     */
    public function getUsersRoles(array $users, EntityInterface $object)
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
     * @param EntityInterface $object
     *
     * @return array
     */
    public function getAllRolesOnObject(EntityInterface $object)
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
     * @param array           $users
     * @param EntityInterface $object
     * @param bool            $andFlush Should we flush after updates ?
     *
     * @return AclFormType|Form|FormInterface
     */
    public function manageForm(array $users, EntityInterface $object, $andFlush = true)
    {
        $acls = $this->configManagerChain->getFormConfig($object);
        $form = new AclFormType(null, null, array(
            'action' => $this->generateUrl(
                $this->get('request')->attributes->get('_route'),
                $this->get('request')->attributes->get('_route_params')
            ),
            'method' => 'POST',
        ));

        $form = $this->formFactory->create($form, compact('users'), array('acls' => $acls, 'userClass' => $this->userClass));

        foreach ($this->getUsersRoles($users, $object) as $userId => $roles) {
            $form->get('users')[$userId]->get('acls')->setData(array_keys($roles));
        }

        $aclFormHandler = new AclFormHandler($this, $this->objectManager, $form, $object, $users);
        $aclFormHandler->process($this->request, $andFlush);

        return $form;
    }

    /**
     * Render the form as a Response
     *
     * @param Form $form
     *
     * @return Response
     */
    public function renderForm(Form $form)
    {
        return new Response($this->templating->render(
            'IfgmACLInterfaceBundle:Default:index.html.twig',
            array('form' => $form->createView())
        ));
    }

    /**
     * Get a new instance of MaskBuilder
     *
     * @param int $bitmask
     *
     * @return MaskBuilderInterface
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