<?php

namespace Niden\Tests\integration\library\Models;

use IntegrationTester;
use function Niden\Core\appPath;
use Niden\Models\Companies;
use Niden\Mvc\Model\AbstractModel;
use Niden\Traits\TokenTrait;
use Phalcon\Filter;
use function str_replace;

class PrefixesCest
{
    public function validatePrefixes(IntegrationTester $I)
    {
        $modelsPath = appPath('/library/Models');
        $prefixes   = [];
        $count      = 0;
        foreach (glob($modelsPath . '/*.php') as $filename) {
            $modelName = str_replace([$modelsPath . '/', '.php'], ['', ''], $filename);
            $modelName = sprintf('Niden\Models\%s', $modelName);
            /** @var AbstractModel $model */
            $model = new $modelName();
            $prefixes[] = $model->getTablePrefix();
            $count++;
        }

        $I->assertEquals($count, count($prefixes));
    }
}
