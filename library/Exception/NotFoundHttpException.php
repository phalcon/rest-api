<?php

declare(strict_types=1);

namespace Gewaer\Exception;

use Gewaer\Http\Response;

class NotFoundHttpException extends HttpException
{
    protected $httpCode = Response::NOT_FOUND;
    protected $httpMessage = 'Not Found';
    protected $data;
}
