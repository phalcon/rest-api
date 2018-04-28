<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\Url;

class UrlProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Config $config */
        $config = $container->getShared('config');
        /** @var Url $url */
        $url = $container->getShared('url');

        $url->setBasePath($config->path('app.baseUri', '/'));
    }
}
