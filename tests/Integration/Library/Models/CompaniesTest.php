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
use Phalcon\Api\Models\Products;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Filter\Filter;

use function count;

final class CompaniesTest extends AbstractIntegrationTestCase
{
    public function testValidateModel(): void
    {
        $this->haveModelDefinition(
            Companies::class,
            [
                'id',
                'name',
                'address',
                'city',
                'phone',
            ]
        );
    }

    public function testValidateFilters(): void
    {
        $model    = new Companies();
        $expected = [
            'id'      => Filter::FILTER_ABSINT,
            'name'    => Filter::FILTER_STRING,
            'address' => Filter::FILTER_STRING,
            'city'    => Filter::FILTER_STRING,
            'phone'   => Filter::FILTER_STRING,
        ];
        $this->assertSame($expected, $model->getModelFilters());
    }

    public function testValidateRelationships(): void
    {
        $actual   = $this->getModelRelationships(Companies::class);
        $expected = [
            [
                2,
                'id',
                Individuals::class,
                'companyId',
                ['alias' => Relationships::INDIVIDUALS, 'reusable' => true],
            ],
            [
                4,
                'id',
                Products::class,
                'id',
                ['alias' => Relationships::PRODUCTS, 'reusable' => true],
            ],
        ];

        $this->assertSame($expected, $actual);
    }

    public function testValidateUniqueName(): void
    {
        $companyOne = new Companies();
        /** @var Companies $companyOne */
        $result = $companyOne
            ->set('name', 'acme')
            ->set('address', '123 Phalcon way')
            ->set('city', 'World')
            ->set('phone', '555-999-4444')
            ->save()
        ;
        $this->assertNotEquals(false, $result);

        $companyTwo = new Companies();
        /** @var Companies $companyTwo */
        $result = $companyTwo
            ->set('name', 'acme')
            ->set('address', '123 Phalcon way')
            ->set('city', 'World')
            ->set('phone', '555-999-4444')
            ->save()
        ;
        $this->assertSame(false, $result);
        $this->assertSame(1, count($companyTwo->getMessages()));

        $messages = $companyTwo->getMessages();
        $expected = 'The company name already exists in the database';
        $actual   = $messages[0]->getMessage();
        $this->assertSame($expected, $actual);

        $result = $companyOne->delete();
        $this->assertNotEquals(false, $result);
    }
}
