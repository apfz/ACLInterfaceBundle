<?php

namespace Ifgm\ACLInterfaceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the configManagers.
 *
 * @author Jérémy Hubert <jeremy.hubert@infogroom.fr>
 */
class AddConfigManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ifgm_acl_interface.config_manager_chain')) {
            return;
        }

        $configManagers = array();
        foreach ($container->findTaggedServiceIds('ifgm_acl_interface.config_manager') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $configManagers[$priority][] = new Reference($id);
        }

        if (empty($configManagers)) {

            return;
        }

        // sort by priority and flatten
        krsort($configManagers);
        $configManagers = call_user_func_array('array_merge', $configManagers);

        $container->getDefinition('ifgm_acl_interface.config_manager_chain')->addArgument($configManagers);
    }
}
