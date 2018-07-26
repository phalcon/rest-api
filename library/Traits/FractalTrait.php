<?php

declare(strict_types=1);

namespace Niden\Traits;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use function Niden\Core\envValue;

/**
 * Trait FractalTrait
 *
 * @package Niden\Traits
 */
trait FractalTrait
{
    /**
     * Format results based on a transformer
     *
     * @param mixed  $results
     * @param string $transformer
     * @param string $resource
     * @param array  $relationships
     *
     * @return array
     */
    protected function format($results, string $transformer, string $resource, array $relationships = []): array
    {
        $url      = envValue('APP_URL', 'http://localhost');
        $manager  = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));

        /**
         * Process relationships
         */
        foreach ($relationships as $relationship) {
            $manager->parseIncludes($relationship);
        }

        $resource = new Collection($results, new $transformer(), $resource);
        $results  = $manager->createData($resource)->toArray();

        return $results;
    }
}
