<?php

namespace Niden\Cli\Tasks;

use function in_array;

use Dariuszp\CliProgressBar;
use function Niden\Core\appPath;
use Phalcon\CLI\Task as PhTask;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ClearcacheTask extends PhTask
{
    /**
     * Adds the developer logins in the system
     */
    public function mainAction()
    {
        echo 'Clearing Cache folders' . PHP_EOL;

        $fileList    = [];
        $whitelist   = ['.', '..', '.gitignore'];
        $path        = appPath('storage/cache');
        $dirIterator = new RecursiveDirectoryIterator($path);
        $iterator    = new RecursiveIteratorIterator(
            $dirIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /**
         * Get how many files we have there and where they are
         */
        foreach ($iterator as $file) {
            if (true !== $file->isDir() && true !== in_array($file->getFilename(), $whitelist)) {
                $fileList[] = $file->getPathname();
            }
        }

        $steps = count($fileList);
        $bar   = new CliProgressBar($steps);
        $bar
            ->setColorToGreen()
            ->display();
        foreach ($fileList as $file) {
            $bar->progress();
            unlink($file);
        }

        $bar->end();

        echo 'Cleared Cache folders' . PHP_EOL;
    }
}
