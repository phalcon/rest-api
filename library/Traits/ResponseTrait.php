<?php

declare(strict_types=1);

namespace Phalcon\Api\Traits;

use Phalcon\Api\Http\Response;
use Phalcon\Mvc\Micro;

/**
 * Trait ResponseTrait
 */
trait ResponseTrait
{
    /**
     * Halt execution after setting the message in the response
     *
     * @param Micro  $api
     * @param int    $status
     * @param string $message
     *
     * @return mixed
     */
    protected function halt(Micro $api, int $status, string $message)
    {
        /** @var Response $response */
        $response = $api->getService('response');
        $response
            ->setPayloadError($message)
            ->setStatusCode($status)
            ->send();

        $api->stop();
    }
}
