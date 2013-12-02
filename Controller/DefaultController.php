<?php

/*
 * This file is part of the ACLInterfaceBundle package.
 *
 * (c) Jérémy Hubert <jeremy.hubert@infogroom.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ifgm\ACLInterfaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $users = $this->get('doctrine.orm.entity_manager')->getRepository(get_class($this->getUser()))->findAllIndexedById();

        $userList = array();
        foreach ($users as $user) {
            $userList[$user->getId()] = $user;
        }
        $address = $this->get('doctrine.orm.entity_manager')->getRepository('IfgmFrontBundle:Address')->find(1);

        $form = $this->get('ifgm.acl_manager')->manageForm($userList, $address);

        // var_dump($this->get('security.context')->isGranted('EDIT', $address));

        return $this->get('ifgm.acl_manager')->renderForm($form);
    }
}
