<?php

declare(strict_types=1);

namespace Niden\Transformers;

use function array_keys;
use League\Fractal\TransformerAbstract;
use Niden\Exception\ModelException;
use Niden\Mvc\Model\AbstractModel;

/**
 * Class CompaniesTransformer
 */
class BaseTransformer extends TransformerAbstract
{
    /**
     * @param AbstractModel $model
     *
     * @return array
     * @throws ModelException
     */
    public function transform(AbstractModel $model)
    {
        $data    = [];
        $filters = array_keys($model->getModelFilters());
        foreach ($filters as $column) {
            $data[$column] = $model->get($column);
        }

        return $data;
    }
}
