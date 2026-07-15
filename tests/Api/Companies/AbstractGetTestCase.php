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

use Phalcon\Api\Tests\Api\AbstractApiTestCase;

/**
 * Ported from the Codeception `GetBase` helper.
 */
abstract class AbstractGetTestCase extends AbstractApiTestCase
{
    /**
     * Returns [$company, $prdOne, $prdTwo, $indOne, $indTwo].
     *
     * @return array<int, mixed>
     */
    protected function addRecords(): array
    {
        $company = $this->addCompanyRecord('com-a');
        $indType = $this->addIndividualTypeRecord('type-a-');
        $indOne  = $this->addIndividualRecord('ind-a-', $company->get('id'), $indType->get('id'));
        $indTwo  = $this->addIndividualRecord('ind-a-', $company->get('id'), $indType->get('id'));
        $prdType = $this->addProductTypeRecord('type-a-');
        $prdOne  = $this->addProductRecord('prd-a-', $prdType->get('id'));
        $prdTwo  = $this->addProductRecord('prd-b-', $prdType->get('id'));

        $this->addCompanyXProduct($company->get('id'), $prdOne->get('id'));
        $this->addCompanyXProduct($company->get('id'), $prdTwo->get('id'));

        return [$company, $prdOne, $prdTwo, $indOne, $indTwo];
    }
}
