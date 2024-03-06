<?php

namespace App\Core\Application;

/**
 * Class Configuration
 * 
 * This class contains constants for configuring the application.
 */
class Configuration
{
    /**
     * Enable or disable debug mode.
     */
    public const DEBUG_ENABLED = true;

    /**
     * Set whether the application is running in a local environment.
     */
    public const LOCAL_ENVIRONMENT = true;

    /**
     * Allow or disallow testing outside the local environment.
     */
    public const ALLOW_TESTING_OUTSIDE_LOCAL = false;

    /**
     * The version of the application.
     */
    public const APP_VERSION = "0.0.0-dev1";

    /**
     * The root path of the application.
     */
    public const APP_ROOT_PATH = "//";

    /**
     * The URL path of the application.
     */
    public const PATH_URL = "/";

    /**
     * Automatically log exceptions.
     */
    public const AUTOLOG_EXCEPTIONS = true;

    /**
     * Log errors.
     */
    public const LOG_ERRORS = true;

    /**
     * Log language errors.
     */
    public const LOG_LANGUAGE_ERRORS = true;

    /**
     * The default language code for default lang file.
     */
    public const APP_LANG_DISPLAY = "en";

    /**
     * The name of environment variable which stores encryption key for the application.
     */
    public const ENV_CRYPTOGRAPHY_KEY_NAME = "GUSI-FRAMEWORK-ENCRYPTION-KEY";
}