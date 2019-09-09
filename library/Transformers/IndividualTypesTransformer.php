<?php

declare(strict_types=1);

namespace Phalcon\Api\Transformers;

use League\Fractal\Resource\Collection;
use Niden\Constants\Relationships;
use Niden\Models\IndividualTypes;

/**
 * Class IndividualTypesTransformer
 */
class IndividualTypesTransformer extends BaseTransformer
{
    /** @var array */
    protected $availableIncludes = [
        Relationships::INDIVIDUALS,
    ];

    /** @var string */
    protected $resource = Relationships::INDIVIDUAL_TYPES;

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
