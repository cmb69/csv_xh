<?php

spl_autoload_register(function ($className) {
    $pieces = explode('\\', $className);
    if ($pieces[0] == 'Csv') {
        $pieces[0] = __DIR__;
        $filename = implode(DIRECTORY_SEPARATOR, $pieces) . '.php';
        if (file_exists($filename)) {
            include_once $filename;
        } else {
            array_pop($pieces);
            $filename = implode(DIRECTORY_SEPARATOR, $pieces) . '.php';
            if (file_exists($filename)) {
                include_once $filename;
            }
        }
    }
});
