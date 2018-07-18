<?php

declare(strict_types=1);

namespace Niden\Http;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Niden\Transformers\PayloadTransformer;
use Phalcon\Http\Response as PhResponse;
use Phalcon\Mvc\Model\MessageInterface as ModelMessage;
use Phalcon\Validation\Message\Group as ValidationMessage;

class Response extends PhResponse
{
    /**
     * @var array
     */
    private $content = [];

    /**
     * Sets the payload code as Error
     *
     * @param string $detail
     *
     * @return Response
     */
    public function setPayloadError(string $detail = ''): Response
    {
        $this->content['errors'][] = $detail;
        $this->setPayloadContent();

        return $this;
    }

    /**
     * Traverses the errors collection and sets the errors in the payload
     *
     * @param ModelMessage[]|ValidationMessage $errors
     *
     * @return Response
     */
    public function setPayloadErrors($errors): Response
    {
        foreach ($errors as $error) {
            $this->setPayloadError($error->getMessage());
        }
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
        $data = (true === is_array($content)) ? $content : [$content];

        $this->content['data'] = $data;
        $this->setPayloadContent();

        return $this;
    }

    /**
     * Sets the API payload to return back. Used instead of the setContent or
     * setJsonContent, so as to provide a uniformed payload no matter what
     * the response is
     *
     * @return Response
     */
    public function setPayloadContent(): Response
    {
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
        $resource = new Item($this->content, new PayloadTransformer());
        $data     = $manager->createData($resource)->toArray();

        return $data['data'];
    }
}
