<?php

declare(strict_types=1);

namespace Niden\Transformers;

use Niden\Constants\Relationships;
use Niden\Constants\Resources;
use Niden\Models\Products;
use Niden\Models\ProductTypes;

/**
 * Class ProductsTransformer
 */
class ProductsTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Resources::PRODUCT_TYPES,
    ];

    public function includeProductTypes(Products $product)
    {
        /** @var ProductTypes $productType */
        $productType = $product->getRelated(Relationships::PRODUCT_TYPE);

        return $this->item($productType, new ProductTypesTransformer(), Resources::PRODUCT_TYPES);
    }
}
