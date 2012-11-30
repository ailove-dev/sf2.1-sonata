<?php

namespace Ailove\OKBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Ailove\OKBundle\DependencyInjection\Security\Factory\OKFactory;

/**
 * OKBundle
 */
class OKBundle extends Bundle
{
    /**
     * Add security listener factory
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new OKFactory());
    }
}
