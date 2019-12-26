<?php

namespace Phalcon\Api\Tests\unit\library\Providers;

use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Model\MetaData\Libmemcached;
use UnitTester;

class ModelsMetadataCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new ModelsMetadataProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('modelsMetadata'));
        /** @var Libmemcached $cache */
        $metadata = $diContainer->getShared('modelsMetadata');
        $I->assertTrue($metadata instanceof Libmemcached);
    }
}
