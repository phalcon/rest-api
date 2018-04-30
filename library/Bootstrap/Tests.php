<?php

namespace Niden\Bootstrap;

use Phalcon\Cli\Console;
use Phalcon\Mvc\Micro;

class Tests extends AbstractBootstrap
{
    /**
     * Run the application
     *
     * @return Micro|Console|string|void
     */
    protected function runApplication()
    {
        return $this->application;
    }
}
