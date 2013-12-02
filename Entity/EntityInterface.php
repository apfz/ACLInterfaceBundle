<?php

namespace Ifgm\ACLInterfaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ifgm\ACLInterfaceBundle\Model\AclInterface;

/**
 * Entity interface
 *
 * @author Jérémy Hubert <jeremy.hubert@infogroom.fr>
 */
interface EntityInterface
{
    /**
     * Get Id
     *
     * @return mixed
     */
    public function getId();
}