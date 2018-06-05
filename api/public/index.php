<?php

use Niden\Bootstrap\Api;
use Niden\Http\Response;

require_once __DIR__ . '/../../library/Core/autoload.php';

try {
    $bootstrap = new Api();
    $bootstrap->setup();
    $application = $bootstrap->getApplication();

    $bootstrap->run();
} catch (\Exception $ex) {
    /** @var Response $response */
    $response = $bootstrap->getResponse();
    $response->setPayloadError($ex->getMessage());
} finally {
    /** @var Response $response */
    $response = $bootstrap->getResponse();
    $response->send();
}
