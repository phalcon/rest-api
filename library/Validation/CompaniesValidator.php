<?php

declare(strict_types=1);

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
