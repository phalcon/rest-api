<?php

namespace Gewaer\Cli\Tasks;

use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Cli\Task as PhTask;

/**
 * Class ClearcacheTask
 *
 * @package Niden\Cli\Tasks
 *
 * @property Libmemcached $cache
 */
class AclTask extends PhTask
{
    /**
     * Create the default roles of the system
     */
    public function mainAction()
    {
        $this->acl->addRole('Default.Administrator');
        $this->acl->addRole('Default.Agents');
        $this->acl->addRole('Default.Users');
    }
}
