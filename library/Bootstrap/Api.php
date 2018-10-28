<?php

declare(strict_types=1);

namespace Gewaer\Bootstrap;

use function Gewaer\Core\appPath;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Gewaer\Http\Response;
use Phalcon\Http\Request;
use Throwable;
use Dmkit\Phalcon\Auth\Middleware\Micro as AuthMicro;

/**
 * Class Api
 *
 * @package Gewaer\Bootstrap
 *
 * @property Micro $application
 */
class Api extends AbstractBootstrap
{
    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        try {
            $config = $this->container->getConfig()->jwt->toArray();

            //ignore token validation if disable
            if (!$this->container->getConfig()->application->jwtSecurity) {
                $config['ignoreUri'] = ['regex: *'];
            }

            //JWT Validation
            $auth = new AuthMicro($this->application, $config);

            return $this->application->handle();
        } catch (Throwable $e) {
            $this->handleException($e)->send();
        }
    }

    /**
     * Handle the exception we throw from our api
     *
     * @param Throwable $e
     * @return Response
     */
    public function handleException(Throwable $e): Response
    {
        $response = new Response();
        $request = new Request();
        $identifier = $request->getServerAddress();
        $config = $this->container->getConfig();

        $httpCode = (method_exists($e, 'getHttpCode')) ? $e->getHttpCode() : 400;
        $httpMessage = (method_exists($e, 'getHttpMessage')) ? $e->getHttpMessage() : 'Bad Request';
        $data = (method_exists($e, 'getData')) ? $e->getData() : [];

        $message = $e->getMessage();
        $response->setStatusCode($httpCode, $httpMessage);
        $response->setContentType('application/json');
        $response->setJsonContent([
            'errors' => [
                'type' => 'FAILED',
                'identifier' => $identifier,
                'message' => $e->getMessage(),
                'trace' => strtolower($config->app->env) != 'production' ? $e->getTraceAsString() : null,
                'data' => $data,
            ],
        ]);

        $this->container->getLog()->error($e);

        return $response;
    }

    /**
     * @return mixed
     */
    public function setup()
    {
        //set the default DI
        $this->container = new FactoryDefault();
        //set all the services
        $this->providers = require appPath('api/config/providers.php');

        //run my parents setup
        parent::setup();
    }
}
