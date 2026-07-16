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

namespace Phalcon\Api\Tests\Unit\Library;

use Phalcon\Api\Bootstrap\Api;
use Phalcon\Api\Http\Response;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class BootstrapTest extends AbstractUnitTestCase
{
    public function testBootstrap(): void
    {
        $bootstrap = new Api();

        $this->assertTrue($bootstrap->getContainer() instanceof FactoryDefault);
        $this->assertTrue($bootstrap->getResponse() instanceof Response);
        $this->assertTrue($bootstrap->getApplication() instanceof Micro);
    }
}
