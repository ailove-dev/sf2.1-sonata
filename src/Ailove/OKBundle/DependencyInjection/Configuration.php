<?php

namespace Ailove\OKBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('ok');

        $rootNode
            ->children()
                ->scalarNode('app_id')->cannotBeEmpty()->end()
                ->scalarNode('app_secret')->cannotBeEmpty()->end()
                ->scalarNode('app_public_key')->cannotBeEmpty()->end()
                ->scalarNode('access_token_url')
                    ->defaultValue('http://api.odnoklassniki.ru/oauth/token.do')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('redirect_uri')->end()
                ->scalarNode('scope')
                    ->defaultValue('VALUABLE ACCESS')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('dialog_url')
                    ->defaultValue('http://www.odnoklassniki.ru/oauth/authorize')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('oauth_proxy_class')
                    ->defaultValue('Ailove\OKBundle\Service\OKOauthSessionProxy')
                    ->cannotBeEmpty()
                ->end()
//                ->scalarNode('sdk_file')->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
