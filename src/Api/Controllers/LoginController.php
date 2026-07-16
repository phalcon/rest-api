<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Api\Controllers;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Repositories\UsersRepository;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Filter\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 *
 * @property Request         $request
 * @property Response        $response
 * @property UsersRepository $usersRepository
 */
class LoginController extends Controller
{
    use TokenTrait;

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
        $user = $this->usersRepository->getByUsernameAndPassword(
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
