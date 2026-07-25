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
        $url     = $this->getBaseUrl();
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

        /**
         * Scope::toArray() is typed nullable; an empty document is an empty
         * array here rather than null.
         */
        return $manager
            ->createData(
                new $class(
                    $results,
                    new $transformer($fields, $resource),
                    $resource
                )
            )
            ->toArray() ?? []
        ;
    }

    /**
     * The URL the serializer builds every `self` and `related` link from.
     *
     * Declared rather than resolved. `app.url` is application configuration,
     * so it belongs to the `config` service - but a trait has no constructor
     * to inject into, and its users do not share a base class to inherit one
     * from. Requiring them to answer keeps the dependency explicit and checked
     * at compile time, where reaching for the default container would have
     * hidden it and reading the environment here would have gone around
     * config.php entirely.
     *
     * @return string
     */
    abstract protected function getBaseUrl(): string;
}
