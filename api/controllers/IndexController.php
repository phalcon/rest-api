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
        /**
         * Send some random text out - why not
         */
        $digits    = intval(rand(2, 24));
        $precision = ini_get('precision');

        ini_set('precision', $digits);
        $pi = pi();
        ini_set('precision', $precision);

        return $pi;
    }
}
