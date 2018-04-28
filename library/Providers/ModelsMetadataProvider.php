<?php

namespace Niden\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Registry;
use function Niden\Web\appPath;

class ModelsMetadataProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $registry = $container->getShared('registry');
        if (true === $registry->offsetGet('devMode')) {
            $adapter = 'Memory';
            $options = [];
        } else {
            $adapter = 'Files';
            $options = [
                'metaDataDir' => appPath('storage/cache/metadata/'),
            ];
        }

        $container->setShared(
            'modelsMetadata',
            function () use ($adapter, $options) {
                $class = sprintf('Phalcon\Mvc\Model\Metadata\%s', $adapter);

                return new $class($options);
            }
        );
    }
}
