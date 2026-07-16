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

namespace Phalcon\Api\Transformers;

use League\Fractal\Resource\Collection;
use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Models\ProductTypes;

/**
 * Class ProductTypesTransformer
 */
class ProductTypesTransformer extends BaseTransformer
{
    protected array $availableIncludes = [
        Relationships::PRODUCTS,
    ];

    /**
     * @param ProductTypes $type
     *
     * @return Collection
     */
    public function includeProducts(ProductTypes $type): Collection
    {
        return $this->getRelatedData(
            'collection',
            $type,
            ProductsTransformer::class,
            Relationships::PRODUCTS
        );
    }
}
