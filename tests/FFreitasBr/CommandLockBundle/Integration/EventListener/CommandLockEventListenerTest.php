<?php

namespace FFreitasBr\CommandLockBundle\Integration\EventListener;

use FFreitasBr\CommandLockBundle\DependencyInjection\CommandLockBundleExtension;
use FFreitasBr\CommandLockBundle\EventListener\CommandLockEventListener;
use FFreitasBr\CommandLockBundle\Traits\NamesDefinitionsTrait;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CommandLockEventListenerTest
 *
 * @package FFreitasBr\CommandLockBundle\Integration\EventListener
 */
class CommandLockEventListenerTest extends \PHPUnit_Framework_TestCase
{
    use NamesDefinitionsTrait;

    /**
     * @var null|Container
     */
    protected $container = null;

    /**
     * @var null|string
     */
    protected $pidDirectory = null;

    /**
     * @var null|CommandLockEventListener
     */
    protected static $firstInitiatedListener = null;
    
    /**
     * @return void
     */
    public function setUp()
    {
        // create container
        $this->container = new Container();
        // create pid directory
        $this->pidDirectory = sys_get_temp_dir().'/FFreitasBrCommandLockBundle/pid_directory';
        $fs = new Filesystem();
        if (!$fs->exists($this->pidDirectory)) {
            $fs->mkdir($this->pidDirectory);
        }
        // configure container
        $this->container->setParameter(
            $this->configurationsParameterKey,
            array(
                $this->pidDirectorySetting => $this->pidDirectory,
            )
        );
    }

    public function testCleanStringMustRemoveSpecialCharacters()
    {
        $eventListener = new CommandLockEventListener($this->container);
        $this->assertEquals(
            'a-b-c',
            $eventListener->cleanString('a-b c|!@#$%^&*(){}[]\\:;"\'<>,./?~`ºª•¶§∞¢£™¡“‘«æ…≤≥')
        );
    }
    
    public function testOnConsoleCommandMustCreatePidFile()
    {
        $pidFile = $this->pidDirectory.'/cacheclear.pid';
        $fs = new Filesystem();
        if (!$fs->exists($pidFile)) {
            $fs->remove($pidFile);
        }
        $myPid = getmypid();
        $commandForTesting   = new CacheClearCommand();
        $inputForTesting     = new ArrayInput(array());
        $outputForTesting    = new StreamOutput(fopen('php://memory', 'w', false));
        $consoleCommandEvent = new ConsoleCommandEvent($commandForTesting, $inputForTesting, $outputForTesting);
        static::$firstInitiatedListener = new CommandLockEventListener($this->container);
        static::$firstInitiatedListener->onConsoleCommand($consoleCommandEvent);
        $this->assertFileExists($pidFile);
        $this->assertEquals($myPid, file_get_contents($pidFile));
    }

    public function testOnConsoleCommandMustTriggerExceptionOfAlreadyRunning()
    {
        $myPid = getmypid();
        $commandForTesting   = new CacheClearCommand();
        $inputForTesting     = new ArrayInput(array());
        $outputForTesting    = new StreamOutput(fopen('php://memory', 'w', false));
        $consoleCommandEvent = new ConsoleCommandEvent($commandForTesting, $inputForTesting, $outputForTesting);
        $this->setExpectedException(
            '\FFreitasBr\CommandLockBundle\Exception\CommandAlreadyRunningException',
            "Command \"cache:clear\" already running with pid \"{$myPid}\"",
            0
        );
        static::$firstInitiatedListener->onConsoleCommand($consoleCommandEvent);
    }

    public function testOnTerminateCommandMustDeletePidFile()
    {
        $pidFile = $this->pidDirectory.'/cacheclear.pid';
        $myPid = getmypid();
        $commandForTesting     = new CacheClearCommand();
        $inputForTesting       = new ArrayInput(array());
        $outputForTesting      = new StreamOutput(fopen('php://memory', 'w', false));
        $consoleTerminateEvent = new ConsoleTerminateEvent($commandForTesting, $inputForTesting, $outputForTesting, 0);
        static::$firstInitiatedListener->onConsoleTerminate($consoleTerminateEvent);
        $this->assertFileNotExists($pidFile);
    }
}
