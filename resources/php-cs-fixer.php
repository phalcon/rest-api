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

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$root = dirname(__FILE__, 2);

$finder = Finder::create()
    ->in(
        [
            $root . '/src',
            $root . '/tests',
        ]
    )
    // Build artifacts - phpstan's container cache and this fixer's own cache
    // file live here. phpcs excludes it for the same reason.
    ->exclude('_output');

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    // declare_strict_types is a risky rule.
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile($root . '/tests/_output/.php-cs-fixer.cache')
    ->setRules(
        [
            // The two rules below are a local addition on top of the ordering
            // rules shared with the other Phalcon projects. They are kept here
            // until the global coding standard is agreed, at which point they
            // should move into the shared set rather than stay a divergence.
            // PSR-12 (via phpcs) checks neither, so nothing else enforces them.
            'declare_strict_types'   => true,
            'no_unused_imports'      => true,
            'ordered_imports'        => [
                'sort_algorithm' => 'alpha',
                'imports_order'  => ['class', 'function', 'const'],
            ],
            'ordered_class_elements' => [
                'sort_algorithm' => 'alpha',
                'order'          => [
                    'use_trait',
                    'case',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public_static',
                    'property_protected_static',
                    'property_private_static',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'magic',
                    'phpunit',
                    'method_public_static',
                    'method_protected_static',
                    'method_private_static',
                    'method_public',
                    'method_protected',
                    'method_private',
                ],
            ],
        ]
    )
    ->setFinder($finder);
