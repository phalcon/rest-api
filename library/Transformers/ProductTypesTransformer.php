<?php

declare(strict_types=1);

namespace Phalcon\Api\Transformers;

use League\Fractal\Resource\Collection;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\ProductTypes;

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
