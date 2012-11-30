<?php

namespace Ailove\VKBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('vk');

        $rootNode
            ->children()
                ->scalarNode('app_id')->cannotBeEmpty()->end()
                ->scalarNode('app_secret')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('access_token_url')
                    ->defaultValue('http://oauth.vk.com/oauth/authorize')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('oauth_proxy_class')
                    ->defaultValue('Oauth2Proxy')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('redirect_uri')->end()
                ->scalarNode('scope')->end()
                ->scalarNode('dialog_url')
                    ->defaultValue('http://oauth.vk.com/oauth/authorize')
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
