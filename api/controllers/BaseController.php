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

    /** @var string */
    protected $model       = '';
    /** @var string */
    protected $resource    = '';
    /** @var string */
    protected $transformer = '';
    /** @var string */
    protected $orderBy     = 'name';

    /**
     * Get the company/companies
     *
     * @param int $id
     *
     * @return array
     */
    public function callAction($id = 0)
    {
        $parameters = $this->checkIdParameter($id);
        $results    = $this->getRecords($this->model, $parameters, $this->orderBy);

        return $this->format($results, $this->transformer, $this->resource);
    }

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
}
