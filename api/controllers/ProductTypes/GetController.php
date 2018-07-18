<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\ProductTypes;

use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\ProductTypes;
use Niden\Traits\FractalTrait;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Niden\Transformers\ProductTypesTransformer;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\ProductTypes
 *
 * @property Micro    $application
 * @property Request  $request
 * @property Response $response
 */
class GetController extends Controller
{
    use FractalTrait;
    use QueryTrait;
    use ResponseTrait;

    /**
     * Get a user
     */
    public function callAction()
    {
        $results = $this->getRecords(ProductTypes::class, [], 'prt_name');

        if (count($results) > 0) {
            return $this->format($results, ProductTypesTransformer::class);
        } else {
            $this->halt($this->application, 'Record(s) not found');
        }
    }
}
