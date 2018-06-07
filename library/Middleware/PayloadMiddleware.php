<?php

namespace Niden\Middleware;

use Niden\Exception\Exception;
use Niden\Http\Response;
use Niden\Traits\ResponseTrait;
use Phalcon\Events\Event;
use Phalcon\Http\Request;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class PayloadMiddleware
 *
 * @package Niden\Middleware
 *
 * @property Response $response
 */
class PayloadMiddleware implements MiddlewareInterface
{
    use ResponseTrait;

    /**
     * @param Event $event
     * @param Micro $api
     *
     * @return bool
     */
    public function beforeExecuteRoute(/** @scrutinizer ignore-unused */Event $event, Micro $api)
    {
        try {
            /** @var Request $request */
            $request = $api->getService('request');
            if (true === $request->isPost()) {
                $body = $request->getRawBody();
                if (true !== empty($body)) {
                    $data = json_decode($body, true);
                    $this->checkJson();
                    $this->checkDataElement($data);
                    $this->parsePayload($data);
                }
            }

            return true;
        } catch (Exception $ex) {
            $this->halt($api, $ex->getMessage());

            return false;
        }
    }

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     */
    public function call(Micro $api)
    {
        return true;
    }

    /**
     * Checks if the 'data' element has been sent
     *
     * @param array $data
     *
     * @throws Exception
     */
    private function checkDataElement(array $data)
    {
        if (true !== isset($data['data'])) {
            throw new Exception('"data" element not present in the payload');
        }
    }

    /**
     * Check if we have a JSON error
     *
     * @throws Exception
     */
    private function checkJson()
    {
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception('Malformed JSON');
        }
    }

    /**
     * Parses the payload and injects the posted data in the POST array
     *
     * @param array $data
     */
    private function parsePayload(array $data)
    {
        $_POST = $data['data'];
    }
}
