<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Api\Controllers;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Cache\Cache;
use Phalcon\Config\Config;
use Phalcon\Filter\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 *
 * @property Cache    $cache
 * @property Config   $config
 * @property Request  $request
 * @property Response $response
 */
class LoginController extends Controller
{
    use TokenTrait;
    use QueryTrait;

    /**
     * Default action logging in
     *
     * @return void
     * @throws ModelException
     */
    public function callAction()
    {
        $username = $this->request->getPost('username', Filter::FILTER_STRING);
        $password = $this->request->getPost('password', Filter::FILTER_STRING);

        /** @var Users|null $user */
        $user = $this->getUserByUsernameAndPassword(
            $this->config,
            $this->cache,
            $username,
            $password
        );

        if (null !== $user) {
            $this
                ->response
                ->setPayloadSuccess(['token' => $user->getToken()])
            ;
        } else {
            $this
                ->response
                ->setPayloadError('Incorrect credentials')
            ;
        }
    }
}
