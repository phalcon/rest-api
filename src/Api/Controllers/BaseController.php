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

namespace Phalcon\Api\Api\Controllers;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Api\Services\QueryService;
use Phalcon\Api\Traits\BaseUrlTrait;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Filter\Exception;
use Phalcon\Filter\Filter;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro;

use function explode;
use function implode;
use function in_array;
use function str_starts_with;
use function strtolower;
use function substr;

/**
 * Class BaseController
 *
 * @property Micro        $application
 * @property QueryService $queryService
 * @property Response     $response
 */
class BaseController extends Controller
{
    use BaseUrlTrait;
    use FractalTrait;
    use ResponseTrait;

    /** @var array<int, string> */
    protected array $includes = [];

    /** @var string */
    protected string $method = 'collection';

    /** @var string */
    protected string $model = '';

    /** @var string */
    protected string $orderBy = 'name';

    /** @var string */
    protected string $resource = '';

    /**
     * Defaults to the base transformer rather than an empty string: every
     * concrete controller names its own, and '' was never a usable value.
     *
     * @var class-string<BaseTransformer>
     */
    protected string $transformer = BaseTransformer::class;

    /**
     * Get the record or the collection.
     *
     * The second parameter arrives from the two relationship routes -
     * `/companies/1/products` and `/companies/1/relationships/products`. It
     * used to be declared nowhere, so the segment was parsed by the router,
     * discarded by this method, and both routes quietly answered with the
     * plain record - while every response went on advertising those same URLs
     * in its `links`.
     *
     * @param mixed  $id
     * @param string $relationships Comma separated, from the route
     *
     * @return void
     * @throws Exception
     */
    public function callAction(mixed $id = 0, string $relationships = ''): void
    {
        $parameters = $this->checkIdParameter($id);
        $fields     = $this->checkFields();
        $validSort  = $this->checkSort();

        /**
         * Named in the path, a relationship this resource does not have is a
         * URL that does not exist - unlike `?includes=`, where an unknown name
         * is a request for something extra that simply is not there.
         */
        if ('' !== $relationships) {
            $related = $this->filterRelationships($relationships);

            if ([] === $related) {
                $this->sendError($this->response::NOT_FOUND);

                return;
            }
        } else {
            $related = $this->checkIncludes();
        }

        if (true !== $validSort) {
            $this->sendError($this->response::BAD_REQUEST);
        } else {
            $results = $this->queryService->getRecords(
                $this->model,
                $parameters,
                $this->orderBy
            );

            /**
             * A record was asked for by id and nothing came back. `getFirst()`
             * rather than `count()`: ResultsetInterface declares the former and
             * not the latter, however countable the concrete Resultset is.
             */
            if (true !== empty($parameters) && null === $results->getFirst()) {
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
                    ->setPayloadSuccess($data)
                ;
            }
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function checkFields(): array
    {
        $data      = [];
        $fieldSent = $this->request->getQuery(
            'fields',
            [Filter::FILTER_STRING, Filter::FILTER_TRIM],
            []
        );
        foreach ($fieldSent as $resource => $fields) {
            $data[$resource] = explode(',', $fields);
        }

        return $data;
    }

    /**
     * Checks the passed id parameter and returns the relevant array back
     *
     * @param mixed $recordId
     *
     * @return array<string, int>
     * @throws Exception
     */
    private function checkIdParameter(mixed $recordId = 0): array
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
     * @return array<int, string>
     */
    private function checkIncludes(): array
    {
        $includes = $this->request->getQuery('includes', [Filter::FILTER_STRING, Filter::FILTER_TRIM], '');

        return $this->filterRelationships($includes);
    }

    /**
     * Process the sort. If supplied change the `orderBy` of the builder. If a
     * field that is not supported has been supplied return false
     *
     * The sortable set comes from the model rather than a copy kept here: two
     * lists of the same columns drifted apart, and the model is what knows
     * which of them it owns.
     *
     * @return bool
     */
    private function checkSort(): bool
    {
        $sortArray  = [];
        $sortFields = $this->request->getQuery('sort', [Filter::FILTER_STRING, Filter::FILTER_TRIM], '');
        if (true !== empty($sortFields)) {
            /** @var AbstractModel $model */
            $model     = new $this->model();
            $sortable  = $model->getSortableFields();

            $requestedSort = explode(',', $sortFields);
            foreach ($requestedSort as $field) {
                list($trueField, $direction) = $this->getFieldAndDirection($field);
                /**
                 * Is this a valid field and is it sortable? If yes, process it
                 */
                if (true === in_array($trueField, $sortable)) {
                    $sortArray[] = $trueField . $direction;
                } else {
                    return false;
                }
            }
        }

        /**
         * Check the results. If we have something update the $orderBy
         */
        if (true !== empty($sortArray)) {
            $this->orderBy = implode(',', $sortArray);
        }

        return true;
    }

    /**
     * Keeps the names this resource actually publishes, in the vocabulary the
     * transformer and the model relationships share. Shared by `?includes=`
     * and by the relationship routes so the two cannot answer differently
     * about what exists.
     *
     * @param string $requested Comma separated relationship names
     *
     * @return array<int, string>
     */
    private function filterRelationships(string $requested): array
    {
        $related = [];

        if (true !== empty($requested)) {
            foreach (explode(',', $requested) as $include) {
                if (true === in_array($include, $this->includes)) {
                    $related[] = strtolower($include);
                }
            }
        }

        return $related;
    }


    /**
     * Return the field name and direction
     *
     * @param string $field
     *
     * @return array{0: string, 1: string} The field name and its direction
     */
    private function getFieldAndDirection(string $field): array
    {
        $trueField = strtolower($field);
        $direction = ' asc';

        /**
         * Ascending or descending
         */
        if (true === str_starts_with($trueField, '-')) {
            $trueField = substr($trueField, 1);
            $direction = ' desc';
        }

        return [$trueField, $direction];
    }

    /**
     * Sets the response with an error code
     *
     * @param int $code
     *
     * @return void
     */
    private function sendError(int $code): void
    {
        $this
            ->response
            ->setPayloadError($this->response->getHttpCodeDescription($code))
            ->setStatusCode($code)
        ;
    }
}
