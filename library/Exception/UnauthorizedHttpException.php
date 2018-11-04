<?php

declare(strict_types=1);

namespace Gewaer\Exception;

use Gewaer\Http\Response;

class UnauthorizedHttpException extends HttpException
{
    protected $httpCode = Response::UNAUTHORIZED;
    protected $httpMessage = 'Unauthorized';
    protected $data;
}
