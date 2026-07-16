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

namespace Phalcon\Api\Tests\Unit\Library\Providers;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Providers\ResponseProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class ResponseTest extends AbstractUnitTestCase
{
    public function testRegistration(): void
    {
        $diContainer = new FactoryDefault();
        $provider    = new ResponseProvider();
        $provider->register($diContainer);

        $this->assertTrue($diContainer->has('response'));
        /** @var Response $response */
        $response = $diContainer->getShared('response');
        $this->assertTrue($response instanceof Response);
    }
}
