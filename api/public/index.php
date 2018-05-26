<?php

use Niden\Bootstrap\Api;
use Niden\Http\Response;

require_once __DIR__ . '/../../config/autoload.php';

try {
    $bootstrap = new Api();
    $bootstrap->setup();
    $application = $bootstrap->getApplication();

    $bootstrap->run();

    /** @var Response $response */
    $response = $bootstrap->getResponse();
} catch (\Exception $ex) {
    /** @var Response $response */
    $response = $bootstrap->getResponse();
    $response->setPayloadError($ex->getMessage());
} finally {
    /** @var Response $response */
    $response = $bootstrap->getResponse();

    if (true !== $response->isSent()) {
        $response->send();
    }
}
