<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\IndividualTypes;

use Niden\Api\Controllers\BaseController;
use Niden\Models\IndividualTypes;
use Niden\Traits\FractalTrait;
use Niden\Traits\QueryTrait;
use Niden\Traits\ResponseTrait;
use Niden\Transformers\TypesTransformer;

/**
 * Class GetController
 *
 * @package Niden\Api\Controllers\IndividualTypes
 */
class GetController extends BaseController
{
    use FractalTrait;
    use QueryTrait;
    use ResponseTrait;

    /**
     * Get the individual types
     *
     * @param int $typeId
     *
     * @return array
     */
    public function callAction($typeId = 0)
    {
        $parameters = $this->checkIdParameter('idt_id', $typeId);
        $results    = $this->getRecords(IndividualTypes::class, $parameters, 'idt_name');

        return $this->format($results, TypesTransformer::class);
    }
}
