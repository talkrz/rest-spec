<?php

namespace RestSpec;

class Loader
{
    /**
     * Include rest-spec definition files
     */
    public function run()
    {
        $defaultSpecDirectoryName = 'rest-spec';

        $specDirectory = dirname(COMPOSER_INSTALL) . '/../' . $defaultSpecDirectoryName . '/';
        $i = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $specDirectory,
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($i as $file) {
            if (!$file->isDir() && strpos($file->getFilename(), '.php') !== false) {
                $filename = $file->getPathname();
                require_once $filename;
            }
        }
    }
}
