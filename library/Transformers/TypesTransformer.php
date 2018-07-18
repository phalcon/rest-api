<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
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
        return [
            'id'          => $model->get(sprintf('%s_id', $model->getTablePrefix())),
            'name'        => $model->get(sprintf('%s_name', $model->getTablePrefix())),
            'description' => $model->get(sprintf('%s_description', $model->getTablePrefix())),
        ];
    }
}
