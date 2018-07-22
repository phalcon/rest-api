<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Companies;

use Niden\Api\Controllers\BaseController;
use Niden\Models\Companies;
use Niden\Transformers\CompaniesTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Companies
 */
class GetController extends BaseController
{
    /**
     * Get the company/companies
     *
     * @param int $companyId
     *
     * @return array
     */
    public function callAction($companyId = 0)
    {
        return $this->processCall(
            Companies::class,
            'com_id',
            CompaniesTransformer::class,
            $companyId,
            'com_name'
        );
    }
}
