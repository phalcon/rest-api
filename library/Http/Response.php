<?php

namespace Niden\Http;

use Phalcon\Http\Response as PhResponse;

class Response extends PhResponse
{
    /** @var int */
    const STATUS_SUCCESS = 2000;

    /** @var int */
    const STATUS_ERROR   = 3000;

    /** @var int */
    protected $payloadCode = self::STATUS_SUCCESS;

    /** @var string  */
    protected $payloadErrorDetail = '';

    /**
     * Sets the payload code as Error
     *
     * @param string $detail
     *
     * @return Response
     */
    public function setPayloadError(string $detail = ''): Response
    {
        $this->payloadCode        = self::STATUS_ERROR;
        $this->payloadErrorDetail = $detail;
        $this->setPayloadContent();

        return $this;
    }

    /**
     * Sets the payload code as Error
     *
     * @param null|string|array $content The content
     *
     * @return Response
     */
    public function setPayloadSuccess($content = []): Response
    {
        $this->payloadCode = self::STATUS_SUCCESS;
        $this->setPayloadContent($content);

        return $this;
    }

    /**
     * Sets the API payload to return back. Used instead of the setContent or
     * setJsonContent, so as to provide a uniformed payload no matter what
     * the response is
     *
     * @param null|string|array $content The content
     *
     * @return Response
     */
    public function setPayloadContent($content = []): Response
    {
        $data = (null !== $content)        ? $content : [];
        $data = (true === is_array($data)) ? $data    : [$data];

        parent::setJsonContent($this->processPayload($data));

        $this->setStatusCode(200);
        $this->setContentType('application/vnd.api+json', 'UTF-8');
        $this->setHeader('E-Tag', sha1($this->getContent()));

        return $this;
    }

    /**
     * Returns the response array based in the JSONAPI spec
     *
     * @param array $data
     *
     * @return array
     */
    private function processPayload(array $data): array
    {
        $timestamp = date('c');

        return [
            'jsonapi' => [
                'version' => '1.0',
            ],
            'data'   => $data,
            'errors' => [
                'code'   => $this->payloadCode,
                'detail' => $this->payloadErrorDetail,
            ],
            'meta'   => [
                'timestamp' => $timestamp,
                'hash'      => sha1($timestamp . json_encode($data)),
            ],
        ];
    }
}
