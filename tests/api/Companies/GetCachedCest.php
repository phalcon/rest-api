<?php

namespace Niden\Tests\api\Companies;

use ApiTester;
use function copy;
use Niden\Constants\Relationships;
use function Niden\Core\appPath;
use Niden\Models\Companies;
use Page\Data;
use Phalcon\Config;
use function sprintf;
use function uniqid;

class GetCachedCest
{
    public function _before(ApiTester $I)
    {
        /**
         * Manipulate the .env file to simulate production
         */
        $result = copy(appPath('./storage/ci/.env.prod'), appPath('./.env'));
        $I->assertTrue($result);

    }

    public function _after(ApiTester $I)
    {
        /**
         * Manipulate the .env file to simulate production
         */
        $result = copy(appPath('./storage/ci/.env.example'), appPath('./.env'));
        $I->assertTrue($result);
    }

    public function getCompaniesWithCache(ApiTester $I)
    {
        /** @var Config $config */
        $config = $I->grabFromDi('config');
        $I->assertFalse($config->path('app.devMode'));

        /**
         * Login
         */
        $I->addApiUserRecord();
        $token = $I->apiLogin();

        /**
         * Company 1
         */
        $comName = uniqid('com-cached-');
        $comOne  = $I->haveRecordWithFields(
            Companies::class,
            [
                'name'    => $comName,
                'address' => uniqid(),
                'city'    => uniqid(),
                'phone'   => uniqid(),
            ]
        );

        /**
         * Get the company - should see "com-cached" -> save to cache
         */
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordUrl, $comOne->get('id')));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $comOne->get('id'),
                    'type'       => Relationships::COMPANIES,
                    'attributes' => [
                        'name'    => $comName,
                        'address' => $comOne->get('address'),
                        'city'    => $comOne->get('city'),
                        'phone'   => $comOne->get('phone'),
                    ],
                ],
            ]
        );

        /**
         * Get the company again  - should see "com-cached" -> should get cached
         */
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET(sprintf(Data::$companiesRecordUrl, $comOne->get('id')));
        $I->deleteHeader('Authorization');
        $I->seeResponseIsSuccessful();
        $I->seeSuccessJsonResponse(
            'data',
            [
                [
                    'id'         => $comOne->get('id'),
                    'type'       => Relationships::COMPANIES,
                    'attributes' => [
                        'name'    => $comName,
                        'address' => $comOne->get('address'),
                        'city'    => $comOne->get('city'),
                        'phone'   => $comOne->get('phone'),
                    ],
                ],
            ]
        );

        /**
         * Change the name in the database
         */
        $result = $comOne->set('name', 'no-cache-name')->save();
        $I->assertNotEquals(false, $result);

//        /**
//         * Call the get again and check if we get cached data or not
//         */
//        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
//        $I->sendGET(sprintf(Data::$companiesRecordUrl, $comOne->get('id')));
//        $I->deleteHeader('Authorization');
//        $I->seeResponseIsSuccessful();
//        $I->seeSuccessJsonResponse(
//            'data',
//            [
//                [
//                    'id'         => $comOne->get('id'),
//                    'type'       => Relationships::COMPANIES,
//                    'attributes' => [
//                        'name'    => $comName,
//                        'address' => $comOne->get('address'),
//                        'city'    => $comOne->get('city'),
//                        'phone'   => $comOne->get('phone'),
//                    ],
//                ],
//            ]
//        );
    }
}

