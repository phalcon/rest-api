<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\IndividualTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Models\IndividualTypes;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\IndividualTypes
 */
class GetController extends BaseController
{
    /**
     * Get the individual types
     *
     * @param int $typeId
     *
     * @return array
     */
    public function callAction($typeId = 0)
    {
        return $this->processCall(IndividualTypes::class, 'individual-types', $typeId, 'name');
    }
}
