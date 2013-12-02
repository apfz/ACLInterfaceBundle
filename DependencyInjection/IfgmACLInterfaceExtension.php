<?php

/*
 * This file is part of the ACLInterfaceBundle package.
 *
 * (c) Jérémy Hubert <jeremy.hubert@infogroom.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ifgm\ACLInterfaceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IfgmACLInterfaceExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('ifgm_acl_interface.form_manager.class', $config['form_manager']['class']);
        $container->setParameter('ifgm_acl_interface.mask_builder.class', $config['mask_builder']['class']);
        $container->setParameter('ifgm_acl_interface.permission_map.class', $config['permission_map']['class']);
        $container->setParameter('ifgm_acl_interface.acl_voter.class', $config['acl_voter']['class']);
        $container->setParameter('ifgm_acl_interface.user.class', $config['user']['class']);
    }
}
