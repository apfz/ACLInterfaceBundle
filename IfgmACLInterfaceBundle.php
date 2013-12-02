<?php

namespace Ifgm\ACLInterfaceBundle;

use Ifgm\ACLInterfaceBundle\DependencyInjection\Compiler\AddConfigManagerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IfgmACLInterfaceBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddConfigManagerPass);
    }
}
