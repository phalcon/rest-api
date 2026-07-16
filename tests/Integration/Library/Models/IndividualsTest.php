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
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Filter\Filter;

final class IndividualsTest extends AbstractIntegrationTestCase
{
    public function testValidateFilters(): void
    {
        $model    = new Individuals();
        $expected = [
            'id'        => Filter::FILTER_ABSINT,
            'companyId' => Filter::FILTER_ABSINT,
            'typeId'    => Filter::FILTER_ABSINT,
            'prefix'    => Filter::FILTER_STRING,
            'first'     => Filter::FILTER_STRING,
            'middle'    => Filter::FILTER_STRING,
            'last'      => Filter::FILTER_STRING,
            'suffix'    => Filter::FILTER_STRING,
        ];
        $this->assertSame($expected, $model->getModelFilters());
    }
    public function testValidateModel(): void
    {
        $this->haveModelDefinition(
            Individuals::class,
            [
                'id',
                'companyId',
                'typeId',
                'prefix',
                'first',
                'middle',
                'last',
                'suffix',
            ]
        );
    }

    public function testValidateRelationships(): void
    {
        $actual   = $this->getModelRelationships(Individuals::class);
        $expected = [
            [
                0,
                'companyId',
                Companies::class,
                'id',
                ['alias' => Relationships::COMPANIES, 'reusable' => true],
            ],
            [
                1,
                'typeId',
                IndividualTypes::class,
                'id',
                ['alias' => Relationships::INDIVIDUAL_TYPES, 'reusable' => true],
            ],
        ];
        $this->assertSame($expected, $actual);
    }
}
