<?php

namespace FFreitasBr\CommandLockBundle\Integration\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use FFreitasBr\CommandLockBundle\DependencyInjection\CommandLockExtension;
use FFreitasBr\CommandLockBundle\Traits\NamesDefinitionsTrait;

/**
 * Class CommandLockExtensionTest
 *
 * @package FFreitasBr\CommandLockBundle\Integration\DependencyInjection
 */
class CommandLockExtensionTest extends \PHPUnit_Framework_TestCase
{
    use NamesDefinitionsTrait;

    /**
     * @var null|ContainerBuilder
     */
    protected static $container = null;

    /**
     * @var null|CommandLockExtension
     */
    protected static $extension = null;

    /**
     * @var null|string
     */
    protected static $pidDirectory = null;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        static::$container = new ContainerBuilder();
        static::$extension = new CommandLockExtension();
        static::$pidDirectory = sys_get_temp_dir().'/FFreitasBrCommandLockBundle/pid_directory';
    }

    public function testMustTriggerExceptionWhenLoadBundleWithoutPidDirectoryConfiguration()
    {
        $pidDirectorySetting   = $this->pidDirectorySetting;
        $configurationRootName = $this->configurationRootName;
        $this->setExpectedException(
            '\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            "The child node \"{$pidDirectorySetting}\" at path \"{$configurationRootName}\" must be configured.",
            0
        );
        $configuration = array();
        static::$extension->load($configuration, static::$container);
    }

    public function testMustRegisterParameterInContainerWithTheFullPidDirectoryAndMustCreateIt()
    {
        $fs = new Filesystem();
        $fs->remove(static::$pidDirectory);
        $configuration = array(
            0 => array(
                $this->pidDirectorySetting => static::$pidDirectory
            )
        );
        static::$extension->load($configuration, static::$container);
        $this->assertTrue(static::$container->hasParameter($this->configurationsParameterKey));
        $configurations = static::$container->getParameter($this->configurationsParameterKey);
        $this->assertArrayHasKey($this->pidDirectorySetting, $configurations);
        $this->assertNotNull($configurations[$this->pidDirectorySetting]);
        $this->assertEquals(static::$pidDirectory, $configurations[$this->pidDirectorySetting]);
        $this->assertTrue(file_exists(static::$pidDirectory));
    }

    public function testMustRegisterEventListenerServiceInContainerAndItMustHaveTheCorrectDefinition()
    {
        $configuration = array(
            0 => array(
                $this->pidDirectorySetting => static::$pidDirectory
            )
        );
        static::$extension->load($configuration, static::$container);
        $this->assertTrue(static::$container->hasDefinition('command_lock_bundle.command_lock.event_listener'));
        /* @var \Symfony\Component\DependencyInjection\Definition $definition */
        $definition = static::$container->getDefinition('command_lock_bundle.command_lock.event_listener');
        $this->assertEquals('%command_lock_bundle.command_lock.event_listener.class%', $definition->getClass());
        $tags = $definition->getTags();
        $this->assertCount(1, $tags);
        $this->assertArrayHasKey('kernel.event_listener', $tags);
        $this->assertCount(2, $tags['kernel.event_listener']);
        $arguments = $definition->getArguments();
        $this->assertCount(1, $arguments);
        $this->assertEquals('service_container', (string)$arguments[0]);
    }

    public function testMustRegisterExceptionListWithEmptyArray()
    {
        $configuration = array(
            0 => array(
                $this->pidDirectorySetting => static::$pidDirectory
            )
        );
        static::$extension->load($configuration, static::$container);
        $configurations = static::$container->getParameter($this->configurationsParameterKey);
        $this->assertTrue(is_array($configurations[$this->exceptionsListSetting]));
        $this->assertEquals(array(), $configurations[$this->exceptionsListSetting]);
    }

    public function testMustRegisterExceptionListWithConfiguredArray()
    {
        $configuration = array(
            0 => array(
                $this->pidDirectorySetting => static::$pidDirectory,
                $this->exceptionsListSetting => array('test1', 'test2')
            )
        );
        static::$extension->load($configuration, static::$container);
        $configurations = static::$container->getParameter($this->configurationsParameterKey);
        $this->assertTrue(is_array($configurations[$this->exceptionsListSetting]));
        $this->assertEquals(array('test1', 'test2'), $configurations[$this->exceptionsListSetting]);
    }
}
