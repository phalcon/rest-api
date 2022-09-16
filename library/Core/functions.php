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

namespace Phalcon\Api\Core;

use function function_exists;

if (true !== function_exists('Phalcon\Api\Core\appPath')) {
    /**
     * Get the application path.
     *
     * @param string $path
     *
     * @return string
     */
    function appPath(string $path = ''): string
    {
        return dirname(dirname(__DIR__)) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (true !== function_exists('Phalcon\Api\Core\envValue')) {
    /**
     * Gets a variable from the environment, returns it properly formatted or the
     * default if it does not exist
     *
     * @param string     $variable
     * @param mixed|null $default
     *
     * @return mixed
     */
    function envValue(string $variable, mixed $default = null): mixed
    {
        $value  = $_ENV[$variable] ?? $default;
        $values = [
            'false' => false,
            'true'  => true,
            'null'  => null,
        ];

        return $values[$value] ?? $value;
    }
}

if (true !== function_exists('Phalcon\Api\Core\appUrl')) {
    /**
     * Constructs a URL for links with resource and id
     *
     * @param string $resource
     * @param int    $recordId
     *
     * @return array|false|mixed|string
     */
    function appUrl(string $resource, int $recordId)
    {
        return sprintf(
            '%s/%s/%s',
            envValue('APP_URL'),
            $resource,
            $recordId
        );
    }
}
