<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
use Niden\Constants\Resources;
use Niden\Exception\ModelException;
use Niden\Mvc\Model\AbstractModel;

/**
 * Class TypesTransformer
 */
class TypesTransformer extends TransformerAbstract
{

    /**
     * @param AbstractModel $model
     *
     * @return array
     * @throws ModelException
     */
    public function transform(AbstractModel $model)
    {
        $prefix = $model->getTablePrefix();
        $type   = ('prt' === $prefix) ? Resources::PRODUCT_TYPES : Resources::INDIVIDUAL_TYPES;
        return [
            'id'         => $model->get(sprintf('%s_id', $prefix)),
            'type'       => $type,
            'attributes' => [
                'name'        => $model->get(sprintf('%s_name', $prefix)),
                'description' => $model->get(sprintf('%s_description', $prefix)),
            ],
        ];
    }
}
