<?php

namespace Niden\Api\Controllers;

use Niden\Http\Response;
use Phalcon\Mvc\Controller;
use function pi;
use function round;

/**
 * Class IndexController
 *
 * @package Niden\Api\Controllers
 *
 * @property Response $response
 */
class IndexController extends Controller
{
    /**
     * Default action for integrations
     */
    public function indexAction()
    {
        /**
         * Send some random text out - why not
         */
        $this->response->setPayloadSuccess((string) round(pi(), 4));
    }
}
