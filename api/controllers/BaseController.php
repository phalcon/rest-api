<?php

declare(strict_types=1);

namespace Baka\Api\Controllers;

/**
 * Class BaseController
 *
 * @package Niden\Api\Controllers
 *
 * @property Micro               $application
 * @property CacheMemcached      $cache
 * @property Config              $config
 * @property ModelsMetadataCache $modelsMetadata
 * @property Response            $response
 */
abstract class BaseController extends \Baka\Http\Rest\CrudExtendedController
{
}
