<?php

namespace Ailove\VKBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VKExtension extends Extension
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

        $container->setParameter('vk.app_id', $config['app_id']);
        $container->setParameter('vk.app_secret', $config['app_secret']);
        $container->setParameter('vk.access_token_url', $config['access_token_url']);
        $container->setParameter('vk.scope', $config['scope']);
        $container->setParameter('vk.redirect_uri', $config['redirect_uri']);
        $container->setParameter('vk.dialog_url', $config['dialog_url']);
        $container->setParameter('vk.oauth_proxy_class', $config['oauth_proxy_class']);
    }
}
