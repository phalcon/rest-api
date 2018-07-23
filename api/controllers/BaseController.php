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
use Niden\Transformers\BaseTransformer;
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
     * @param int    $recordId
     *
     * @return array
     */
    protected function checkIdParameter($recordId = 0): array
    {
        $parameters = [];

        /** @var int $localId */
        $localId = $this->filter->sanitize($recordId, Filter::FILTER_ABSINT);

        if ($localId > 0) {
            $parameters['id'] = $localId;
        }

        return $parameters;
    }

    /**
     * Processes getting records as a while or one using an id
     *
     * @param string $model
     * @param string $resource
     * @param int    $recordId
     * @param string $orderBy
     *
     * @return array
     */
    protected function processCall(
        string $model,
        string $resource,
        $recordId = 0,
        string $orderBy = ''
    ) {
        $parameters = $this->checkIdParameter($recordId);
        $results    = $this->getRecords($model, $parameters, $orderBy);

        return $this->format($results, BaseTransformer::class, $resource);
    }
}
