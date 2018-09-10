<?php

declare(strict_types=1);

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
     * @param int    $status
     * @param string $message
     *
     * @return mixed
     */
    protected function halt(Micro $api, int $status, string $message)
    {
        $apiResponse = new Response();

        $apiResponse
            ->setPayloadError($message)
            ->setStatusCode($status)
            ->send();

        $api->stop();
    }
}
