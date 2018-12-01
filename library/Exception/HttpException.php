<?php

declare(strict_types=1);

namespace Gewaer\Exception;

use Gewaer\Http\Response;

class HttpException extends Exception
{
    protected $httpCode = Response::BAD_REQUEST;
    protected $httpMessage = 'Bad Request';
    protected $data;
}
