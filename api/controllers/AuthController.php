<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Exception;
use Gewaer\Models\Users;
use Gewaer\Models\UserLinkedSources;

/**
 * Base controller
 *
 */
class AuthController extends \Baka\Auth\AuthController
{
    /**
     * Setup for this controller
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->userLinkedSourcesModel = new UserLinkedSources();
        $this->userModel = new Users();

        if (!isset($this->config->jwt)) {
            throw new Exception('You need to configure your app JWT');
        }
    }
}
