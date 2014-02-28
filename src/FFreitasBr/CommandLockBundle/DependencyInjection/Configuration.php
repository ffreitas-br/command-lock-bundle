<?php

namespace FFreitasBr\CommandLockBundle\DependencyInjection;

use FFreitasBr\CommandLockBundle\Traits\NamesDefinitionsTrait;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package FFreitasBr\CommandLockBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    use NamesDefinitionsTrait;

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->configurationRootName);

        $rootNode
            ->children()
                ->scalarNode($this->pidDirectorySetting)
                    ->info('Define where the pid files will be stored')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
