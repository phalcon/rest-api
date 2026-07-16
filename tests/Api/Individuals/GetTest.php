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

namespace Phalcon\Api\Tests\Api\Individuals;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Tests\Api\AbstractApiTestCase;
use Phalcon\Api\Tests\Support\Data;

use function count;
use function implode;
use function Phalcon\Api\Core\envValue;
use function sprintf;

final class GetTest extends AbstractApiTestCase
{
    public function testGetIndividual(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $company        = $this->addCompanyRecord('com-a-');
        $individualType = $this->addIndividualTypeRecord('prt-a-');
        $individual     = $this->addIndividualRecord('prd-a-', $company->get('id'), $individualType->get('id'));

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$individualsRecordUrl, $individual->get('id')));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::individualResponse($individual),
            ]
        );
    }

    public function testGetIndividuals(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $company        = $this->addCompanyRecord('com-a-');
        $individualType = $this->addIndividualTypeRecord('prt-a-');
        $individualOne  = $this->addIndividualRecord('ind-a-', $company->get('id'), $individualType->get('id'));
        $individualTwo  = $this->addIndividualRecord('ind-b-', $company->get('id'), $individualType->get('id'));

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$individualsUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::individualResponse($individualOne),
                Data::individualResponse($individualTwo),
            ]
        );
    }

    public function testGetIndividualsNoData(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$individualsUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse();
    }

    public function testGetIndividualsWithIncludesAllIncludes(): void
    {
        $this->checkIncludes([Relationships::COMPANIES, Relationships::INDIVIDUAL_TYPES]);
    }

    public function testGetIndividualsWithIncludesCompanies(): void
    {
        $this->checkIncludes([Relationships::COMPANIES]);
    }

    public function testGetIndividualsWithRelationshipIndividualTypes(): void
    {
        $this->checkIncludes([Relationships::INDIVIDUAL_TYPES]);
    }

    public function testGetUnknownIndividual(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$individualsRecordUrl, 1));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIs404();
    }

    /**
     * Returns [$individual, $individualType, $company].
     *
     * @return array<int, mixed>
     */
    private function addRecords(): array
    {
        $company        = $this->addCompanyRecord('com-a');
        $individualType = $this->addIndividualTypeRecord('prt-a-');
        $individual     = $this->addIndividualRecord('ind-a-', $company->get('id'), $individualType->get('id'));

        return [$individual, $individualType, $company];
    }

    /**
     * @param array<int, string> $includes
     */
    private function checkIncludes(array $includes = []): void
    {
        [$individual, $individualType, $company] = $this->addRecords();

        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(
            sprintf(
                Data::$individualsRecordIncludesUrl,
                $individual->get('id'),
                implode(',', $includes)
            )
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();

        $element = [
            'type'       => Relationships::INDIVIDUALS,
            'id'         => (string) $individual->get('id'),
            'attributes' => [
                'companyId' => $individual->get('companyId'),
                'typeId'    => $individual->get('typeId'),
                'prefix'    => $individual->get('prefix'),
                'first'     => $individual->get('first'),
                'middle'    => $individual->get('middle'),
                'last'      => $individual->get('last'),
                'suffix'    => $individual->get('suffix'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL', 'localhost'),
                    Relationships::INDIVIDUALS,
                    $individual->get('id')
                ),
            ],
        ];

        $included = [];
        foreach ($includes as $include) {
            if (Relationships::COMPANIES === $include) {
                $element['relationships'][Relationships::COMPANIES] = [
                    'links' => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id'),
                            Relationships::COMPANIES
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id'),
                            Relationships::COMPANIES
                        ),
                    ],
                    'data'  => [
                        'type' => Relationships::COMPANIES,
                        'id'   => (string) $company->get('id'),
                    ],
                ];

                $included[] = Data::companiesResponse($company);
            }

            if (Relationships::INDIVIDUAL_TYPES === $include) {
                $element['relationships'][Relationships::INDIVIDUAL_TYPES] = [
                    'links' => [
                        'self'    => sprintf(
                            '%s/%s/%s/relationships/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id'),
                            Relationships::INDIVIDUAL_TYPES
                        ),
                        'related' => sprintf(
                            '%s/%s/%s/%s',
                            envValue('APP_URL', 'localhost'),
                            Relationships::INDIVIDUALS,
                            $individual->get('id'),
                            Relationships::INDIVIDUAL_TYPES
                        ),
                    ],
                    'data'  => [
                        'type' => Relationships::INDIVIDUAL_TYPES,
                        'id'   => (string) $individualType->get('id'),
                    ],
                ];

                $included[] = Data::individualTypeResponse($individualType);
            }
        }

        $this->assertSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $this->assertSuccessJsonResponse('included', $included);
        }
    }
}
