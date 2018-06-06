<?php

namespace Niden\Cli\Tasks;

use function in_array;

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

        $whitelist   = ['.', '..', '.gitignore'];
        $path        = appPath('storage/cache');
        $dirIterator = new RecursiveDirectoryIterator($path);
        $iterator    = new RecursiveIteratorIterator(
            $dirIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if (true !== $file->isDir() && true !== in_array($file->getFilename(), $whitelist)) {
                unlink($file->getPathname());
                echo '.';
            }
        }

        echo PHP_EOL . 'Cleared Cache folders' . PHP_EOL;
    }
}
