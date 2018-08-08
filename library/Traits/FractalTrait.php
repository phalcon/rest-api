<?php

declare(strict_types=1);

namespace Niden\Traits;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use function Niden\Core\envValue;
use function sprintf;
use function ucfirst;

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
     * @param string  $method
     * @param mixed   $results
     * @param string  $transformer
     * @param string  $resource
     * @param array   $relationships
     * @param array   $fields
     *
     * @return array
     */
    protected function format(
        string $method,
        $results,
        string $transformer,
        string $resource,
        array $relationships = [],
        array $fields = []
    ): array {
        $url      = envValue('APP_URL', 'http://localhost');
        $manager  = new Manager();
        $manager->setSerializer(new JsonApiSerializer($url));

        /**
         * Process relationships
         */
        if (count($relationships) > 0) {
            $manager->parseIncludes($relationships);
        }

        $class    = sprintf('League\Fractal\Resource\%s', ucfirst($method));
        $resource = new $class($results, new $transformer($fields, $resource), $resource);
        $results  = $manager->createData($resource)->toArray();

        return $results;
    }
}
