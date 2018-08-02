<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Niden\Constants\Relationships;
use Niden\Models\Individuals;

/**
 * Class IndividualsTransformer
 */
class IndividualsTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::COMPANIES,
        Relationships::INDIVIDUAL_TYPES,
    ];

    /**
     * Includes the companies
     *
     * @param Individuals $individual
     *
     * @return Collection
     */
    public function includeCompanies(Individuals $individual)
    {
        return $this->getRelatedData(
            'item',
            $individual,
            CompaniesTransformer::class,
            Relationships::COMPANIES
        );
    }

    /**
     * Includes the product types
     *
     * @param Individuals $individual
     *
     * @return Item
     */
    public function includeIndividualTypes(Individuals $individual)
    {
        return $this->getRelatedData(
            'item',
            $individual,
            BaseTransformer::class,
            Relationships::INDIVIDUAL_TYPES
        );
    }
}
