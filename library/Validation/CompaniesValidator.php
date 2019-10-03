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

namespace Phalcon\Api\Validation;

use Phalcon\Filter;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class CompaniesValidator extends Validation
{
    public function initialize()
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
