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

Trait MaskBuilderTrait
{
    /**
     * Returns the code for the passed mask
     *
     * @param integer $mask
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return string
     */
    public static function getMaskLabel($mask)
    {
        if (!is_int($mask)) {
            throw new \InvalidArgumentException('$mask must be an integer.');
        }
        $reflection = new \ReflectionClass(get_called_class());

        foreach ($reflection->getConstants() as $name => $cMask) {
            if (0 !== strpos($name, 'MASK_')) {
                continue;
            }

            if ($mask === $cMask) {
                if (!defined('static::CODE_'.substr($name, 5))) {
                    throw new \RuntimeException('There was no code defined for this mask.');
                }

                return substr($name, 5);
            }
        }

        throw new \InvalidArgumentException(sprintf('The mask "%d" is not supported.', $mask));
    }

    /**
     * Returns all permissions covered by the mask
     *
     * @return string
     */
    public function getPermissions()
    {
        $pattern = self::ALL_OFF;
        $length = strlen($pattern);
        $bitmask = str_pad(decbin($this->mask), $length, '0', STR_PAD_LEFT);

        $permissions = array();
        for ($i=$length-1; $i>=0; $i--) {
            if ('1' === $bitmask[$i]) {
                $permissions[] = 1 << ($length - $i - 1);
            }
        }

        return $permissions;
    }

    /**
     * Returns a human-readable representation of the permission
     *
     * @return string
     */
    public function getMasks()
    {
        $pattern = self::ALL_OFF;
        $length = strlen($pattern);
        $bitmask = str_pad(decbin($this->mask), $length, '0', STR_PAD_LEFT);

        $roles = array();
        for ($i=$length-1; $i>=0; $i--) {
            if ('1' === $bitmask[$i]) {
                $roles[1 << ($length - $i - 1)] = self::getMaskLabel(1 << ($length - $i - 1));
            }
        }

        return $roles;
    }
}