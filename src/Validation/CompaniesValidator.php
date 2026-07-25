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

namespace Phalcon\Api\Validation;

use Phalcon\Filter\Filter;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;

/**
 * What the request has to carry before a company can be built from it.
 *
 * Runs on the posted payload in AddController, before any record exists, and
 * answers with the message the caller sees. Uniqueness is not here: that one
 * needs the database and has to hold for writes that never see a request, so
 * it lives on the model - see Companies::validation(). Between them, a bad
 * request is rejected early and a bad row is rejected always.
 */
class CompaniesValidator extends Validation
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $presenceOf = new PresenceOf(
            [
                'message' => "The company name is required",
            ]
        );
        $this->setFilters('name', Filter::FILTER_STRING);
        $this->add('name', $presenceOf);
    }
}
