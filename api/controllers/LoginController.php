<?php

declare(strict_types=1);

namespace Niden\Api\Controllers;

use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\QueryTrait;
use Niden\Traits\TokenTrait;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Filter\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 *
 * @package Niden\Api\Controllers
 *
 * @property Cache $cache
 * @property Config       $config
 * @property Request      $request
 * @property Response     $response
 */
class LoginController extends Controller
{
    use TokenTrait;
    use QueryTrait;

    /**
     * Default action logging in
     *
     * @return array
     * @throws ModelException
     */
    public function callAction()
    {
        $username = $this->request->getPost('username', Filter::FILTER_STRING);
        $password = $this->request->getPost('password', Filter::FILTER_STRING);
        /** @var Users|false $user */
        $user     = $this->getUserByUsernameAndPassword($this->config, $this->cache, $username, $password);

        if (false !== $user) {
            $this
                ->response
                ->setPayloadSuccess(['token' => $user->getToken()]);
        } else {
            $this
                ->response
                ->setPayloadError('Incorrect credentials')
            ;
        }
    }
}
