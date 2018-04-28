<?php

namespace Retail\Middleware;

use Phalcon\Events\Event as PhEvent;
use Phalcon\Mvc\Micro as PhMicro;
use Phalcon\Mvc\Micro\MiddlewareInterface as PhMiddlewareInterface;

use Common\Exceptions\Exception as CException;
use Common\Mvc\Plugin as CPlugin;

use Retail\Constants\ErrorCodes as RErrorCodes;

class PayloadMiddleware extends CPlugin implements PhMiddlewareInterface
{
    /**
     * @param PhEvent $event
     * @param PhMicro $api
     */
    public function beforeExecuteRoute(PhEvent $event, PhMicro $api)
    {
        json_decode($api->request->getRawBody());
        if (JSON_ERROR_NONE !== json_last_error()) {
            $this
                ->returnResponse(
                    RErrorCodes::CODE_INCORRECT_PAYLOAD,
                    RErrorCodes::STATUS_ERROR,
                    'Malformed JSON'
                );
        }
    }

    /**
     * Call me
     *
     * @param \Phalcon\Mvc\Micro $api
     *
     * @return bool
     */
    public function call(PhMicro $api)
    {
        return true;
    }
}
