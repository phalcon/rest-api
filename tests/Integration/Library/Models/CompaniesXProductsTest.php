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
use Phalcon\Api\Models\CompaniesXProducts;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Filter\Filter;

final class CompaniesXProductsTest extends AbstractIntegrationTestCase
{
    public function testValidateFilters(): void
    {
        $model    = new CompaniesXProducts();
        $expected = [
            'companyId' => Filter::FILTER_ABSINT,
            'productId' => Filter::FILTER_ABSINT,
        ];
        $this->assertSame($expected, $model->getModelFilters());
    }
    public function testValidateModel(): void
    {
        $this->haveModelDefinition(
            CompaniesXProducts::class,
            [
                'companyId',
                'productId',
            ]
        );
    }

    public function testValidateRelationships(): void
    {
        $actual   = $this->getModelRelationships(CompaniesXProducts::class);
        $expected = [
            [
                0,
                'companyId',
                Companies::class,
                'id',
                ['alias' => Relationships::COMPANIES, 'reusable' => true],
            ],
            [
                0,
                'productId',
                Products::class,
                'id',
                ['alias' => Relationships::PRODUCTS, 'reusable' => true],
            ],
        ];
        $this->assertSame($expected, $actual);
    }
}
