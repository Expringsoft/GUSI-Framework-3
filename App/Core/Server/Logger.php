<?php
namespace App\Core\Server;

class Logger{

    private const DATE_TIME = "d/m/Y - H:i:s";
    private const DATE_FILE_NAME = "Y-m-d";
    
    private const DOUBLE_LINE = "\n\n";
    private const LOG_SEPARATOR = " - ";
    private const LOG_DESCRIPTOR = ": ";

    private const LOG_EXTENSION = ".log";


    /**
     * Logs an error message to the error log file.
     *
     * @param string $Logger The logger identifier.
     * @param string $Message The error message to be logged.
     * @return void
     */
    public static function LogError($Logger, $Message)
    {
        $FormatStart = date(self::DATE_TIME) . self::LOG_SEPARATOR . $Logger . self::LOG_DESCRIPTOR;
        $Filename = date(self::DATE_FILE_NAME) . self::LOG_EXTENSION;
        error_log($FormatStart . $Message . self::DOUBLE_LINE, 3, "App/Logs/Errors/" . $Filename);
    }


    /**
     * Logs a warning message to the specified logger.
     *
     * @param string $Logger The name of the logger.
     * @param string $Message The warning message to be logged.
     * @return void
     */
    public static function LogWarning($Logger, $Message)
    {
        $FormatStart = date(self::DATE_TIME) . self::LOG_SEPARATOR . $Logger . self::LOG_DESCRIPTOR;
        $Filename = date(self::DATE_FILE_NAME) . self::LOG_EXTENSION;
        error_log($FormatStart . $Message . self::DOUBLE_LINE, 3, "App/Logs/Warnings/" . $Filename);
    }

    /**
     * Logs a debug message to the debug log file.
     *
     * @param string $Logger The name of the logger.
     * @param mixed $Message The message to be logged. If it is an array, it will be converted to a JSON string.
     * @return void
     */
    public static function LogDebug($Logger, $Message)
    {
        if (gettype($Message) == "array") {
            $Message = json_encode($Message);
        }
        $FormatStart = date(self::DATE_TIME) . self::LOG_SEPARATOR . $Logger . self::LOG_DESCRIPTOR;
        $Filename = date(self::DATE_FILE_NAME) . self::LOG_EXTENSION;
        error_log($FormatStart . $Message . self::DOUBLE_LINE, 3, "App/Logs/Debug/" . $Filename);
    }
}