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

use Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;


/**
 * {@inheritdoc}
 */
class MaskBuilder extends BaseMaskBuilder implements MaskBuilderInterface
{
    use MaskBuilderTrait;

    protected $mask;
}
