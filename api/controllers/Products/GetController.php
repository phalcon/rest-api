<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\Products;

use Niden\Api\Controllers\BaseController;
use Niden\Models\Products;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\Products
 */
class GetController extends BaseController
{
    /**
     * Get products
     *
     * @param int $productId
     *
     * @return array
     */
    public function callAction($productId = 0)
    {
        return $this->processCall(Products::class, 'products', $productId, 'name');
    }
}
