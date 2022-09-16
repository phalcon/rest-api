<?php

namespace Phalcon\Api\Tests\unit\config;

use Phalcon\Api\Http\Response;
use UnitTester;

use function function_exists;
use function Phalcon\Api\Core\appPath;

class AutoloaderCest
{
    public function checkDotenvVariables(UnitTester $I)
    {
        require appPath('library/Core/autoload.php');

        $I->assertNotEquals(false, $_ENV['APP_DEBUG']);
        $I->assertNotEquals(false, $_ENV['APP_ENV']);
        $I->assertNotEquals(false, $_ENV['APP_URL']);
        $I->assertNotEquals(false, $_ENV['APP_NAME']);
        $I->assertNotEquals(false, $_ENV['APP_BASE_URI']);
        $I->assertNotEquals(false, $_ENV['APP_SUPPORT_EMAIL']);
        $I->assertNotEquals(false, $_ENV['APP_TIMEZONE']);
        $I->assertNotEquals(false, $_ENV['CACHE_PREFIX']);
        $I->assertNotEquals(false, $_ENV['CACHE_LIFETIME']);
        $I->assertNotEquals(false, $_ENV['DATA_API_MYSQL_NAME']);
        $I->assertNotEquals(false, $_ENV['LOGGER_DEFAULT_FILENAME']);
        $I->assertNotEquals(false, $_ENV['VERSION']);

        $I->assertSame('true', $_ENV['APP_DEBUG']);
        $I->assertSame('development', $_ENV['APP_ENV']);
        $I->assertSame('http://api.phalcon.ld', $_ENV['APP_URL']);
        $I->assertSame('/', $_ENV['APP_BASE_URI']);
        $I->assertSame('team@phalcon.io', $_ENV['APP_SUPPORT_EMAIL']);
        $I->assertSame('UTC', $_ENV['APP_TIMEZONE']);
        $I->assertSame('api_cache_', $_ENV['CACHE_PREFIX']);
        $I->assertSame('86400', $_ENV['CACHE_LIFETIME']);
        $I->assertSame('api', $_ENV['LOGGER_DEFAULT_FILENAME']);
        $I->assertSame('20180401', $_ENV['VERSION']);
    }

    public function checkAutoloader(UnitTester $I)
    {
        require appPath('library/Core/autoload.php');

        $class = new Response();
        $I->assertTrue($class instanceof Response);
        $I->assertTrue(function_exists('Phalcon\Api\Core\envValue'));
    }
}
