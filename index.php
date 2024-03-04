<?php

// {}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}
// {}                                                                                {}
// {}    ___ _   _ ___ ___   ___                                  _            ____  {}
// {}   / __| | | / __|_ _| | __| _ __ _ _ __  _____ __ _____ _ _| |__    __ _|__ /  {}
// {}  | (_ | |_| \__ \| |  | _| '_/ _` | '  \/ -_) V  V / _ \ '_| / /    \ V /|_ \  {}
// {}   \___|\___/|___/___| |_||_| \__,_|_|_|_\___|\_/\_/\___/_| |_\_\     \_/|___/  {}
// {}                                                                                {}
// {}                                                                                {}
// {}                                 By Expringsoft                                 {}
// {}                                                                                {}
// {}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}

// Autoload classes
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'App\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/App/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Application\App;
use App\Core\Exceptions\AppException;
use App\Core\Server\Actions;
use App\Core\Server\Logger;

/**
 * This code snippet initializes the application by creating a new instance of the App class.
 * If an exception is thrown during the initialization process, it is caught and logged using the Logger class.
 * An error response with a status code of 500 is then rendered, and the script execution is terminated.
 */
try {
    new App();
} catch (\Throwable $th) {
    Logger::LogError("App", $th->getMessage() . ' - ' . $th->getTraceAsString(), $th->getCode(), $th->getPrevious());
    Actions::renderError(500);
    die();
}