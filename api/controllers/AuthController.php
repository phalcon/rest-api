<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Users;
use Gewaer\Models\UserLinkedSources;
use Gewaer\Exception\ServerErrorHttpException;

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
            throw new ServerErrorHttpException('You need to configure your app JWT');
        }
    }
}
