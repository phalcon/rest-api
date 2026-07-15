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

use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Model\MetaData\Memory;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

final class ModelsMetadataTest extends AbstractUnitTestCase
{
    public function testRegistration(): void
    {
        $diContainer = new FactoryDefault();
        $config      = new ConfigProvider();
        $config->register($diContainer);
        $provider = new ModelsMetadataProvider();
        $provider->register($diContainer);

        $this->assertTrue($diContainer->has('modelsMetadata'));
        /** @var Memory $metadata */
        $metadata = $diContainer->getShared('modelsMetadata');
        $this->assertTrue($metadata instanceof Memory);
    }
}
