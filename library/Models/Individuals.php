<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Models;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Filter;

/**
 * Class Individuals
 */
class Individuals extends AbstractModel
{
    /**
     * Initialize relationships and model properties
     */
    public function initialize()
    {
        $this->setSource('co_individuals');

        $this->belongsTo(
            'companyId',
            Companies::class,
            'id',
            [
                'alias'    => Relationships::COMPANIES,
                'reusable' => true,
            ]
        );

        $this->hasOne(
            'typeId',
            IndividualTypes::class,
            'id',
            [
                'alias'    => Relationships::INDIVIDUAL_TYPES,
                'reusable' => true,
            ]
        );

        parent::initialize();
    }

    /**
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'id'        => Filter::FILTER_ABSINT,
            'companyId' => Filter::FILTER_ABSINT,
            'typeId'    => Filter::FILTER_ABSINT,
            'prefix'    => Filter::FILTER_STRING,
            'first'     => Filter::FILTER_STRING,
            'middle'    => Filter::FILTER_STRING,
            'last'      => Filter::FILTER_STRING,
            'suffix'    => Filter::FILTER_STRING,
        ];
    }
}
