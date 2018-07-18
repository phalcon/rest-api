<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\ProductTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Models\ProductTypes;
use Niden\Traits\FractalTrait;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Niden\Transformers\TypesTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\ProductTypes
 */
class GetController extends BaseController
{
    use FractalTrait;
    use QueryTrait;
    use ResponseTrait;

    /**
     * Get product types
     *
     * @param int $typeId
     *
     * @return array
     */
    public function callAction($typeId = 0)
    {
        $parameters = $this->checkIdParameter('prt_id', $typeId);
        $results    = $this->getRecords(ProductTypes::class, $parameters, 'prt_name');

        return $this->format($results, TypesTransformer::class);
    }
}
