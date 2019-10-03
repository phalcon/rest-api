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

class Tests extends Api
{
    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        return $this->application;
    }
}
