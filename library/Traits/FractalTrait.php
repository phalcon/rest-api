<?php

namespace Niden\Traits;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

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
     *
     * @return array
     */
    protected function format($results, string $transformer): array
    {
        $manager  = new Manager();
        $resource = new Collection($results, new $transformer());
        $results  = $manager->createData($resource)->toArray();

        return $results['data'];
    }
}
