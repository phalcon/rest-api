<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Traits;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
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
