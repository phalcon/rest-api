<?php

declare(strict_types=1);

namespace Gewaer\Exception;

use Gewaer\Http\Response;

/**
 * Using this exception for critical erros taht you need to get notify ASAP
 */
class ServerErrorHttpException extends HttpException
{
    protected $httpCode = Response::INTERNAL_SERVER_ERROR;
    protected $httpMessage = 'Internal Server Error';
    protected $data;
}
