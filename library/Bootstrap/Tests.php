<?php

namespace Gewaer\Bootstrap;

use Dmkit\Phalcon\Auth\Middleware\Micro as AuthMicro;

class Tests extends Api
{
    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        $config = $this->container->getConfig()->jwt->toArray();

        //ignore token validation if disable
        $config['ignoreUri'] = ['regex: *'];

        //JWT Validation
        new AuthMicro($this->application, $config);

        return $this->application;
    }
}
