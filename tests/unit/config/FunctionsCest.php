<?php

namespace Phalcon\Api\Tests\unit\config;

use Phalcon\Api\Constants\Relationships;
use UnitTester;

use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\appUrl;
use function Phalcon\Api\Core\envValue;

class FunctionsCest
{
    private array $store;

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function __before(UnitTester $I)
    {
        $this->store = $_ENV ?? [];
    }

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function __after(UnitTester $I)
    {
        $_ENV = $this->store;
    }

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function checkApppath(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__)));
        $I->assertSame($path, appPath());
    }

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function checkApppathWithParameter(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__))) . '/library/Core/config.php';
        $I->assertSame($path, appPath('library/Core/config.php'));
    }

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function checkEnvvalueAsFalse(UnitTester $I)
    {
        $_ENV['SOMEVAL'] = false;
        $I->assertFalse(envValue('SOMEVAL'));
    }

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function checkEnvvalueAsTrue(UnitTester $I)
    {
        $_ENV['SOMEVAL'] = true;
        $I->assertTrue(envValue('SOMEVAL'));
    }

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function checkEnvvalueWithValue(UnitTester $I)
    {
        $_ENV['SOMEVAL'] = 'someval';
        $I->assertSame('someval', envValue('SOMEVAL'));
    }

    /**
     * @param UnitTester $I
     *
     * @return void
     */
    public function checkEnvurlWithUrl(UnitTester $I)
    {
        $I->assertSame(
            'http://api.phalcon.ld/companies/1',
            appUrl(Relationships::COMPANIES, 1)
        );
    }
}
