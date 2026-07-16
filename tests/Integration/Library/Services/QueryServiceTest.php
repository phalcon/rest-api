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

namespace Phalcon\Api\Tests\Integration\Library\Services;

use Phalcon\Api\Models\Companies;
use Phalcon\Api\Services\QueryService;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;

use function count;
use function Phalcon\Api\Core\appPath;
use function uniqid;

final class QueryServiceTest extends AbstractIntegrationTestCase
{
    public function testGetCompaniesCachedData(): void
    {
        $configData = require appPath('./src/Core/config.php');
        $this->assertTrue($configData['app']['devMode']);

        $configData['app']['devMode'] = false;
        /** @var Config $config */
        $config    = new Config($configData);
        $container = $this->grabDi();
        $container->set('config', $config);
        $this->assertFalse($config->path('app.devMode'));

        /** @var Cache $cache */
        $cache = $this->grabFromDi('cache');
        $cache->clear();
        /** @var Config $config */
        $config = $this->grabFromDi('config');
        $this->assertFalse($config->path('app.devMode'));

        $queryService = new QueryService($config, $cache);

        /**
         * Company 1
         */
        $comName = uniqid('com-cached-');
        $comOne  = $this->haveRecordWithFields(
            Companies::class,
            [
                'name'    => $comName,
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

        $results = $queryService->getRecords(Companies::class);
        $this->assertSame(1, count($results));
        $this->assertSame($comName, $results[0]->get('name'));
        $this->assertSame($comOne->get('address'), $results[0]->get('address'));
        $this->assertSame($comOne->get('city'), $results[0]->get('city'));
        $this->assertSame($comOne->get('phone'), $results[0]->get('phone'));

        /**
         * Get the record again but ensure the name has been changed
         */
        $result = $comOne->set('name', 'com-cached-change')
                         ->save()
        ;
        $this->assertNotEquals(false, $result);

        /**
         * This should return the cached result
         */
        $results = $queryService->getRecords(Companies::class);
        $this->assertSame(1, count($results));
        $this->assertSame($comName, $results[0]->get('name'));
        $this->assertSame($comOne->get('address'), $results[0]->get('address'));
        $this->assertSame($comOne->get('city'), $results[0]->get('city'));
        $this->assertSame($comOne->get('phone'), $results[0]->get('phone'));
    }
}
