<?php

declare(strict_types=1);

namespace Niden\Http;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Niden\Transformers\PayloadTransformer;
use Phalcon\Http\Response as PhResponse;

class Response extends PhResponse
{
    /** @var int */
    const STATUS_SUCCESS = 2000;

    /** @var int */
    const STATUS_ERROR   = 3000;

    /** @var array */
    protected $data = [
        'code'   => self::STATUS_SUCCESS,
        'detail' => '',
    ];

    /**
     * Sets the payload code as Error
     *
     * @param string $detail
     *
     * @return Response
     */
    public function setPayloadError(string $detail = ''): Response
    {
        $this->data = [
            'code'   => self::STATUS_ERROR,
            'detail' => $detail,
        ];
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
        $this->data['code'] = self::STATUS_SUCCESS;
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
        $data = (true === is_array($content)) ? $content : [$content];

        $this->data['data'] = $data;

        parent::setJsonContent($this->processPayload());

        $this->setStatusCode(200);
        $this->setContentType('application/vnd.api+json', 'UTF-8');
        $this->setHeader('E-Tag', sha1($this->getContent()));

        return $this;
    }

    /**
     * Returns the response array based in the JSONAPI spec
     *
     * @return array
     */
    private function processPayload(): array
    {
        $manager  = new Manager();
        $resource = new Item($this->data, new PayloadTransformer());
        $data     = $manager->createData($resource)->toArray();

        return $data['data'];
    }
}
