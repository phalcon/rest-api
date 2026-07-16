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
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Filter\Filter;

final class ProductsTest extends AbstractIntegrationTestCase
{
    public function testValidateModel(): void
    {
        $this->haveModelDefinition(
            Products::class,
            [
                'id',
                'typeId',
                'name',
                'description',
                'quantity',
                'price',
            ]
        );
    }

    public function testValidateFilters(): void
    {
        $model    = new Products();
        $expected = [
            'id'          => Filter::FILTER_ABSINT,
            'typeId'      => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
            'quantity'    => Filter::FILTER_ABSINT,
            'price'       => Filter::FILTER_FLOAT,
        ];
        $this->assertSame($expected, $model->getModelFilters());
    }

    public function testValidateRelationships(): void
    {
        $actual   = $this->getModelRelationships(Products::class);
        $expected = [
            [
                0,
                'typeId',
                ProductTypes::class,
                'id',
                ['alias' => Relationships::PRODUCT_TYPES, 'reusable' => true],
            ],
            [
                4,
                'id',
                Companies::class,
                'id',
                ['alias' => Relationships::COMPANIES, 'reusable' => true],
            ],
        ];

        $this->assertSame($expected, $actual);
    }
}
