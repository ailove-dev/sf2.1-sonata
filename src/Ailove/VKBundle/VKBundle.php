<?php

namespace Ailove\VKBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Ailove\VKBundle\DependencyInjection\Security\Factory\VKFactory;

/**
 * VK Bundle
 */
class VKBundle extends Bundle
{
    /**
     * Add security listener factory
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new VKFactory());
    }
}
