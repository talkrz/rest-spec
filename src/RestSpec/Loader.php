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
        $i = new \DirectoryIterator($specDirectory);
        foreach($i as $file) {
            if (!$file->isDot()) {
                $filename = $i->getPathname();
                require $filename;
            }
        }
    }
}
