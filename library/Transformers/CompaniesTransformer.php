<?php

declare(strict_types=1);

namespace Phalcon\Api\Transformers;

use League\Fractal\Resource\Collection;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\Companies;

/**
 * Class CompaniesTransformer
 */
class CompaniesTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::PRODUCTS,
        Relationships::INDIVIDUALS,
    ];

    /**
     * @param Companies $company
     *
     * @return Collection
     */
    public function includeIndividuals(Companies $company)
    {
        return $this->getRelatedData(
            'collection',
            $company,
            IndividualsTransformer::class,
            Relationships::INDIVIDUALS
        );
    }

    /**
     * @param Companies $company
     *
     * @return Collection
     */
    public function includeProducts(Companies $company)
    {
        return $this->getRelatedData(
            'collection',
            $company,
            ProductsTransformer::class,
            Relationships::PRODUCTS
        );
    }
}
