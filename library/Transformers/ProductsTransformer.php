<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Products;
use Niden\Models\ProductTypes;

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
        /** @var Companies $companies */
        $companies = $product->getRelated(Relationships::COMPANIES);

        return $this->collection($companies, new CompaniesTransformer(), Relationships::PRODUCT_TYPES);
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
        /** @var ProductTypes $productType */
        $productType = $product->getRelated(Relationships::PRODUCT_TYPE);

        return $this->item($productType, new BaseTransformer(), Relationships::PRODUCT_TYPES);
    }
}
