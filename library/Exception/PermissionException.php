<?php

declare(strict_types=1);

namespace Gewaer\Exception;

use Gewaer\Http\Response;

class PermissionException extends Exception
{
    protected $httpCode = Response::UNAUTHORIZED;
    protected $httpMessage = 'Unauthorized';
    protected $data;
}
