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
    const OK                    = 200;
    const MOVED_PERMANENTLY     = 301;
    const FOUND                 = 302;
    const TEMPORARY_REDIRECT    = 307;
    const PERMANENTLY_REDIRECT  = 308;
    const BAD_REQUEST           = 400;
    const UNAUTHORIZED          = 401;
    const FORBIDDEN             = 403;
    const NOT_FOUND             = 404;
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED       = 501;
    const BAD_GATEWAY           = 502;

    private $codes = [
        200 => 'OK',
        301 => 'Moved Permanently',
        302 => 'Found',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
    ];

    /**
     * @var array
     */
    private $content = [];

    /**
     * Returns the http code description or if not found the code itself
     * @param int $code
     *
     * @return int|string
     */
    public function getHttpCodeDescription(int $code)
    {
        if (true === isset($this->codes[$code])) {
            return sprintf('%d (%s)', $code, $this->codes[$code]);
        }

        return $code;
    }

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
        $data = (true === is_array($content)) ? $content : ['data' => $content];
        $data = (true === isset($data['data'])) ? $data  : ['data' => $data];

        $this->content = $data;
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
