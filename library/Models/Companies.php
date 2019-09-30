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
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class Companies
 */
class Companies extends AbstractModel
{
    /**
     * Initialize relationships and model properties
     */
    public function initialize()
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
     * Validates the company name
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
