<?php

declare(strict_types=1);

namespace Niden\Api\Controllers;

use Niden\Http\Response;
use Niden\Models\Co;
use Niden\Traits\FractalTrait;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Phalcon\Cache\Backend\Libmemcached;
use Phalcon\Config;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro;
use function explode;
use function in_array;
use function strtolower;

/**
 * Class BaseController
 *
 * @package Niden\Api\Controllers
 *
 * @property Micro        $application
 * @property Libmemcached $cache
 * @property Config       $config
 * @property Response     $response
 */
class BaseController extends Controller
{
    use FractalTrait;
    use QueryTrait;
    use ResponseTrait;

    /** @var string */
    protected $model = '';

    /** @var array */
    protected $includes = [];

    /** @var string */
    protected $resource = '';

    /** @var string */
    protected $transformer = '';

    /** @var string */
    protected $orderBy = 'name';

    /**
     * Get the company/companies
     *
     * @param int    $id
     *
     * @return array
     */
    public function callAction($id = 0)
    {
        $parameters = $this->checkIdParameter($id);
        $related    = $this->checkIncludes();
        $results    = $this->getRecords($this->config, $this->cache, $this->model, $parameters, $this->orderBy);

        if (count($parameters) > 0 && 0 === count($results)) {
            return $this->send404();
        }

        return $this->format($results, $this->transformer, $this->resource, $related);
    }

    /**
     * Checks the passed id parameter and returns the relevant array back
     *
     * @param int $recordId
     *
     * @return array
     */
    private function checkIdParameter($recordId = 0): array
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
     * Processes the includes requested; Unknown includes are ignored
     *
     * @return array
     */
    private function checkIncludes(): array
    {
        $related  = [];
        $includes = $this->request->getQuery('includes', [Filter::FILTER_STRING, Filter::FILTER_TRIM], '');
        if (true !== empty($includes)) {
            $requestedIncludes = explode(',', $includes);
            foreach ($requestedIncludes as $include) {
                if (true === in_array($include, $this->includes)) {
                    $related[] = strtolower($include);
                }
            }
        }

        return $related;
    }

    /**
     * Sets the response with a 404 and returns an empty array back
     *
     * @return array
     */
    private function send404(): array 
    {
        $this->response->setPayloadError('Not Found')->setStatusCode(404);

        return [];
    }
}
