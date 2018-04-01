<?php


class NotFoundCest
{
    public function checkNotFoundRoute(ApiTester $I)
    {
        $I->sendGET('/something');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('Route not found');
    }
}
