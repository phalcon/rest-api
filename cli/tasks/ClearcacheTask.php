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

        $fileList = $this->getFileList();
        $steps    = count($fileList);
        $bar      = new CliProgressBar($steps);
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

    /**
     * Iterate through the folders and get the file list
     *
     * @return array
     */
    private function getFileList(): array
    {
        $fileList    = [];
        $whitelist   = ['.', '..', '.gitignore'];
        $iterator    = $this->getIterator();

        /**
         * Get how many files we have there and where they are
         */
        foreach ($iterator as $file) {
            if (true !== $file->isDir() && true !== in_array($file->getFilename(), $whitelist)) {
                $fileList[] = $file->getPathname();
            }
        }

        return $fileList;
    }

    /**
     * @return RecursiveIteratorIterator
     */
    private function getIterator(): RecursiveIteratorIterator
    {
        $path        = appPath('storage/cache');
        $dirIterator = new RecursiveDirectoryIterator($path);

        return new RecursiveIteratorIterator(
            $dirIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );
    }
}
