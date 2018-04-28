<?php

namespace Niden\Bootstrap;

use function Niden\Functions\appPath;
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
     * Setup the application object in the container
     *
     * @return void
     */
    protected function setupApplication()
    {
        $this->application = new Micro($this->container);
        $this->container->setShared('application', $this->application);
    }

    /**
     * Registers available services
     *
     * @return void
     */
    private function registerServices()
    {
        /**
         * Get the providers from the config file
         */
        $providers = require appPath('config/providers.php');

        /** @var ServiceProviderInterface $provider */
        foreach ($providers as $provider) {
            (new $provider())->register($this->container);
        }
    }

    /**
     * @return Micro|string
     */
    abstract protected function runApplication();
}
