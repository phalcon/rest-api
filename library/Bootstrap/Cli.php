<?php

declare(strict_types=1);

namespace Gewaer\Bootstrap;

use function Gewaer\Core\appPath;
use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault\Cli as PhCli;
use Throwable;
use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Constants\Flags;

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
        try {
            $config = $this->container->getConfig();

            return $this->application->handle($this->options);
        } catch (Throwable $e) {
            //only log when server error production is seerver error or dev
            if ($e instanceof ServerErrorHttpException || strtolower($config->app->env) != Flags::PRODUCTION) {
                $this->container->getLog()->error($e->getTraceAsString());
            }

            //we need to see it on the console -_-
            echo $e->getMessage();
        }
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
