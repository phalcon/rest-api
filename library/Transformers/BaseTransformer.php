<?php

declare(strict_types=1);

namespace Niden\Transformers;

use function array_keys;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Niden\Exception\ModelException;
use Niden\Mvc\Model\AbstractModel;

/**
 * Class BaseTransformer
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

    /**
     * @param string        $method
     * @param AbstractModel $model
     * @param string        $transformer
     * @param string        $relationship
     *
     * @return Collection|Item
     */
    protected function getRelatedData(string $method, AbstractModel $model, string $transformer, string $relationship)
    {
        /** @var AbstractModel $data */
        $data = $model->getRelated($relationship);

        return $this->$method($data, new $transformer(), $relationship);
    }
}
