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

namespace Phalcon\Api\Tests\Unit\Config;

use Phalcon\Api\Http\Response;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;

use function function_exists;
use function Phalcon\Api\Core\appPath;

final class AutoloaderTest extends AbstractUnitTestCase
{
    public function testAutoloader(): void
    {
        require appPath('src/Core/autoload.php');

        $class = new Response();
        $this->assertTrue($class instanceof Response);
        $this->assertTrue(function_exists('Phalcon\Api\Core\envValue'));
    }

    public function testDotenvVariables(): void
    {
        require appPath('src/Core/autoload.php');

        $this->assertNotEquals(false, $_ENV['APP_DEBUG']);
        $this->assertNotEquals(false, $_ENV['APP_ENV']);
        $this->assertNotEquals(false, $_ENV['APP_URL']);
        $this->assertNotEquals(false, $_ENV['APP_NAME']);
        $this->assertNotEquals(false, $_ENV['APP_BASE_URI']);
        $this->assertNotEquals(false, $_ENV['APP_SUPPORT_EMAIL']);
        $this->assertNotEquals(false, $_ENV['APP_TIMEZONE']);
        $this->assertNotEquals(false, $_ENV['CACHE_PREFIX']);
        $this->assertNotEquals(false, $_ENV['CACHE_LIFETIME']);
        $this->assertNotEquals(false, $_ENV['DATA_API_MYSQL_NAME']);
        $this->assertNotEquals(false, $_ENV['LOGGER_DEFAULT_FILENAME']);
        $this->assertNotEquals(false, $_ENV['VERSION']);

        $this->assertSame('true', $_ENV['APP_DEBUG']);
        $this->assertSame('development', $_ENV['APP_ENV']);
        $this->assertSame('http://api.phalcon.ld', $_ENV['APP_URL']);
        $this->assertSame('/', $_ENV['APP_BASE_URI']);
        $this->assertSame('team@phalcon.io', $_ENV['APP_SUPPORT_EMAIL']);
        $this->assertSame('UTC', $_ENV['APP_TIMEZONE']);
        $this->assertSame('api_cache_', $_ENV['CACHE_PREFIX']);
        $this->assertSame('86400', $_ENV['CACHE_LIFETIME']);
        $this->assertSame('api', $_ENV['LOGGER_DEFAULT_FILENAME']);
        $this->assertSame('20180401', $_ENV['VERSION']);
    }
}
