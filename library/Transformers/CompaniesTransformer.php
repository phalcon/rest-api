<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\Resource\Collection;
use Niden\Constants\Relationships;
use Niden\Models\Companies;
use Niden\Models\Products;

/**
 * Class CompaniesTransformer
 */
class CompaniesTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::PRODUCTS,
    ];

    /**
     * @param Companies $company
     *
     * @return Collection
     */
    public function includeProducts(Companies $company)
    {
        /** @var Products $products */
        $products = $company->getRelated(Relationships::PRODUCTS);

        return $this->collection($products, new ProductsTransformer(), Relationships::PRODUCTS);
    }
}
