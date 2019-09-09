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
        $this->container = new FactoryDefault();
        $this->providers = require appPath('api/config/providers.php');

        parent::setup();
    }
}
