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
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Mvc\Model\AbstractModel;

use function array_intersect;

/**
 * Class BaseTransformer
 */
class BaseTransformer extends TransformerAbstract
{
    /** @var array<string, array<int, string>> */
    private array $fields = [];

    /** @var string */
    private string $resource = '';

    /**
     * BaseTransformer constructor.
     *
     * @param array<string, array<int, string>> $fields
     * @param string                            $resource
     */
    public function __construct(array $fields = [], string $resource = '')
    {
        $this->fields   = $fields;
        $this->resource = $resource;
    }

    /**
     * @param AbstractModel $model
     *
     * @return array<string, mixed>
     * @throws ModelException
     */
    public function transform(AbstractModel $model): array
    {
        /**
         * The model decides what may be published. A caller asking for a field
         * outside that set - `?fields[users]=password` - gets nothing back for
         * it, because the intersection is taken against the public set and not
         * against every column the model happens to have.
         */
        $publicFields    = $model->getPublicFields();
        $requestedFields = $this->fields[$this->resource] ?? $publicFields;
        $fields          = array_intersect($publicFields, $requestedFields);
        $data            = [];
        foreach ($fields as $field) {
            $data[$field] = $model->get($field);
        }

        return $data;
    }

    /**
     * A related resource holding many records.
     *
     * Split from getRelatedItem() rather than switched on a method-name string:
     * one method returning Collection|Item cannot tell a caller which it got,
     * so every includeXxx() declaring `: Collection` was lying by half.
     *
     * @param AbstractModel                 $model
     * @param class-string<BaseTransformer> $transformer
     * @param string                        $resource
     *
     * @return Collection
     */
    protected function getRelatedCollection(
        AbstractModel $model,
        string $transformer,
        string $resource
    ): Collection {
        return $this->collection(
            $model->getRelated($resource),
            new $transformer($this->fields, $resource),
            $resource
        );
    }

    /**
     * A related resource holding a single record.
     *
     * @param AbstractModel                 $model
     * @param class-string<BaseTransformer> $transformer
     * @param string                        $resource
     *
     * @return Item
     */
    protected function getRelatedItem(
        AbstractModel $model,
        string $transformer,
        string $resource
    ): Item {
        return $this->item(
            $model->getRelated($resource),
            new $transformer($this->fields, $resource),
            $resource
        );
    }
}
