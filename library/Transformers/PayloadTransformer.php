<?php

declare(strict_types=1);

namespace Niden\Transformers;

use function array_merge;
use function date;
use function json_encode;
use function sha1;
use League\Fractal\TransformerAbstract;

/**
 * Class PayloadTransformer
 */
class PayloadTransformer extends TransformerAbstract
{
    /**
     * @param array $content
     *
     * @return array
     */
    public function transform(array $content)
    {
        $timestamp = date('c');
        $jsonApi   = [
            'jsonapi' => [
                'version' => '1.0',
            ]
        ];
        $meta      = [
            'meta' => [
                'timestamp' => $timestamp,
                'hash'      => sha1($timestamp . json_encode($content)),
            ],
        ];

        $result    = array_merge(
            $jsonApi,
            $content,
            $meta
        );

        return $result;
    }
}
