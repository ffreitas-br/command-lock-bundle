<?php

namespace FFreitasBr\CommandLockBundle\Traits;

/**
 * Class NamesDefinitionsTrait
 *
 * @package FFreitasBr\CommandLockBundle\Traits
 */
trait NamesDefinitionsTrait
{
    protected $configurationsParameterKey = 'command_lock.configuration';
    protected $pidDirectorySetting        = 'pid_directory';
    protected $exceptionsListSetting      = 'exceptions';
    protected $configurationRootName      = 'command_lock';
}
