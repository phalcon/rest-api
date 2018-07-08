<?php

declare(strict_types=1);

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
        $section   = (true === isset($content['errors'])) ? 'errors' : 'data';
        $timestamp = date('c');
        $result    = [
            'jsonapi' => [
                'version' => '1.0',
            ],
            $section => $content[$section],
            'meta'   => [
                'timestamp' => $timestamp,
                'hash'      => sha1($timestamp . json_encode($content[$section])),
            ],
        ];

        return $result;
    }
}
