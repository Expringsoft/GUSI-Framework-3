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
     * The version of the application.
     */
    public const APP_VERSION = "0.0.0-dev1";

    /**
     * The URL path of the application.
     * On production replace with the actual URL (domain) with / at the end.
     */
    public const PATH_URL = "/";

    /**
     * The root path of the application.
     * Used by Actions class to redirect to the root of the application for dynamic URLs.
     */
    public const APP_ROOT_PATH = "//";

    /**
     * The default language code for default lang file.
     */
    public const APP_LANG_DISPLAY = "en";

    /**
     * The name of environment variable which stores encryption key for the application.
     */
    public const ENV_CRYPTOGRAPHY_KEY_NAME = "GUSI-FRAMEWORK-ENCRYPTION-KEY";

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
     * If the application is only accessible via HTTPS (production).
     */
    public const APP_ONLY_OVER_HTTPS = true;

    /**
     * The default timezone for the application.
     */
    public const DEFAULT_TIMEZONE = "UTC";

    /**
     * Prevents setting model properties if they are not defined in the model.
     */
    public const STRICT_MODELS = true;

    /**
     * Automatically log exceptions.
     */
    public const AUTOLOG_EXCEPTIONS = true;

    /**
     * Automatically log exceptions.
     */
    public const AUTOLOG_ERRORS = true;

    /**
     * Log errors.
     */
    public const LOG_ERRORS = true;

    /**
     * Log language errors.
     */
    public const LOG_LANGUAGE_ERRORS = true;
}