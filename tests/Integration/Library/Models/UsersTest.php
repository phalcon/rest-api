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

use Phalcon\Api\Models\Users;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Filter\Filter;

use function count;

/**
 * Issuing and validating tokens moved to TokenService; those tests live in
 * Services/TokenServiceTest.
 */
final class UsersTest extends AbstractIntegrationTestCase
{
    /**
     * Guards the field whitelist at the model, where it is declared. The api
     * suite asserts JSON subsets, so it cannot prove absence on its own.
     */
    public function testGetPublicFields(): void
    {
        $expected = [
            'id',
            'status',
            'username',
            'issuer',
            'tokenId',
        ];

        $actual = (new Users())->getPublicFields();

        $this->assertSame($expected, $actual);
        $this->assertNotContains('password', $actual);
        $this->assertNotContains('tokenPassword', $actual);
    }

    public function testValidateFilters(): void
    {
        $model    = new Users();
        $expected = [
            'id'            => Filter::FILTER_ABSINT,
            'status'        => Filter::FILTER_ABSINT,
            'username'      => Filter::FILTER_STRING,
            'password'      => Filter::FILTER_STRING,
            'issuer'        => Filter::FILTER_STRING,
            'tokenPassword' => Filter::FILTER_STRING,
            'tokenId'       => Filter::FILTER_STRING,
        ];
        $this->assertSame($expected, $model->getModelFilters());
    }

    public function testValidateModel(): void
    {
        $this->haveModelDefinition(
            Users::class,
            [
                'id',
                'status',
                'username',
                'password',
                'issuer',
                'tokenPassword',
                'tokenId',
            ]
        );
    }

    public function testValidateRelationships(): void
    {
        $actual = $this->getModelRelationships(Users::class);
        $this->assertSame(0, count($actual));
    }
}
