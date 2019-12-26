<?php

namespace Phalcon\Api\Tests\api\Companies;

use ApiTester;
use Phalcon\Api\Constants\Relationships;
use Page\Data;
use function Phalcon\Api\Core\envValue;

class GetIncludesCest extends GetBase
{
    /**
     * @param ApiTester $I
     */
    public function getCompanyUnknownInclude(ApiTester $I)
    {
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $company = $I->addCompanyRecord('com-a-');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordIncludesUrl, $company->get('id'), 'unknown'));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                Data::companiesResponse($company),
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithIncludesAllIncludes(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::INDIVIDUALS, Relationships::PRODUCTS]);
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithIncludesIndividuals(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::INDIVIDUALS]);
    }

    /**
     * @param ApiTester $I
     */
    public function getCompaniesWithIncludesProducts(ApiTester $I)
    {
        $this->checkIncludes($I, [Relationships::PRODUCTS]);
    }

    private function checkIncludes(ApiTester $I, array $includes = [])
    {
        list($com, $prdOne, $prdTwo, $indOne, $indTwo) = $this->addRecords($I);

        $I->addApiUserRecord();
        $token = $I->apiLogin();

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                implode(',', $includes)
            )
        );

        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();

        $element = [
            'type'          => Relationships::COMPANIES,
            'id'            => $com->get('id'),
            'attributes'    => [
                'name'    => $com->get('name'),
                'address' => $com->get('address'),
                'city'    => $com->get('city'),
                'phone'   => $com->get('phone'),
            ],
            'links'         => [
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
                            'id'   => $indOne->get('id'),
                        ],
                        [
                            'type' => Relationships::INDIVIDUALS,
                            'id'   => $indTwo->get('id'),
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
                            'id'   => $prdOne->get('id'),
                        ],
                        [
                            'type' => Relationships::PRODUCTS,
                            'id'   => $prdTwo->get('id'),
                        ],
                    ],
                ];

                $included[] = Data::productResponse($prdOne);
                $included[] = Data::productResponse($prdTwo);
            }
        }

        $I->seeSuccessJsonResponse('data', [$element]);

        if (count($included) > 0) {
            $I->seeSuccessJsonResponse('included', $included);
        }
    }
}
