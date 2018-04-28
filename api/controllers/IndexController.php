<?php

namespace Niden\Api\Controllers;

use Phalcon\Mvc\Controller;

/**
 * Class IndexController
 *
 * @package Niden\Api\Controllers
 */
class IndexController extends Controller
{
    /**
     * Default action for integrations
     */
    public function indexAction()
    {
        return 'Phalcon API';
    }

    /**
     * Status page
     */
    public function notfoundAction()
    {
        return 'Not found';
    }
}
