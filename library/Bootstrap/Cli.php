<?php

declare(strict_types=1);

namespace Gewaer\Bootstrap;

use function Gewaer\Core\appPath;
use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault\Cli as PhCli;

/**
 * Class Cli
 *
 * @package Gewaer\Bootstrap
 *
 * @property Console $application
 */
class Cli extends AbstractBootstrap
{
    private $argv;

    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        return $this->application->handle($this->options);
    }

    /**
     * @return mixed
     */
    public function setup()
    {
        $this->container = new PhCli();
        $this->providers = require appPath('cli/config/providers.php');

        $this->processArguments();

        parent::setup();
    }

    /**
     * Setup the application object in the container
     *
     * @return void
     */
    protected function setupApplication()
    {
        $this->application = new Console($this->container);
        $this->container->setShared('application', $this->application);
    }

    /**
     * Pass php argv
     *
     * @param array $argv
     * @return void
     */
    public function setArgv(array $argv): void
    {
        $this->argv = $argv;
    }

    /**
     * Parses arguments from the command line
     */
    private function processArguments()
    {
        $arguments = [];
        foreach ($this->argv as $k => $arg) {
            if ($k == 1) {
                $arguments['task'] = $arg;
            } elseif ($k == 2) {
                $arguments['action'] = $arg;
            } elseif ($k >= 3) {
                $arguments['params'][] = $arg;
            }
        }

        $this->options = $arguments;
    }
}
