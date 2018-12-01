<?php

declare(strict_types=1);

namespace Gewaer\Bootstrap;

use Gewaer\Http\Response;
use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli as PhCli;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Micro;

/**
 * Absstract class that provides the boostrap structure for any Micro PhalconPHP App
 */
abstract class AbstractBootstrap
{
    /** @var Micro|Console */
    protected $application;

    /** @var FactoryDefault|PhCli */
    protected $container;

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $providers = [];

    /**
     * @return Console|Micro
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return FactoryDefault|PhCli
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
        //setup the phalcon micro application
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
        /** @var ServiceProviderInterface $provider */
        foreach ($this->providers as $provider) {
            (new $provider())->register($this->container);
        }
    }
}
