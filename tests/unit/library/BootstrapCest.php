<?php

namespace Niden\Tests\unit;

use function Niden\Functions\appPath;
use Phalcon\Di;
use function strtolower;
use Niden\Logger;
use Phalcon\Logger\Formatter\Line;
use \UnitTester;

class BootstrapCest
{
    public function checkBootstrap(UnitTester $I)
    {
        $container   = Di::getDefault();
        $application = $container->getShared('application');

        $I->assertTrue()

        /**
         * Before rollback
         */
        $fileName = $I->getNewFileName('log', 'log');
        $logger = new Logger($this->logPath . $fileName);
            $logger->alert('Hello');
            $logger->close();
            $I->amInPath($this->logPath);
            $I->openFile($fileName);
            $I->seeFileContentsEqual(
                sprintf(
                    "[%s][ALERT] Hello\n",
                    date('D, d M y H:i:s O')
                )
            );
            $I->deleteFile($this->logPath . $fileName);
        /**
         * After rollback
         */
        $fileName = $I->getNewFileName('log', 'log');
        $logger = new Logger($this->logPath . $fileName);
        $logger->info('Hello');
        $logger->begin();
        $logger->info('Message 1');
        $logger->info('Message 2');
        $logger->info('Message 3');
        $logger->rollback();
        $logger->close();
        $I->amInPath($this->logPath);
        $I->openFile($fileName);
        $I->seeFileContentsEqual(
            sprintf(
                "[%s][INFO] Hello\n",
                date('D, d M y H:i:s O')
            )
        );
        $I->deleteFile($this->logPath . $fileName);
    }

    protected function runLogging(UnitTester $I, $level, $name = null, $native = true)
    {
        $fileName = $I->getNewFileName('log', 'log');

        $logger = new Logger($this->logPath . $fileName);
        $name   = (null === $name) ? 'DEBUG' : $name;

        if (true === $native) {
            $function = strtolower($name);
            $logger->$function('Hello');
        } else {
            $logger->log($level, 'Hello');
        }
        $logger->close();

        $I->amInPath($this->logPath);
        $I->openFile($fileName);
        $expected = sprintf(
            "[%s][%s] Hello",
            date('D, d M y H:i:s O'),
            $name
        );
        $I->seeInThisFile($expected);
        $I->deleteFile($fileName);
    }
}
