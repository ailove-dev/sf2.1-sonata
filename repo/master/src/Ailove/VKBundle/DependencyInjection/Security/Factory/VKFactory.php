<?php

namespace Ailove\VKBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

/**
 * VK security factory.
 */
class VKFactory extends AbstractFactory
{
    /**
     * Create factory.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container         DI container
     * @param string                                                  $id                ID
     * @param mixed                                                   $config            Config
     * @param object                                                  $userProvider      User provider
     * @param object                                                  $defaultEntryPoint Entry point
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        return parent::create($container, $id, $config, $userProvider, $defaultEntryPoint);
    }

    /**
     * Return position.
     *
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * Return key.
     *
     * @return string
     */
    public function getKey()
    {
        return 'vk_firewall';
    }

    /**
     * Add configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeDefinition $node Node definition
     */
    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);
    }

    /**
     * Return listener ID.
     *
     * @return string
     */
    protected function getListenerId()
    {
        return 'vk.firewall.listener';
    }

    /**
     * Create AuthProvider.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container      Symfony DI
     * @param string                                                  $id             Firewall id
     * @param mixed                                                   $config         Configuration
     * @param string                                                  $userProviderId User provider service ID
     *
     * @return string
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        if (isset($config['provider'])) {
            $authProviderId = 'vk.auth.provider.'.$id;

            $container
                ->setDefinition($authProviderId, new DefinitionDecorator('vk.auth.provider'))
                ->addArgument(new Reference('vk.oauth.proxy'))
                ->addArgument(new Reference('vk.user.provider'))
                ->addArgument(new Reference('service_container'));

            return $authProviderId;
        }

        // without user provider
        return 'vk.auth.provider';
    }
}
