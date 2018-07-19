<?php

declare(strict_types=1);

namespace Niden\Api\Controllers;

use Niden\Exception\ModelException;
use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\Users;
use Niden\Traits\FractalTrait;
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
    use FractalTrait;
    use QueryTrait;

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

    /**
     * Processes getting records as a while or one using an id
     *
     * @param string $model
     * @param string $field
     * @param string $transformer
     * @param int    $recordId
     * @param string $orderBy
     *
     * @return array
     */
    protected function processCall(
        string $model,
        string $field,
        string $transformer,
        $recordId = 0,
        string $orderBy = ''
    ) {
        $parameters = $this->checkIdParameter($field, $recordId);
        $results    = $this->getRecords($model, $parameters, $orderBy);

        return $this->format($results, $transformer);
    }
}
