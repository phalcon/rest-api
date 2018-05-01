<?php

namespace Niden\Middleware;

use Niden\Http\Response;
use Phalcon\Events\Event;
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
    /**
     * @param Event $event
     * @param Micro $api
     *
     * @return bool
     */
    public function beforeExecuteRoute(Event $event, Micro $api)
    {
        if (true === $api->request->isPost()) {
            json_decode($api->request->getRawBody());
            if (JSON_ERROR_NONE !== json_last_error()) {
                /** @var Response $response */
                $response = $api->response;
                $response
                    ->setPayloadStatusError()
                    ->setErrorSource($event->getType())
                    ->setErrorDetail('Malformed JSON')
                    ->setPayloadContent()
                    ->send()
                ;

                return false;
            }
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
}
