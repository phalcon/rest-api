<?php

namespace Niden\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Session\Adapter\Files;
use const PHP_SESSION_ACTIVE;
use function getenv;
use function session_start;
use function session_status;

class SessionProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
//        $session = new Files(
//            [
//                'uniqueId'   => getenv('APP_ENV') . '-tdm',
//                'prefix'     => 'tdm_',
//                'persistent' => true,
//            ]
//        );
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start(
                [
                    'save_path'    => '/tmp',
                    'name'         => getenv('APP_ENV') . '-tdm',
                    'save_handler' => 'files',
                    'cookie_path'  => '/',
                ]
            );
        }

//        $container->setShared('session', $session);
    }
}
