<?php

namespace FFreitasBr\CommandLockBundle\Unit\Exception;

use FFreitasBr\CommandLockBundle\Exception\CommandAlreadyRunningException;

/**
 * Class ReprocessMessageTest
 *
 * @package WestwingBrazil\MessageBusBundle\Unit\Entity
 */
class CommandAlreadyRunningExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testSetCommandNameMustUpdateMessageCorrectlyAndMustReturnItself()
    {
        $exception = new CommandAlreadyRunningException;
        $this->assertEquals("Command \"unknown\" already running with pid \"unknown\"", $exception->getMessage());
        $exception->setCommandName('command_name');
        $this->assertEquals("Command \"command_name\" already running with pid \"unknown\"", $exception->getMessage());
        $return = $exception->setCommandName(null);
        $this->assertEquals("Command \"unknown\" already running with pid \"unknown\"", $exception->getMessage());
        $this->assertSame($exception, $return);
    }
    
    public function testSetPidNumberMustUpdateMessageCorrectlyAndMustReturnItself()
    {
        $exception = new CommandAlreadyRunningException;
        $this->assertEquals("Command \"unknown\" already running with pid \"unknown\"", $exception->getMessage());
        $exception->setPidNumber(666);
        $this->assertEquals("Command \"unknown\" already running with pid \"666\"", $exception->getMessage());
        $return = $exception->setPidNumber(null);
        $this->assertEquals("Command \"unknown\" already running with pid \"unknown\"", $exception->getMessage());
        $this->assertSame($exception, $return);
    }
}
