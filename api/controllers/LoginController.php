<?php

namespace Niden\Api\Controllers;

use function explode;
use Lcobucci\JWT\Builder;
use Niden\Exception\ModelException;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\UserTrait;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 *
 * @package Niden\Api\Controllers
 *
 * @property Response $response
 */
class LoginController extends Controller
{
    use UserTrait;

    /**
     * Default action for integrations
     */
    /**
     * @return array
     * @throws ModelException
     */
    public function indexAction()
    {
        $username = $this->request->getPost('username', Filter::FILTER_STRING);
        $password = $this->request->getPost('password', Filter::FILTER_STRING);

        $user = $this->getUserByUsernameAndPassword(
            $username,
            $password,
            'Incorrect credentials'
        );

        $token = $this->getToken($user);

        $this->updateRecord($user, $token);

        /**
         * User found - Return token
         */
        $this->response->setPayloadSuccess(['token' => $token]);
    }

    /**
     * @param Users $user
     *
     * @return string
     * @throws ModelException
     */
    private function getToken(Users $user): string
    {
        $builder = new Builder();
        $token   = $builder
            ->setIssuer('http://phalconphp.com')
            ->setAudience($user->get('usr_domain_name'))
            ->setId($user->get('usr_token_id'), true)
            ->setIssuedAt(time())
            ->setNotBefore(time() + 60)
            ->setExpiration(time() + 3600)
            ->getToken();

        return $token->__toString();
    }

    /**
     * @param Users  $user
     * @param string $token
     *
     * @throws ModelException
     */
    private function updateRecord(Users $user, string $token)
    {
        list($pre, $mid, $post) = explode('.', $token);
        $result = $user
            ->set('usr_token_pre', $pre)
            ->set('usr_token_mid', $mid)
            ->set('usr_token_post', $post)
            ->save();
        $this->checkResult($result, 'Cannot update user record');
    }
}
