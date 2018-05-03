<?php

namespace Niden\Tests\unit;

use function Niden\Functions\appPath;
use function strtolower;
use Niden\Logger;
use Phalcon\Logger\Formatter\Line;
use \UnitTester;

class LoggerCest
{
    protected $logPath = '';

    public function __construct()
    {
        $this->logPath = appPath('storage/logs/');
    }

    public function loggerCheckErrorLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::ERROR, 'ERROR');
        $this->runLogging($I, Logger::ERROR, 'ERROR', false);
    }

    public function loggerCheckCriticalLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::CRITICAL, 'CRITICAL');
        $this->runLogging($I, Logger::CRITICAL, 'CRITICAL', false);
    }

    public function loggerCheckDebugLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::DEBUG, 'DEBUG');
        $this->runLogging($I, Logger::DEBUG, 'DEBUG', false);
    }

    public function loggerCheckEmergencyLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::EMERGENCY, 'EMERGENCY');
        $this->runLogging($I, Logger::EMERGENCY, 'EMERGENCY', false);
    }

    public function loggerCheckNoticeLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::NOTICE, 'NOTICE');
        $this->runLogging($I, Logger::NOTICE, 'NOTICE', false);
    }

    public function loggerCheckInfoLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::INFO, 'INFO');
        $this->runLogging($I, Logger::INFO, 'INFO', false);
    }

    public function loggerCheckWarningLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::WARNING, 'WARNING');
        $this->runLogging($I, Logger::WARNING, 'WARNING', false);
    }

    public function loggerCheckAlertLogging(UnitTester $I)
    {
        $this->runLogging($I, Logger::ALERT, 'ALERT');
        $this->runLogging($I, Logger::ALERT, 'ALERT', false);
    }

    public function loggerCheckFormatter(UnitTester $I)
    {
        $fileName = $I->getNewFileName('log', 'log');
        $logger = new Logger($this->logPath . $fileName);

        $format = '[%date%][%type%] %message%';
        $date   = 'd-M-Y H:i:s';

        $logger->setFormatter(new Line($format, $date));

        $I->assertEquals($format, $logger->getFormatter()->getFormat());
    }

    public function loggerCheckLevel(UnitTester $I)
    {
        $fileName = $I->getNewFileName('log', 'log');
        $logger = new Logger($this->logPath . $fileName);

        $level  = Logger::ALERT;
        $format = '[%date%][%type%] %message%';
        $date   = 'd-M-Y H:i:s';

        $logger->setFormatter(new Line($format, $date));
        $logger->setLogLevel($level);

        $I->assertEquals(Logger::ALERT, $logger->getLogLevel());
    }

    public function loggerCommit(UnitTester $I)
    {
        /**
         * Before commit
         */
        $fileName = $I->getNewFileName('log', 'log');
        $logger = new Logger($this->logPath . $fileName);
        $logger->begin();
        $logger->alert('Hello');
        $logger->commit();
        $I->amInPath($this->logPath);
        $I->openFile($fileName);
        $I->seeNumberNewLines(2);
        $logger->close();
        $I->deleteFile($this->logPath . $fileName);

        /**
         * After commit
         */
        $fileName = $I->getNewFileName('log', 'log');
        $logger = new Logger($this->logPath . $fileName);
        $logger->info('Hello');
        $logger->begin();
        $logger->info('Message 1');
        $logger->info('Message 2');
        $logger->info('Message 3');
        $logger->commit();
        $logger->close();
        $contents = \file($this->logPath . $fileName);
        $I->deleteFile($this->logPath . $fileName);
        $I->assertEquals(4, count($contents));
    }

    public function loggerRollback(UnitTester $I)
    {
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
