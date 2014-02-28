<?php

namespace FFreitasBr\CommandLockBundle\DependencyInjection;

use FFreitasBr\CommandLockBundle\Traits\NamesDefinitionsTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class MessageBusExtension
 *
 * @package FFreitasBr\CommandLockBundle\DependencyInjection
 */
class CommandLockBundleExtension extends Extension
{
    use NamesDefinitionsTrait;
    
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     * 
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load configurations
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        // process the pid_directory configuration
        $config = $this->processPidDirectoryConfiguration($config);
        
        // save configurations in container
        $container->setParameter($this->configurationsParameterKey, $config);
        
        // load services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function processPidDirectoryConfiguration(array $config)
    {
        $pidDirectory = $config[$this->pidDirectorySetting];
        $fs = new Filesystem();
        if (!$fs->exists($pidDirectory)) {
            $fs->mkdir($pidDirectory);
        }
        $config[$this->pidDirectorySetting] =
            ($fs->isAbsolutePath($pidDirectory))
                ? $pidDirectory
                : realpath($pidDirectory);
        return $config;
    }
}
