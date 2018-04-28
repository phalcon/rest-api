<?php

namespace Niden;

use Phalcon\Logger\Adapter\File as PhLogger;
use Phalcon\Logger\FormatterInterface as PhFormatter;
use Psr\Log\LoggerInterface as PInterface;

class Logger implements PInterface
{
    const ALERT     = 2;
    const CRITICAL  = 1;
    const CUSTOM    = 8;
    const DEBUG     = 7;
    const EMERGENCY = 0;
    const ERROR     = 3;
    const INFO      = 6;
    const NOTICE    = 5;
    const SPECIAL   = 9;
    const WARNING   = 4;

    private $logger = null;

    /**
     * Logger constructor.
     *
     * @param string     $name
     * @param null|array $options
     */
    public function __construct($name, $options = null)
    {
        $this->logger = new PhLogger($name, $options);
    }

    /**
     * Sets the logger formatter
     *
     * @param \Phalcon\Logger\FormatterInterface $formatter
     */
    public function setFormatter(PhFormatter $formatter)
    {
        $this->logger->setFormatter($formatter);
    }

    /**
     * Gets the formatter
     *
     * @return \Phalcon\Logger\FormatterInterface
     */
    public function getFormatter()
    {
        return $this->logger->getFormatter();
    }

    /**
     * Sets the log level
     *
     * @param int $level
     */
    public function setLogLevel($level)
    {
        $this->logger->setLogLevel($level);
    }

    /**
     * Gets the log level
     *
     * @return int
     */
    public function getLogLevel()
    {
        return $this->logger->getLogLevel();
    }

    /**
     * Begins a logging transaction
     */
    public function begin()
    {
        $this->logger->begin();
    }

    /**
     * Commits a logging transaction
     */
    public function commit()
    {
        $this->logger->commit();
    }

    /**
     * Rolls back a logging transaction
     */
    public function rollback()
    {
        $this->logger->rollback();
    }

    /**
     * Closes the transaction
     */
    public function close()
    {
        $this->logger->close();
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->logger->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->logger->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->logger->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->logger->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }
}
