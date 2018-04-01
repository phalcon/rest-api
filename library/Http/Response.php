<?php

namespace Niden\Http;

use Phalcon\Http\Response as PhResponse;

class Response extends PhResponse
{
    /** @var int */
    const STATUS_SUCCESS = 2000;

    /** @var int */
    const STATUS_ERROR   = 3000;

    /** @var array */
    protected $payloadCode = self::STATUS_SUCCESS;

    /**
     * Sets the payload code as Error
     *
     * @return Response
     */
    public function setPayloadStatusError(): Response
    {
        $this->payloadCode = self::STATUS_ERROR;

        return $this;
    }

    /**
     * Sets the payload code as Success
     *
     * @return Response
     */
    public function setPayloadStatusSuccess(): Response
    {
        $this->payloadCode = self::STATUS_SUCCESS;

        return $this;
    }

    /**
     * Sets the API payload to return back. Used instead of the setContent or
     * setJsonContent, so as to provide a uniformed payload no matter what
     * the response is
     *
     * @param string|array $content The content
     *
     * @return Response
     */
    public function setPayloadContent($content = []): Response
    {
        $data      = (true === is_array($content)) ? $content : [$content];
        $timestamp = date('c');
        $payload   = [
            'code'      => $this->payloadCode,
            'timestamp' => $timestamp,
            'hash'      => sha1($timestamp . json_encode($data)),
            'data'      => $data,
        ];

        parent::setJsonContent($payload);

        $this->setStatusCode(200);
        $this->setContentType('application/json', 'UTF-8');
        $this->setHeader('E-Tag', sha1($this->getContent()));

        return $this;
    }
}
