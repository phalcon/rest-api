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

namespace Phalcon\Api\Traits;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\JsonApiSerializer;
use Phalcon\Api\Transformers\BaseTransformer;

use function Phalcon\Api\Core\envValue;
use function sprintf;
use function ucfirst;

/**
 * Trait FractalTrait
 */
trait FractalTrait
{
    /**
     * Format results based on a transformer
     *
     * @param string                            $method        'collection' or 'item'
     * @param mixed                             $results
     * @param class-string<BaseTransformer>     $transformer
     * @param string                            $resource
     * @param array<int, string>                $relationships
     * @param array<string, array<int, string>> $fields
     *
     * @return array<string, mixed>
     */
    protected function format(
        string $method,
        $results,
        string $transformer,
        string $resource,
        array $relationships = [],
        array $fields = []
    ): array {
        $url     = envValue('APP_URL', 'http://localhost');
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));

        /**
         * Process relationships
         */
        if (true !== empty($relationships)) {
            $manager->parseIncludes($relationships);
        }

        /** @var class-string<ResourceInterface> $class */
        $class = sprintf('League\Fractal\Resource\%s', ucfirst($method));

        return $manager
            ->createData(new $class($results, new $transformer($fields, $resource), $resource))
            ->toArray()
        ;
    }
}
