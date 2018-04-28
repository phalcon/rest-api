<?php

namespace Niden\Bootstrap;

use Phalcon\Mvc\Micro;

class Tests extends AbstractBootstrap
{
    /**
     * Run the application
     *
     * @return Micro|string|void
     */
    protected function runApplication()
    {
        return $this->application;
    }
}
