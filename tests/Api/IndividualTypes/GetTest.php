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

namespace Phalcon\Api\Tests\Api\IndividualTypes;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Tests\Api\AbstractApiTestCase;
use Phalcon\Api\Tests\Support\Data;

use function Phalcon\Api\Core\envValue;
use function sprintf;

final class GetTest extends AbstractApiTestCase
{
    public function testGetIndividualTypes(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $typeOne = $this->addIndividualTypeRecord('type-a-');
        $typeTwo = $this->addIndividualTypeRecord('type-b-');

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$individualTypesUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                Data::individualTypeResponse($typeOne),
                Data::individualTypeResponse($typeTwo),
            ]
        );
    }

    public function testGetIndividualTypesNoData(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(Data::$individualTypesUrl);
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse();
    }

    public function testGetIndividualTypesWithIncludesIndividuals(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $company        = $this->addCompanyRecord('com-a');
        $individualType = $this->addIndividualTypeRecord('type-a-');
        $individualOne  = $this->addIndividualRecord('prd-a-', $company->get('id'), $individualType->get('id'));
        $individualTwo  = $this->addIndividualRecord('prd-b-', $company->get('id'), $individualType->get('id'));

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(
            sprintf(
                Data::$individualTypesRecordIncludesUrl,
                $individualType->get('id'),
                Relationships::INDIVIDUALS
            )
        );
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIsSuccessful();
        $this->assertSuccessJsonResponse(
            'data',
            [
                [
                    'type'          => Relationships::INDIVIDUAL_TYPES,
                    'id'            => $individualType->get('id'),
                    'attributes'    => [
                        'name'        => $individualType->get('name'),
                        'description' => $individualType->get('description'),
                    ],
                    'links'         => [
                        'self' => sprintf(
                            '%s/%s/%s',
                            envValue('APP_URL'),
                            Relationships::INDIVIDUAL_TYPES,
                            $individualType->get('id')
                        ),
                    ],
                    'relationships' => [
                        Relationships::INDIVIDUALS => [
                            'links' => [
                                'self'    => sprintf(
                                    '%s/%s/%s/relationships/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUAL_TYPES,
                                    $individualType->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                                'related' => sprintf(
                                    '%s/%s/%s/%s',
                                    envValue('APP_URL'),
                                    Relationships::INDIVIDUAL_TYPES,
                                    $individualType->get('id'),
                                    Relationships::INDIVIDUALS
                                ),
                            ],
                            'data'  => [
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $individualOne->get('id'),
                                ],
                                [
                                    'type' => Relationships::INDIVIDUALS,
                                    'id'   => $individualTwo->get('id'),
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->assertSuccessJsonResponse(
            'included',
            [
                Data::individualResponse($individualOne),
                Data::individualResponse($individualTwo),
            ]
        );
    }

    public function testGetUnknownIndividualTypes(): void
    {
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $this->sendGet(sprintf(Data::$individualTypesRecordUrl, 1));
        $this->unsetHttpHeader('Authorization');
        $this->assertResponseIs404();
    }
}
