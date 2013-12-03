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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('ifgm_acl_interface')
            ->children()
                ->arrayNode('form_manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Ifgm\ACLInterfaceBundle\Manager\FormManager')->end()
                    ->end()
                ->end()
                ->arrayNode('mask_builder')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Ifgm\ACLInterfaceBundle\Security\Acl\Permission\MaskBuilder')->end()
                    ->end()
                ->end()
                ->arrayNode('permission_map')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Symfony\Component\Security\Acl\Permission\BasicPermissionMap')->end()
                    ->end()
                ->end()
                ->arrayNode('acl_voter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Ifgm\ACLInterfaceBundle\Security\Authorization\Voter\AclVoter')->end()
                    ->end()
                ->end()
                ->arrayNode('user')
                    ->children()
                        ->scalarNode('class')->end()
                    ->end()
                ->end()
                ->arrayNode('acl')
                    ->children()
                        ->scalarNode('class')->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
