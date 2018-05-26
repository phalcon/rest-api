<?php

namespace Niden\Bootstrap;

use Phalcon\Di\FactoryDefault;

class Api extends AbstractBootstrap
{
    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        return $this->application->handle();
    }

    /**
     * @return mixed
     */
    public function setup()
    {
        $this->container = new FactoryDefault();

        parent::setup();
    }
}
