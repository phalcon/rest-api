<?php

declare(strict_types=1);

namespace Niden\Transformers;

use function array_intersect;
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
    /** @var array */
    private $fields = [];

    /** @var string */
    private $resource = '';

    /**
     * BaseTransformer constructor.
     *
     * @param array  $fields
     * @param string $resource
     */
    public function __construct(array $fields = [], string $resource = '')
    {
        $this->fields   = $fields;
        $this->resource = $resource;
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     * @throws ModelException
     */
    public function transform(AbstractModel $model)
    {
        $modelFields     = array_keys($model->getModelFilters());
        $requestedFields = $this->fields[$this->resource] ?? $modelFields;
        $fields          = array_intersect($modelFields, $requestedFields);
        $data            = [];
        foreach ($fields as $field) {
            $data[$field] = $model->get($field);
        }

        return $data;
    }

    /**
     * @param string        $method
     * @param AbstractModel $model
     * @param string        $transformer
     * @param string        $resource
     *
     * @return Collection|Item
     */
    protected function getRelatedData(string $method, AbstractModel $model, string $transformer, string $resource)
    {
        /** @var AbstractModel $data */
        $data = $model->getRelated($resource);

        return $this->$method($data, new $transformer($this->fields, $resource), $resource);
    }
}
