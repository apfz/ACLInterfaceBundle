<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ifgm\ACLInterfaceBundle\Security\Acl\Permission;

/**
 * MaskBuilder methods required by ACLInterfaceBundle
 */
interface MaskBuilderInterface
{
    /**
     * Adds a mask to the permission
     *
     * @param mixed $mask
     *
     * @return MaskBuilder
     *
     * @throws \InvalidArgumentException
     */
    public function add($mask);

    /**
     * Returns the mask of this permission
     *
     * @return integer
     */
    public function get();

    /**
     * Returns a human-readable representation of the permission
     *
     * @return string
     */
    public function getPattern();


    /**
     * Removes a mask from the permission
     *
     * @param mixed $mask
     *
     * @return MaskBuilder
     *
     * @throws \InvalidArgumentException
     */
    public function remove($mask);


    /**
     * Resets the PermissionBuilder
     *
     * @return MaskBuilder
     */
    public function reset();
    /**
     * Returns the code for the passed mask
     *
     * @param integer $mask
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return string
     */
    public static function getCode($mask);

    /**
     * Returns the code for the passed mask
     *
     * @param integer $mask
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return string
     */
    public static function getMaskLabel($mask);

    /**
     * Returns all permissions covered by the mask
     *
     * @return string
     */
    public function getPermissions();

    /**
     * Returns a human-readable representation of the permission
     *
     * @return string
     */
    public function getMasks();
}
