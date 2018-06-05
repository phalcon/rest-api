<?php

namespace Niden\Bootstrap;

use function Niden\Core\appPath;
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
        $this->providers = require appPath('api/config/providers.php');

        parent::setup();
    }
}
