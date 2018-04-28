<?php

namespace Niden\Providers;

use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Events\Manager;
use Phalcon\Registry;
use Niden\Listeners\Db\QueryListener;
use Niden\Listeners\Dispatcher\NotFoundListener;
use Niden\Listeners\Model\CrudListener;
use Niden\Listeners\Model\ValidationListener;

class EventsProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        /** @var Manager $eventsManager */
        $eventsManager = $container->getShared('eventsManager');
        /** @var Registry $registry */
        $registry      = $container->getShared('registry');

        /**
         * 404
         */
        $eventsManager->attach(
            'dispatch:beforeException',
            new NotFoundListener(),
            200
        );

        $eventsManager->attach(
            'model',
            new CrudListener(),
            190
        );

        $eventsManager->attach(
            'model',
            new ValidationListener(),
            180
        );

        /**
         * Db Query Listener
         */
        if (true === $registry->offsetGet('devMode')) {
            $eventsManager->attach(
                'db',
                new QueryListener(),
                90
            );
        }
    }
}
