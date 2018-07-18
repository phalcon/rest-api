<?php

declare(strict_types=1);

namespace Niden\Api\Controllers\ProductTypes;

use Niden\Http\Request;
use Niden\Http\Response;
use Niden\Models\ProductTypes;
use Niden\Traits\FractalTrait;
use Niden\Traits\ResponseTrait;
use Niden\Traits\UserTrait;
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
    use ResponseTrait;
    use UserTrait;

    /**
     * Get a user
     */
    public function callAction()
    {
        $builder = new Builder();
        $results = $builder
                    ->addFrom(ProductTypes::class)
                    ->orderBy('prt_name')
                    ->getQuery()
                    ->execute()
        ;

        if (count($results) > 0) {
            return $this->format($results, ProductTypesTransformer::class);
        } else {
            $this->halt($this->application, 'Record(s) not found');
        }
    }
}
