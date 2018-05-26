<?php

namespace Niden\Bootstrap;

use function microtime;
use function Niden\Functions\appPath;
use Niden\Http\Response;
use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Micro;

abstract class AbstractBootstrap
{
    /** @var Micro|Console */
    protected $application;

    /** @var FactoryDefault|Cli */
    protected $container;

    /**
     * @return Console|Micro
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return FactoryDefault|Cli
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->container->getShared('response');
    }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * Runs the application
     */
    public function setup()
    {
        $this->container->set('metrics', microtime(true));
        $this->setupApplication();
        $this->registerServices();
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
}
