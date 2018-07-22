<?php

declare(strict_types=1);

namespace Niden\Transformers;

use Niden\Constants\Resources;
use function Niden\Core\envValue;
use Niden\Exception\ModelException;
use Niden\Models\Products;

/**
 * Class ProductsTransformer
 */
class ProductsTransformer extends TypesTransformer
{
    /**
     * @param Products $product
     *
     * @return array
     * @throws ModelException
     */
    public function transform(Products $product)
    {
        return [
            'id'         => $product->get('prd_id'),
            'type'       => Resources::PRODUCTS,
            'attributes' => [
                'name'        => $product->get('prd_name'),
                'description' => $product->get('prd_description'),
                'quantity'    => $product->get('prd_quantity'),
                'price'       => $product->get('prd_price'),




            ],
            'links'      => [
                'self' => sprintf(
                    '%s/products/%s',
                    envValue('APP_URL', 'localhost'),
                    $product->get('prd_id')
                ),
            ]
        ];
    }
}
