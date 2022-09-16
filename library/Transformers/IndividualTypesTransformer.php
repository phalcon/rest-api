<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Transformers;

use League\Fractal\Resource\Collection;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\IndividualTypes;

/**
 * Class IndividualTypesTransformer
 */
class IndividualTypesTransformer extends BaseTransformer
{
    /** @var array */
    protected array $availableIncludes = [
        Relationships::INDIVIDUALS,
    ];

    /** @var string */
    protected string $resource = Relationships::INDIVIDUAL_TYPES;

    /**
     * @param IndividualTypes $type
     *
     * @return Collection
     */
    public function includeIndividuals(IndividualTypes $type): Collection
    {
        return $this->getRelatedData(
            'collection',
            $type,
            IndividualsTransformer::class,
            Relationships::INDIVIDUALS
        );
    }
}
