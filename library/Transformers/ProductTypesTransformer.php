<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
use Niden\Exception\ModelException;
use Niden\Models\ProductTypes;

/**
 * Class ProductTypesTransformer
 */
class ProductTypesTransformer extends TransformerAbstract
{
    /**
     * @param ProductTypes $type
     *
     * @return array
     * @throws ModelException
     */
    public function transform(ProductTypes $type)
    {
        return [
            'id'   => $type->get('prt_id'),
            'name' => $type->get('prt_name'),
        ];
    }
}
