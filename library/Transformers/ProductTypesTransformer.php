<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\Resource\Collection;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Products;
use Niden\Models\ProductTypes;

/**
 * Class ProductTypesTransformer
 */
class ProductTypesTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::PRODUCTS,
    ];

    /**
     * @param ProductTypes $type
     *
     * @return Collection
     */
    public function includeProducts(ProductTypes $type)
    {
        /** @var Products $products */
        $products = $type->getRelated(Relationships::PRODUCTS);

        return $this->collection($products, new ProductsTransformer(), Relationships::PRODUCTS);
    }
}
