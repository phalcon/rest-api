<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\IndividualTypes;

use Niden\Models\IndividualTypes;
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
     * Get the individual types
     */
    public function callAction()
    {
        $results = $this->getRecords(IndividualTypes::class, [], 'idt_name');

        return $this->format($results, TypesTransformer::class);
    }
}
