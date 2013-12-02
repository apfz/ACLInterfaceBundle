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

use Ifgm\ACLInterfaceBundle\Entity\EntityInterface;

interface ConfigManagerInterface {
    /**
     * Get the ACLs to manage for this entity
     *
     * @param EntityInterface $entity
     *
     * @return array
     */
    public function getConfig(EntityInterface $entity);
}