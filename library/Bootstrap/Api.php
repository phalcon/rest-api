<?php

namespace Niden\Bootstrap;

use function Niden\Functions\appPath;
use Phalcon\Mvc\Micro;

class Bootstrap extends AbstractBootstrap
{
    /**
     * Run/Setup the application
     *
     * @return Micro
     */
    public function run()
    {
        /**
         * Get the providers from the config file
         */
        $providers = require appPath('config/providers.php');

        $this->providers = $providers;

        return parent::run();
    }

    /**
     * Run the application
     *
     * @return Micro|string|void
     */
    protected function runApplication()
    {
        echo $this->application->handle()->getContent();
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
}
