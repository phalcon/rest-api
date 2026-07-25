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

namespace Phalcon\Api\Models;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Filter\Filter;

/**
 * Class IndividualTypes
 */
class IndividualTypes extends AbstractModel
{
    /**
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'id'          => Filter::FILTER_ABSINT,
            'name'        => Filter::FILTER_STRING,
            'description' => Filter::FILTER_STRING,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getPublicFields(): array
    {
        return [
            'id',
            'name',
            'description',
        ];
    }

    /**
     * `description` is published but not sortable - it is free text, so
     * ordering by it is noise rather than a useful view of the collection.
     *
     * @return array<int, string>
     */
    public function getSortableFields(): array
    {
        return [
            'id',
            'name',
        ];
    }

    /**
     * Initialize relationships and model properties
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('co_individual_types');

        $this->hasMany(
            'id',
            Individuals::class,
            'typeId',
            [
                'alias'    => Relationships::INDIVIDUALS,
                'reusable' => true,
            ]
        );

        parent::initialize();
    }
}
