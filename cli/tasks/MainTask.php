<?php

namespace Niden\Cli\Tasks;

use Phalcon\CLI\Task as PhTask;

class MainTask extends PhTask
{
    /**
     * Executes the main action of the cli mapping passed parameters to tasks
     */
    public function mainAction()
    {
        $colors = [
            'green' => "\033[0;32m(%s)\033[0m",
            'red'   => "\033[0;31m(%s)\033[0m",
        ];

        echo '******************************************************' . PHP_EOL;
        echo ' Phalcon Team | (C) ' . date('Y') . PHP_EOL;
        echo '******************************************************' . PHP_EOL;
        echo PHP_EOL;
        echo 'Usage: team <command>';

        echo PHP_EOL . PHP_EOL;

        $commands = [
            sprintf(
                '  --help         %s %s',
                sprintf($colors['green'], 'safe'),
                'shows the help screen/available commands'
            ),
            sprintf(
                '  --clear-cache  %s %s',
                sprintf($colors['green'], 'safe'),
                'clears the cache folders'
            ),
        ];

        echo 'Commands:' .  PHP_EOL;

        foreach ($commands as $command) {
            echo $command . PHP_EOL;
        }

        echo PHP_EOL;
    }
}
