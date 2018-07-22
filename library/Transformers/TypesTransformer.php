<?php

declare(strict_types=1);

namespace Niden\Transformers;

use League\Fractal\TransformerAbstract;
use function Niden\Core\envValue;
use Niden\Exception\ModelException;
use Niden\Mvc\Model\AbstractModel;

/**
 * Class TypesTransformer
 */
class TypesTransformer extends TransformerAbstract
{
    /** @var string */
    protected $prefix = '';

    /** @var string */
    protected $type   = '';

    /** @var string */
    protected $url    = '';

    /**
     * @param AbstractModel $model
     *
     * @return array
     * @throws ModelException
     */
    public function transform(AbstractModel $model)
    {
        return [
            'id'         => $model->get(sprintf('%s_id', $this->prefix)),
            'type'       => $this->type,
            'attributes' => [
                'name'        => $model->get(sprintf('%s_name', $this->prefix)),
                'description' => $model->get(sprintf('%s_description', $this->prefix)),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL', 'localhost'),
                    $this->url,
                    $model->get($this->prefix . '_id')
                ),
            ]
        ];
    }
}
