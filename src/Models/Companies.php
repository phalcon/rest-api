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
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\Uniqueness;

/**
 * Class Companies
 */
class Companies extends AbstractModel
{
    /**
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'id'      => Filter::FILTER_ABSINT,
            'name'    => Filter::FILTER_STRING,
            'address' => Filter::FILTER_STRING,
            'city'    => Filter::FILTER_STRING,
            'phone'   => Filter::FILTER_STRING,
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
            'address',
            'city',
            'phone',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getSortableFields(): array
    {
        return [
            'id',
            'name',
            'address',
            'city',
            'phone',
        ];
    }

    /**
     * Initialize relationships and model properties
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('co_companies');

        $this->hasMany(
            'id',
            Individuals::class,
            'companyId',
            [
                'alias'    => Relationships::INDIVIDUALS,
                'reusable' => true,
            ]
        );

        $this->hasManyToMany(
            'id',
            CompaniesXProducts::class,
            'companyId',
            'productId',
            Products::class,
            'id',
            [
                'alias'    => Relationships::PRODUCTS,
                'reusable' => true,
            ]
        );

        parent::initialize();
    }

    /**
     * The database invariant: no two companies share a name.
     *
     * Deliberately here and not in CompaniesValidator. This one needs the
     * database to answer, and it has to hold for every write - a record saved
     * from a CLI task or a future endpoint never passes through the request
     * validator, but it does pass through here.
     *
     * The two are a pair, not a duplicate: the validator rejects a bad request,
     * this rejects a bad row. See Validation\CompaniesValidator.
     *
     * @return bool
     */
    public function validation()
    {
        $validator = new Validation();
        $validator->add(
            'name',
            new Uniqueness(
                [
                    'message' => 'The company name already exists in the database',
                ]
            )
        );

        return $this->validate($validator);
    }
}
