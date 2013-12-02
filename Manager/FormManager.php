<?php
/**
 * Created by JetBrains PhpStorm.
 * User: infogroom
 * Date: 23/09/13
 * Time: 07:52
 * To change this template use File | Settings | File Templates.
 */

namespace Ifgm\ACLInterfaceBundle\Manager;

use Doctrine\Common\Collections\Collection;
use Ifgm\ACLInterfaceBundle\Form\Handler\AclFormHandler;
use Ifgm\ACLInterfaceBundle\Form\Type\AclFormType;

class FormManager {

    /**
     * @var AclFormHandler
     */
    protected $formHandler;

    /**
     * Creates the acl form
     * @param string $entity
     * @param array|Collection $users
     */
    public function getForm($entity, $users)
    {
        $this->entity = $entity;
        $this->users = $users;

        return new AclFormType($entity, $users);
    }

    /**
     * Process the form and persist new ACLs
     * @param $form
     */
    public function processForm($form, $request, $entity, $users)
    {
        $formHandler = new AclFormHandler($this->aclManager, $this->form, $entity, $users);
        $this->formHandler->process($request);
    }
}