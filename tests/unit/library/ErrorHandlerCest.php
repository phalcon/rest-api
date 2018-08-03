<?php

namespace Niden\Tests\unit\library;

use Niden\ErrorHandler;
use Niden\Logger;
use Niden\Providers\ConfigProvider;
use Niden\Providers\LoggerProvider;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use UnitTester;
use function Niden\Core\appPath;

class ErrorHandlerCest
{
    public function logErrorOnError(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);

        /** @var Config $config */
        $config = $diContainer->getShared('config');
        /** @var Logger $logger */
        $logger  = $diContainer->getShared('logger');
        $handler = new ErrorHandler($logger, $config);

        $handler->handle(1, 'test error', 'file.php', 4);
        $fileName = appPath('storage/logs/api.log');
        $I->openFile($fileName);
        $expected = '[ERROR] [#:1]-[L: 4] : test error (file.php)';
        $I->seeInThisFile($expected);
    }

    public function logErrorOnShutdown(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);

        /** @var Config $config */
        $config = $diContainer->getShared('config');
        /** @var Logger $logger */
        $logger  = $diContainer->getShared('logger');
        $handler = new ErrorHandler($logger, $config);

        $handler->shutdown();
        $fileName = appPath('storage/logs/api.log');
        $I->openFile($fileName);
        $expected = '[INFO] Shutdown completed';
        $I->seeInThisFile($expected);
    }
}
