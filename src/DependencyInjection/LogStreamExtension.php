<?php

namespace LogStream\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class LogStreamExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('log_stream.url', $config['url']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $clientProtocol = $config['websocket']['enabled'] ? 'websocket' : 'http';

        $loader->load('client_'.$clientProtocol.'.xml');
        $container->setAlias('log_stream.client', 'log_stream.'.$clientProtocol.'_client');
    }
}
