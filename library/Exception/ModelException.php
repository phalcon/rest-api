<?php

declare(strict_types=1);

namespace Gewaer\Exception;

use Gewaer\Http\Response;

class ModelException extends Exception
{
    protected $httpCode = Response::NOT_ACCEPTABLE;
    protected $httpMessage = 'Not Acceptable';
    protected $data;
}
