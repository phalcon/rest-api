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

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use function Phalcon\Api\Core\appPath;

/**
 * Class Api
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
        return $this->application->handle($_SERVER['REQUEST_URI']);
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
