<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\Resource\Collection;
use Niden\Constants\Relationships;
use Niden\Models\IndividualTypes;

/**
 * Class IndividualTypesTransformer
 */
class IndividualTypesTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::INDIVIDUALS,
    ];

    /**
     * @param IndividualTypes $type
     *
     * @return Collection
     */
    public function includeIndividuals(IndividualTypes $type)
    {
        return $this->getRelatedData(
            'collection',
            $type,
            IndividualsTransformer::class,
            Relationships::INDIVIDUALS
        );
    }
}
