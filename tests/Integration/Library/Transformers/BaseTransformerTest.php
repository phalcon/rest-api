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

namespace Phalcon\Api\Tests\Integration\Library\Transformers;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Transformers\BaseTransformer;

final class BaseTransformerTest extends AbstractIntegrationTestCase
{
    /**
     * @throws ModelException
     */
    public function testTransformer(): void
    {
        /** @var Companies $company */
        $company = $this->haveRecordWithFields(
            Companies::class,
            [
                'name'    => 'acme',
                'address' => '123 Phalcon way',
                'city'    => 'World',
                'phone'   => '555-999-4444',
            ]
        );

        $transformer = new BaseTransformer();
        $expected    = [
            'id'      => $company->get('id'),
            'name'    => $company->get('name'),
            'address' => $company->get('address'),
            'city'    => $company->get('city'),
            'phone'   => $company->get('phone'),
        ];

        $this->assertSame($expected, $transformer->transform($company));
    }

    /**
     * Field selection is intersected against the model's public set: a request
     * is never widened to the full set, and never trusted as given. Asking for
     * one public field alongside one that is not public returns only the public
     * one - which pins the coalesce and the intersection down at once, since
     * dropping either would hand back every field or reach for a column the
     * model does not publish.
     *
     * @throws ModelException
     */
    public function testTransformerReturnsOnlyTheRequestedPublicFields(): void
    {
        /** @var Companies $company */
        $company = $this->haveRecordWithFields(
            Companies::class,
            [
                'name'    => 'acme',
                'address' => '123 Phalcon way',
                'city'    => 'World',
                'phone'   => '555-999-4444',
            ]
        );

        $transformer = new BaseTransformer(
            ['companies' => ['name', 'secret']],
            'companies'
        );

        $this->assertSame(
            ['name' => $company->get('name')],
            $transformer->transform($company)
        );
    }
}
