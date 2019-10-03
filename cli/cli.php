<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Phalcon\Api\Bootstrap\Cli;

require_once __DIR__ . '/../library/Core/autoload.php';

$cli = new Cli();

$cli->setup();
$cli->run();
