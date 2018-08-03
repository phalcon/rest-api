<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\Resource\Collection;
use Niden\Constants\Relationships;
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
        return $this->getRelatedData(
            'collection',
            $type,
            ProductsTransformer::class,
            Relationships::PRODUCTS
        );
    }
}
