<?php

declare(strict_types=1);

namespace Gewaer\Exception;

class HttpException extends Exception
{
    protected $httpCode;
    protected $httpMessage = 'FAILED';
    protected $data;

    /**
     * Returns the httpcode attached to the exception
     *
     * @return string
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     *  Returns the http message attached to the exception
     *
     * @return string
     */
    public function getHttpMessage(): string
    {
        return $this->httpMessage;
    }

    /**
    * Returns the data attached to the exception
    *
    * @return string
    */
    public function getData()
    {
        return $this->data;
    }
}
