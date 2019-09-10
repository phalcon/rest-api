<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Bootstrap;

use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault\Cli as PhCli;
use function Phalcon\Api\Core\appPath;

/**
 * Class Cli
 *
 * @property Console $application
 */
class Cli extends AbstractBootstrap
{
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
     * Parses arguments from the command line
     */
    private function processArguments()
    {
        $this->options = [
            'task' => 'Main',
        ];

        $options = [
            'clear-cache' => 'ClearCache',
            'help'        => 'Main',
        ];

        $arguments = getopt('', array_keys($options));

        foreach ($options as $option => $task) {
            if (true === isset($arguments[$option])) {
                $this->options['task'] = $task;
            }
        }
    }
}
