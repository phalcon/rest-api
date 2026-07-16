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

namespace Phalcon\Api\Tests\Unit;

use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function Phalcon\Api\Core\appPath;

final class BootstrapTest extends AbstractUnitTestCase
{
    public function testBootstrap(): void
    {
        ob_start();
        require appPath('public/index.php');
        $actual = ob_get_contents();
        ob_end_clean();

        $results = json_decode($actual, true);
        $this->assertSame('1.0', $results['jsonapi']['version']);
        $this->assertTrue(empty($results['data']));
        // Was Codeception's HttpCode::getDescription(404), which renders exactly this.
        $this->assertSame('404 (Not Found)', $results['errors'][0]);
    }
}
