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

interface ConfigManagerInterface {
    /**
     * Get the ACLs to manage for this entity
     *
     * @param string $entity
     *
     * @return array
     */
    public function getConfig($entity);
}