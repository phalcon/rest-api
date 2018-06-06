<?php

namespace Niden\Api\Controllers;

use function explode;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Niden\Exception\Exception;
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
     * Default action logging in
     *
     * @throws ModelException
     */
    public function callAction()
    {
        try {
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
            return ['token' => $token];
        } catch (Exception $ex) {
            $this->response->setPayloadError($ex->getMessage());
        }
    }

    /**
     * @param Users $user
     *
     * @return string
     * @throws ModelException
     */
    private function getToken(Users $user): string
    {
        $signer  = new Sha512();
        $builder = new Builder();

        $token   = $builder
            ->setIssuer($user->get('usr_domain_name'))
            ->setAudience('https://phalconphp.com')
            ->setId($user->get('usr_token_id'), true)
            ->setIssuedAt(time())
            ->setNotBefore(time() + 10)
            ->setExpiration(time() + 3600)
            ->sign($signer, $user->get('usr_token_password'))
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

        if (false === $result) {
            throw new ModelException('Cannot update user record');
        }
    }
}
