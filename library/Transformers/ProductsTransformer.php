<?php

declare(strict_types=1);

namespace Phalcon\Api\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Niden\Constants\Relationships;
use Niden\Models\Products;

/**
 * Class ProductsTransformer
 */
class ProductsTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::COMPANIES,
        Relationships::PRODUCT_TYPES,
    ];

    /**
     * Includes the companies
     *
     * @param Products $product
     *
     * @return Collection
     */
    public function includeCompanies(Products $product)
    {
        return $this->getRelatedData(
            'collection',
            $product,
            CompaniesTransformer::class,
            Relationships::COMPANIES
        );
    }

    /**
     * Includes the product types
     *
     * @param Products $product
     *
     * @return Item
     */
    public function includeProductTypes(Products $product)
    {
        return $this->getRelatedData(
            'item',
            $product,
            BaseTransformer::class,
            Relationships::PRODUCT_TYPES
        );
    }
}
