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

use Phalcon\Mvc\Micro;

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
     * @return mixed|void
     */
    public function run()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return $this->application->handle($uri);
    }
}
