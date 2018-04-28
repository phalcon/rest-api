<?php

namespace Niden\Bootstrap;

use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Micro;

abstract class AbstractBootstrap
{
    /** @var Micro|Console */
    protected $application;

    /** @var FactoryDefault */
    protected $container = null;

    /** @var array */
    protected $providers = [];

    /**
     * Runs the application
     *
     * @return Micro|string
     */
    public function run()
    {
        $this->container = new FactoryDefault();

        $this->setupApplication();
        $this->registerServices();

        return $this->runApplication();
    }

    /**
     * @return mixed
     */
    abstract protected function setupApplication();

    /**
     * Registers available services
     *
     * @return void
     */
    private function registerServices()
    {
        /** @var ServiceProviderInterface $provider */
        foreach ($this->providers as $provider) {
            (new $provider())->register($this->container);
        }
    }

    /**
     * @return Micro|string
     */
    abstract protected function runApplication();
}
