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
    public const ENV_CRYPTOGRAPHY_KEY_NAME = "GUSI_FRAMEWORK_ENCRYPTION_KEY";

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
     * Automatically log errors.
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



    #region Storage configuration

    /**
     * The maximum disk space that the application can use in gigabytes.
     */
    public const APP_STORAGE_USAGE_CAP_GB = 20;

    /**
     * The minimum disk space that must be available in gigabytes.
     */
    public const MINIMUM_DISK_SPACE_GB = 1;

    /**
     * The maximum upload size in megabytes.
     * This value should be less than or equal to the value set in the php.ini file.
     */
    public const MAX_UPLOAD_SIZE_MB = 100;

    /**
     * The root folder for all stored files.
     */
    public const APP_STORAGE_FOLDER = "Files/";

    #endregion

    #region Cache configuration

    /**
     * The cache directory.
     */
    const CACHE_FOLDER = "App/Cache/";

    /**
     * The cache file extension.
     */
    const CACHE_FILE_EXTENSION = ".cache";

    /**
     * The maximum cache size in megabytes.
     */
    const MAX_CACHE_SIZE_MB = 25;

    #endregion

    #region Database configuration

    /**
     * The database host.
     */
    public const DB_HOST = "localhost";

    /**
     * The database port.
     */
    public const DB_PORT = 3306;

    /**
     * The database name.
     */
    public const DB_NAME = "gusi-framework";

    /**
     * The database charset.
     */
    public const DB_CHARSET = "utf8mb4";

    /**
     * The database user environment variable.
     */
    public const DB_USER_ENV_VAR = "GUSI_FRAMEWORK_DB_USER";

    /**
     * The database password environment variable.
     */
    public const DB_PASSWORD_ENV_VAR = "GUSI_FRAMEWORK_DB_PASSWORD";

    #endregion
}