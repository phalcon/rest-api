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

use Phalcon\Config\Config;

/**
 * Answers FractalTrait::getBaseUrl() for the controllers.
 *
 * FractalTrait declares that method and refuses to resolve it itself, so that
 * nothing reaches for a container or the environment behind the caller's back.
 * The controllers all answer it the same way - from the `config` service - and
 * this is that answer, written once.
 *
 * The test suite has its own, on AbstractIntegrationTestCase: same value, read
 * through the container the harness hands it rather than through Injectable.
 *
 * @property Config $config
 */
trait BaseUrlTrait
{
    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        /** @var string $url */
        $url = $this->config->path('app.url', 'http://localhost');

        return $url;
    }
}
