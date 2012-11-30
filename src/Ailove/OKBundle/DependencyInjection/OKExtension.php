<?php

namespace Ailove\OKBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OKExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ok.app_id', $config['app_id']);
        $container->setParameter('ok.app_secret', $config['app_secret']);
        $container->setParameter('ok.app_public_key', $config['app_public_key']);
        $container->setParameter('ok.access_token_url', $config['access_token_url']);
        $container->setParameter('ok.scope', $config['scope']);
        $container->setParameter('ok.redirect_uri', $config['redirect_uri']);
        $container->setParameter('ok.dialog_url', $config['dialog_url']);
        $container->setParameter('ok.oauth_proxy_class', $config['oauth_proxy_class']);
        $container->setParameter('ok.sdk_file', $config['sdk_file']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
