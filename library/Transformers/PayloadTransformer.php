<?php

namespace Niden\Transformers;

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
        $payload   = $content['data'];
        $code      = $content['code'];
        $detail    = $content['detail'];
        $timestamp = date('c');

        return [
            'jsonapi' => [
                'version' => '1.0',
            ],
            'data'   => $payload,
            'errors' => [
                'code'   => $code,
                'detail' => $detail,
            ],
            'meta'   => [
                'timestamp' => $timestamp,
                'hash'      => sha1($timestamp . json_encode($payload)),
            ],
        ];
    }
}
