<?php

namespace Niden\Bootstrap;

use Phalcon\Di\FactoryDefault;

class Api extends AbstractBootstrap
{
    /**
     * @return mixed
     */
    public function run()
    {
        $this->container = new FactoryDefault();

        return parent::run();
    }

    /**
     * Run the application
     *
     * @return mixed
     */
    protected function runApplication()
    {
        return $this->application->handle();
    }
}
