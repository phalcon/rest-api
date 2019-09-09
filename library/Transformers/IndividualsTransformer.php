<?php

declare(strict_types=1);

namespace Phalcon\Api\Transformers;

use League\Fractal\Resource\Item;
use Niden\Constants\Relationships;
use Niden\Models\Individuals;

/**
 * Class IndividualsTransformer
 */
class IndividualsTransformer extends BaseTransformer
{
    /** @var array */
    protected $availableIncludes = [
        Relationships::COMPANIES,
        Relationships::INDIVIDUAL_TYPES,
    ];

    /** @var string */
    protected $resource = Relationships::INDIVIDUALS;

    /**
     * Includes the companies
     *
     * @param Individuals $individual
     *
     * @return Item
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
