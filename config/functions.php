<?php

namespace Niden\Functions;

use function getenv;

/**
 * Get the application path.
 *
 * @param  string $path
 *
 * @return string
 */
function appPath(string $path = ''): string
{
    return dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

/**
 * Gets a variable from the environment, returns it properly formatted or the
 * default if it does not exist
 *
 * @param string $variable
 * @param null   $default
 *
 * @return mixed
 */
function envValue(string $variable, $default = null)
{
    $return = $default;
    $value  = getenv($variable);

    if (false !== $value) {
        switch ($value) {
            case 'false':
                $return = false;
                break;
            case 'true':
                $return = true;
                break;
            default:
                $return = $value;
                break;
        }
    }

    return $return;
}
