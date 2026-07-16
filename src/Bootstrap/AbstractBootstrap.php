<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Bootstrap;

use Phalcon\Api\Http\Response;
use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli as PhCli;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Micro;

use function Phalcon\Api\Core\appPath;

abstract class AbstractBootstrap
{
    /**
     * Both are set by the time the constructor returns - the container either
     * by a subclass before parent::__construct() or by it, the application by
     * setupApplication(). Declared without a null default so that is the type
     * rather than a promise.
     *
     * @var Console|Micro
     */
    protected Console|Micro $application;

    /** @var FactoryDefault|PhCli */
    protected FactoryDefault|PhCli $container;

    /** @var array<string, string> */
    protected array $options = [];

    /** @var array<int, class-string<ServiceProviderInterface>> */
    protected array $providers = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        if (false === isset($this->container)) {
            $this->container = new FactoryDefault();
        }

        if ([] === $this->providers) {
            $this->providers = require appPath($this->providersPath());
        }

        $this
            ->setupApplication()
            ->registerServices()
        ;
    }

    /**
     * @return Console|Micro
     */
    public function getApplication(): Console|Micro
    {
        return $this->application;
    }

    /**
     * @return FactoryDefault|PhCli
     */
    public function getContainer(): FactoryDefault|PhCli
    {
        return $this->container;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->container->getShared('response');
    }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * The application class this bootstrap builds - a Micro for HTTP, a Console
     * for the command line.
     *
     * @return class-string<Console|Micro>
     */
    abstract protected function applicationClass(): string;

    /**
     * The provider list this application registers, relative to the app root.
     *
     * @return string
     */
    abstract protected function providersPath(): string;

    /**
     * Set up the application object in the container
     *
     * @return AbstractBootstrap
     */
    protected function setupApplication(): AbstractBootstrap
    {
        $class             = $this->applicationClass();
        $this->application = new $class($this->container);

        $this->container->setShared('application', $this->application);

        return $this;
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
