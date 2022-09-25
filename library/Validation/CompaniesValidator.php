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
