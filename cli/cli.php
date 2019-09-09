<?php

use Phalcon\Api\Bootstrap\Cli;

require_once __DIR__ . '/../library/Core/autoload.php';

$cli = new Cli();

$cli->setup();
$cli->run();
