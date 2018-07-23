<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\ProductTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Models\ProductTypes;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\ProductTypes
 */
class GetController extends BaseController
{
    /**
     * Get product types
     *
     * @param int $typeId
     *
     * @return array
     */
    public function callAction($typeId = 0)
    {
        return $this->processCall(ProductTypes::class, 'product-types', $typeId, 'name');
    }
}
