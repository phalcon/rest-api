<?php

namespace Gewaer\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Baka\Mail\Manager as BakaMail;

class MailProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $config = $container->getShared('config');

        $container->setShared(
            'mail',
            function () use ($config) {
                $mailer = new BakaMail($config->email->toArray());
                return $mailer->createMessage();
            }
        );
    }
}
