<?php

declare(strict_types=1);

namespace Gewaer\Exception;

class ModelException extends Exception
{
    protected $httpCode = Response::NOT_ACCEPTABLE;
    protected $httpMessage = 'Not Acceptable';
    protected $data;
}
