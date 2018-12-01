<?php

namespace Gewaer\Cli\Tasks;

use Phalcon\Cli\Task as PhTask;

/**
 * Class AclTask
 *
 * @package Gewaer\Cli\Tasks;
 *
 * @property \Gewaer\Acl\Manager $acl
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
