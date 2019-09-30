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

namespace Phalcon\Api\Api\Controllers;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Model\MetaData\Libmemcached as ModelsMetadataCache;
use function explode;
use function implode;
use function in_array;
use function strtolower;
use function substr;

/**
 * Class BaseController
 *
 * @property Micro               $application
 * @property Cache               $cache
 * @property Config              $config
 * @property ModelsMetadataCache $modelsMetadata
 * @property Response            $response
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
    protected $method = 'collection';

    /** @var string */
    protected $orderBy = 'name';

    /** @var string */
    protected $resource = '';

    /** @var array */
    protected $sortFields = [];

    /** @var string */
    protected $transformer = '';

    /**
     * Get the company/companies
     *
     * @param int $id
     */
    public function callAction($id = 0)
    {
        $parameters = $this->checkIdParameter($id);
        $fields     = $this->checkFields();
        $related    = $this->checkIncludes();
        $validSort  = $this->checkSort();

        if (true !== $validSort) {
            $this->sendError($this->response::BAD_REQUEST);
        } else {
            $results = $this->getRecords($this->config, $this->cache, $this->model, $parameters, $this->orderBy);
            if (count($parameters) > 0 && 0 === count($results)) {
                $this->sendError($this->response::NOT_FOUND);
            } else {
                $data = $this->format(
                    $this->method,
                    $results,
                    $this->transformer,
                    $this->resource,
                    $related,
                    $fields
                );
                $this
                    ->response
                    ->setPayloadSuccess($data);
            }
        }
    }

    private function checkFields(): array
    {
        $data      = [];
        $fieldSent = $this->request->getQuery('fields', [Filter::FILTER_STRING, Filter::FILTER_TRIM], []);
        foreach ($fieldSent as $resource => $fields) {
            $data[$resource] = explode(',', $fields);
        }

        return $data;
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
     * Process the sort. If supplied change the `orderBy` of the builder. If a
     * field that is not supported has been supplied return false
     *
     * @return bool
     */
    private function checkSort(): bool
    {
        $sortArray  = [];
        $sortFields = $this->request->getQuery('sort', [Filter::FILTER_STRING, Filter::FILTER_TRIM], '');
        if (true !== empty($sortFields)) {
            $requestedSort = explode(',', $sortFields);
            foreach ($requestedSort as $field) {
                list($trueField, $direction) = $this->getFieldAndDirection($field);
                /**
                 * Is this a valid field and is it sortable? If yes, process it
                 */
                if (true === ($this->sortFields[$trueField] ?? false)) {
                    $sortArray[] = $trueField . $direction;
                } else {
                    return false;
                }
            }
        }

        /**
         * Check the results. If we have something update the $orderBy
         */
        if (count($sortArray) > 0) {
            $this->orderBy = implode(',', $sortArray);
        }

        return true;
    }


    /**
     * Return the field name and direction
     *
     * @param string $field
     *
     * @return array
     */
    private function getFieldAndDirection(string $field): array
    {
        $trueField = strtolower($field);
        $direction = ' asc';

        /**
         * Ascending or descending
         */
        if ('-' === substr($trueField, 0, 1)) {
            $trueField = substr($trueField, 1);
            $direction = ' desc';
        }

        return [$trueField, $direction];
    }

    /**
     * Sets the response with an error code
     *
     * @param int $code
     */
    private function sendError(int $code)
    {
        $this
            ->response
            ->setPayloadError($this->response->getHttpCodeDescription($code))
            ->setStatusCode($code)
        ;
    }
}
