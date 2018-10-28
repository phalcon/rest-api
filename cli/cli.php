<?php

use Gewaer\Bootstrap\Cli;

require_once __DIR__ . '/../library/Core/autoload.php';

$cli = new Cli();
$cli->setArgv($argv);
$cli->setup();
$cli->run();
