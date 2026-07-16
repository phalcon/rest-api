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

namespace Phalcon\Api\Tests\Api\Companies;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Tests\Support\Data;

use function count;
use function implode;
use function Phalcon\Api\Core\envValue;
use function sprintf;

final class GetIncludesTest extends AbstractGetTestCase
{
    public function testGetCompaniesWithIncludesAllIncludes(): void
    {
        $this->checkIncludes([Relationships::INDIVIDUALS, Relationships::PRODUCTS]);
    }

    public function testGetCompaniesWithIncludesIndividuals(): void
    {
        $this->checkIncludes([Relationships::INDIVIDUALS]);
    }

    public function testGetCompaniesWithIncludesProducts(): void
    {
        $this->checkIncludes([Relationships::PRODUCTS]);
    }

    public function testGetCompanyUnknownInclude(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $company = $this->addCompanyRecord('com-a-');

        $this->sendGetAs($token, sprintf(Data::$companiesRecordIncludesUrl, $company->get('id'), 'unknown'));
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($company),
            ]
        );
    }

    /**
     * @param array<int, string> $includes
     */
    private function checkIncludes(array $includes = []): void
    {
        [$com, $prdOne, $prdTwo, $indOne, $indTwo] = $this->addRecords();

        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->sendGetAs(
            $token,
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                implode(',', $includes)
            )
        );
        $this->assertResponseIsSuccessful();

        $element = [
            'type'       => Relationships::COMPANIES,
            'id'         => (string) $com->get('id'),
            'attributes' => [
                'name'    => $com->get('name'),
                'address' => $com->get('address'),
                'city'    => $com->get('city'),
                'phone'   => $com->get('phone'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL', 'localhost'),
                    Relationships::COMPANIES,
                    $com->get('id')
                ),
            ],
        ];

        $included = [];
        foreach ($includes as $include) {
            if (Relationships::INDIVIDUALS === $include) {
                $element['relationships'][Relationships::INDIVIDUALS] = [
                    'links' => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::COMPANIES,
                            $com->get('id'),
                            Relationships::INDIVIDUALS
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::COMPANIES,
                            $com->get('id'),
                            Relationships::INDIVIDUALS
                        ),
                    ],
                    'data'  => [
                        [
                            'type' => Relationships::INDIVIDUALS,
                            'id'   => (string) $indOne->get('id'),
                        ],
                        [
                            'type' => Relationships::INDIVIDUALS,
                            'id'   => (string) $indTwo->get('id'),
                        ],
                    ],
                ];

                $included[] = Data::individualResponse($indOne);
                $included[] = Data::individualResponse($indTwo);
            }

            if (Relationships::PRODUCTS === $include) {
                $element['relationships'][Relationships::PRODUCTS] = [
                    'links' => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::COMPANIES,
                            $com->get('id'),
                            Relationships::PRODUCTS
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::COMPANIES,
                            $com->get('id'),
                            Relationships::PRODUCTS
                        ),
                    ],
                    'data'  => [
                        [
                            'type' => Relationships::PRODUCTS,
                            'id'   => (string) $prdOne->get('id'),
                        ],
                        [
                            'type' => Relationships::PRODUCTS,
                            'id'   => (string) $prdTwo->get('id'),
                        ],
                    ],
                ];

                $included[] = Data::productResponse($prdOne);
                $included[] = Data::productResponse($prdTwo);
            }
        }

        $this->assertSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $this->assertSuccessJsonResponse('included', $included);
        }
    }
}
