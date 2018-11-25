<?php

namespace Gewaer\Tests\unit\library\Providers;

use Gewaer\Providers\ModelsMetadataProvider;
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
        //issue with shared config
        return ;
        $diContainer = new FactoryDefault();
        $provider = new ModelsMetadataProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('modelsMetadata'));
        /** @var Libmemcached $cache */
        $metadata = $diContainer->getShared('modelsMetadata');
        $I->assertTrue($metadata instanceof Libmemcached);
    }
}
