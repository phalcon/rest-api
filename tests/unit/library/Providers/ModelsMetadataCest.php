<?php

namespace Phalcon\Api\Tests\unit\library\Providers;

use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Model\MetaData\Memory;
use UnitTester;

class ModelsMetadataCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $config      = new ConfigProvider();
        $config->register($diContainer);
        $provider = new ModelsMetadataProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('modelsMetadata'));
        /** @var Memory $metadata */
        $metadata = $diContainer->getShared('modelsMetadata');
        $I->assertTrue($metadata instanceof Memory);
    }
}
