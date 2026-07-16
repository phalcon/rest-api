<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Integration\Library\Models;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Filter\Filter;

final class IndividualTypesTest extends AbstractIntegrationTestCase
{
    public function testValidateModel(): void
    {
        $this->haveModelDefinition(
            IndividualTypes::class,
            [
                'id',
                'name',
                'description',
            ]
        );
    }

    public function testValidateFilters(): void
    {
        $model    = new IndividualTypes();
        $expected = [
            'id'          => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
        ];
        $this->assertSame($expected, $model->getModelFilters());
    }

    public function testValidateRelationships(): void
    {
        $actual   = $this->getModelRelationships(IndividualTypes::class);
        $expected = [
            [
                2,
                'id',
                Individuals::class,
                'typeId',
                ['alias' => Relationships::INDIVIDUALS, 'reusable' => true],
            ],
        ];
        $this->assertSame($expected, $actual);
    }
}
