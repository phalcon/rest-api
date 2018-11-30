<?php
declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Http\Response;
use DateTimeZone;

/**
 * Base controller
 *
 */
class TimeZonesController extends BaseController
{
    /**
     * Index
     *
     * @method GET
     * @url /
     *
     * @return Phalcon\Http\Response
     */
    public function index($id = null) : Response
    {
        return $this->response(DateTimeZone::listIdentifiers());
    }
}
