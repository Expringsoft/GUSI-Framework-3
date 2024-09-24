<?php

namespace App\Core\Server;

use Exception;

class Logger
{
    private const DATE_TIME_FORMAT = 'd/m/Y - H:i:s';
    private const DATE_FILE_FORMAT = 'Y-m-d';
    private const LOG_EXTENSION = '.log';

    private const DOUBLE_LINE = "\n\n";
    private const LOG_SEPARATOR = ' - ';
    private const LOG_DESCRIPTOR = ': ';

    private const LOG_TYPE_ERROR = 'Errors';
    private const LOG_TYPE_WARNING = 'Warnings';
    private const LOG_TYPE_DEBUG = 'Debug';

    /**
     * Logs an error message to the error log file.
     *
     * @param string|null $logger The logger identifier.
     * @param mixed $message The error message to be logged.
     * @param bool $prettyPrint Enables JSON pretty print for logging data
     * @return void
     */
    public static function LogError(?string $logger, $message, bool $prettyPrint = false): void
    {
        self::writeLog(self::LOG_TYPE_ERROR, $logger, $message, $prettyPrint);
    }

    /**
     * Logs a warning message to the warning log file.
     *
     * @param string|null $logger The logger identifier.
     * @param mixed $message The warning message to be logged.
     * @param bool $prettyPrint Enables JSON pretty print for logging data
     * @return void
     */
    public static function LogWarning(?string $logger, $message, bool $prettyPrint = false): void
    {
        self::writeLog(self::LOG_TYPE_WARNING, $logger, $message, $prettyPrint);
    }

    /**
     * Logs a debug message to the debug log file.
     *
     * @param string|null $logger The logger identifier.
     * @param mixed $message The debug message to be logged.
     * @param bool $prettyPrint Enables JSON pretty print for logging data
     * @return void
     */
    public static function LogDebug(?string $logger, $message, bool $prettyPrint = false): void
    {
        self::writeLog(self::LOG_TYPE_DEBUG, $logger, $message, $prettyPrint);
    }

    /**
     * Writes a log message to the appropriate log file.
     *
     * @param string $type The type of log (Errors, Warnings, Debug).
     * @param string|null $logger The logger identifier.
     * @param mixed $message The message to be logged.
     * @param bool $prettyPrint Enables JSON pretty print for logging data
     * @return void
     */
    private static function writeLog(string $type, ?string $logger, $message, bool $prettyPrint): void
    {
        if ($logger === null) {
            $e = new Exception();
            $trace = $e->getTrace();
            $logger = $trace[1]['file'] ?? 'Unknown';
        }

        if (!is_string($message)) {
            $message = $prettyPrint ? ("\n" . json_encode($message, JSON_PRETTY_PRINT)) : json_encode($message);
        }

        $timestamp = date(self::DATE_TIME_FORMAT);
        $logEntry = $timestamp . self::LOG_SEPARATOR . $logger . self::LOG_DESCRIPTOR . $message . self::DOUBLE_LINE;

        $directory = sprintf('App/Logs/%s', $type);
        $filename = sprintf('%s/%s%s', $directory, date(self::DATE_FILE_FORMAT), self::LOG_EXTENSION);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($filename, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Generates a call trace as a string.
     *
     * @return string The call trace.
     */
    public static function generateCallTraceString(): string
    {
        $e = new Exception();
        return $e->getTraceAsString();
    }

    /**
     * Generates a call trace array.
     *
     * @return array The call trace.
     */
    public static function generateCallTraceArray(): array
    {
        $e = new Exception();
        return $e->getTrace();
    }
}