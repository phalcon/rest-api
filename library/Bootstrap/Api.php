<?php

declare(strict_types=1);

namespace Niden\Bootstrap;

use function Niden\Core\appPath;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;

/**
 * Class Api
 *
 * @package Niden\Bootstrap
 *
 * @property Micro $application
 */
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
        //set the default DI
        $this->container = new FactoryDefault();
        //set all the services
        $this->providers = require appPath('api/config/providers.php');

        //run my parents setup
        parent::setup();
    }
}
