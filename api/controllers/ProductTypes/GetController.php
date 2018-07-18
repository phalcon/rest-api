<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\ProductTypes;

use Niden\Models\ProductTypes;
use Niden\Traits\FractalTrait;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Niden\Transformers\TypesTransformer;
use Phalcon\Mvc\Controller;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\ProductTypes
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

        return $this->format($results, TypesTransformer::class);
    }
}
