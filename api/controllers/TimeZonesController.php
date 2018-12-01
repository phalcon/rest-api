<?php
declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Http\Response;
use DateTimeZone;

/**
 * Class TimeZonesController
 *
 * @package Gewaer\Api\Controllers
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
     * @return Response
     */
    public function index($id = null) : Response
    {
        return $this->response(DateTimeZone::listIdentifiers());
    }
}
