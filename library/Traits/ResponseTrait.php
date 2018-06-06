<?php

namespace Niden\Traits;

use Niden\Http\Response;
use Phalcon\Mvc\Micro;

/**
 * Trait ResponseTrait
 *
 * @package Niden\Traits
 */
trait ResponseTrait
{
    /**
     * Halt execution after setting the message in the response
     *
     * @param Micro  $api
     * @param string $message
     *
     * @return mixed
     */
    protected function halt(Micro $api, string $message)
    {
        /** @var Response $response */
        $response = $api->getService('response');
        $response
            ->setPayloadError($message)
            ->send();

        $api->stop();
    }

    public function process(Micro $api)
    {
        /** @var Response $response */
        $response = $api->getService('response');
        $data     = $api->getReturnedValue();
        $response->setPayloadContent($data);

        if (true !== $response->isSent()) {
            $response->send();
        }
    }
}
