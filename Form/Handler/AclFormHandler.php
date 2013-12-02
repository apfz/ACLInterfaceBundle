<?php
/**
 * Created by JetBrains PhpStorm.
 * User: infogroom
 * Date: 25/10/13
 * Time: 00:20
 * To change this template use File | Settings | File Templates.
 */

namespace Ifgm\ACLInterfaceBundle\Form\Handler;


use Doctrine\Common\Persistence\ObjectManager;
use Ifgm\ACLInterfaceBundle\Form\Type\AclFormType;
use Ifgm\ACLInterfaceBundle\Manager\AclManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AclFormHandler {

    protected $form;

    protected $aclManager;

    protected $entity;

    /**
     * Initialize the form handler
     *
     * @param AclManager    $aclManager    The Acl Manager
     * @param ObjectManager $objectManager The object manager
     * @param Form          $form          The form
     * @param mixed         $entity        The target entity to be managed
     * @param array         $users         Users to be granted/revoked accesses to the target entity
     */
    public function __construct(AclManager $aclManager, ObjectManager $objectManager, Form $form, $entity, array $users)
    {
        $this->aclManager = $aclManager;
        $this->objectManager = $objectManager;
        $this->form = $form;
        $this->entity = $entity;
        $this->users = $users;
    }

    /**
     * Process form
     *
     * @param Request $request The request
     * @param bool    $flush   Should we flush updates ?
     *
     * @return bool if the form is valid or no
     */
    public function process(Request $request, $flush = true)
    {
        if ($request->getMethod() == 'POST' && $request->request->has($this->form->getName())) {

            $this->form->handleRequest($request);

            if ($this->form->isValid()) {
                $this->doOnSuccess($flush);
            }
        }

        return false;
    }

    /**
     * Update data, and flush
     *
     * @param bool $flush Should we flush updates ?
     *
     * @return bool
     */
    public function doOnSuccess($flush = true)
    {
        // For each user, we set updated Acls on this entity
        foreach($this->form['users'] as $userId => $userForm) {
            $bitmask = array_sum($userForm->get('acls')->getData());

            if ($bitmask) {
                $this->aclManager->setBitmask(
                    $bitmask,
                    $this->users[$userId],
                    $this->entity
                );
            } else {
                $this->aclManager->revokeAll($this->users[$userId], $this->entity);
            }
        }

        if ($flush) {
            $this->objectManager->flush();
        }

        return true;
    }
}