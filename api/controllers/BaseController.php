<?php

declare(strict_types=1);

namespace Niden\Api\Controllers;

use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\QueryTrait;
use Niden\Traits\TokenTrait;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class BaseController
 *
 * @package Niden\Api\Controllers
 */
class BaseController extends Controller
{
    /**
     * Checks the passed id parameter and returns the relevant array back
     *
     * @param string $field
     * @param int    $recordId
     *
     * @return array
     */
    protected function checkIdParameter(string $field, $recordId = 0): array
    {
        $parameters = [];

        /** @var int $localId */
        $localId = $this->filter->sanitize($recordId, Filter::FILTER_ABSINT);

        if ($localId > 0) {
            $parameters[$field] = $localId;
        }

        return $parameters;
    }
}
