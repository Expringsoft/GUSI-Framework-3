<?php

namespace App\Core\Application;

use App\Core\Exceptions\APIException;
use App\Core\Exceptions\SecurityException;
use App\Core\Framework\Classes\Strings;
use App\Core\Server\Actions;
use App\Core\Server\Logger;
use App\Core\Server\Router;
use App\Core\Server\Session;
use DirectoryIterator;
use ReflectionClass;
use ReflectionException;
use Exception;

/**
 * The main application class responsible for initializing the application and handling exceptions.
 */
class App
{
	/**
	 * Constructs a new instance of the App class.
	 * Initializes the session, sets headers, error reporting, and exception handler.
	 * Initializes the application by calling the init() method.
	 * 
	 * @throws SecurityException If the application requires HTTPS and is not running on local environment and the current context is not secure.
	 */
	public function __construct()
	{
		Session::start();
		header('x-powered-by: GUSIFramework');
		error_reporting(Configuration::DEBUG_ENABLED ? E_ALL : 0);
		set_error_handler(array($this, 'AppErrorHandler'));
		set_exception_handler(array($this, 'AppExceptionHandler'));
		// If the application requires HTTPS, is not running on local environment and the current context is not secure, throw a SecurityException.
		if (Configuration::APP_ONLY_OVER_HTTPS && !Configuration::LOCAL_ENVIRONMENT && !Router::getInstance()->isContextSecure()) {
			throw new SecurityException("This application requires HTTPS.");
		}
		$this->init();
	}

	/**
	 * Initializes the application by loading modules and handling the request.
	 */
	private function init()
	{
		$this->loadModules();
		Router::getInstance()->handleRequest();
	}

	/**
	 * Loads modules from the App/Modules directory.
	 */
	private function loadModules()
	{
		$this->loadModulesFromDirectory('App/Modules');
	}

	/**
	 * Recursively loads modules from a directory and registers their routes.
	 *
	 * @param string $directory The directory to load modules from.
	 */
	private function loadModulesFromDirectory($directory)
	{
		$iterator = new DirectoryIterator($directory);
		foreach ($iterator as $fileinfo) {
			if ($fileinfo->isDot()) {
				continue;
			}
			if ($fileinfo->isDir()) {
				$this->loadModulesFromDirectory($fileinfo->getPathname());
			} elseif ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
				$this->registerModuleRoutes($fileinfo->getPathname());
			}
		}
	}

	/**
	 * Registers the routes for a module.
	 *
	 * @param string $filePath The file path of the module.
	 */
	private function registerModuleRoutes($filePath)
	{
		$className = $this->getClassNameFromFilePath($filePath);
		if (class_exists($className)) {
			try {
				$reflectionClass = new ReflectionClass($className);
				if ($reflectionClass->hasMethod('registerRoutes')) {
					$reflectionClass->getMethod('registerRoutes')->invoke(null);
				} else {
					Logger::LogError(self::class, "Method 'registerRoutes' not found in Module $className");
				}
			} catch (ReflectionException $e) {
				Logger::LogError(self::class, "Reflection error: " . $e->getMessage());
			} catch (Exception $e) {
				Logger::LogError(self::class, "Error invoking 'registerRoutes' in Module $className: " . $e->getMessage());
			}
		} else {
			Logger::LogError(self::class, "Module $className not found");
		}
	}

	/**
	 * Converts a file path to a fully qualified class name.
	 *
	 * @param string $filePath The file path.
	 * @return string The fully qualified class name.
	 */
	private function getClassNameFromFilePath($filePath)
	{
		// Get the relative path of the file
		$relativePath = str_replace([realpath(__DIR__ . '/../../Modules') . DIRECTORY_SEPARATOR, '.php'], '', realpath($filePath));

		// Replace directory separators with namespace separators
		$relativePath = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

		// Build the fully qualified class name
		$className = 'App\\Modules\\' . $relativePath;

		return $className;
	}

	/**
	 * Exception handler for the application.
	 * Logs the exception if AUTOLOG_EXCEPTIONS is enabled.
	 * If DEBUG_ENABLED is enabled, displays the exception details.
	 * Renders the error page using Actions::renderError().
	 *
	 * @param Exception $exception The exception to handle.
	 */
	public function AppExceptionHandler($exception)
	{
		if (Configuration::AUTOLOG_EXCEPTIONS) {
			$exceptionMessage = $exception->getMessage();
			$exceptionFile = $exception->getFile();
			$exceptionLine = $exception->getLine();
			$exceptionTrace = $exception->getTraceAsString();

			$exceptionData = "$exceptionMessage\nFile: $exceptionFile\nLine: $exceptionLine\nTrace: $exceptionTrace";

			Logger::LogError("AppExceptionHandler", $exceptionData);
		}
		if ($exception instanceof APIException) {
			Actions::clearOutputBuffer();
			echo Actions::printLocalized(Strings::API_UNHANDLED_EXCEPTION);
		} else {
			Actions::renderError();
		}
	}


	/**
	 * Handles application fatal errors and warnings.
	 * Logs the error if AUTOLOG_ERRORS is enabled.
	 * Renders the error page using Actions::renderError().
	 * If is not possible to render the error page, clears the output buffer and sets the response code to 500.
	 * Finally, terminates the script.
	 *
	 * @param int $errorNumber The error number.
	 * @param string $errorMessage The error message.
	 * @param string $errorFile The file where the error occurred.
	 * @param int $errorLine The line number where the error occurred.
	 * @return void
	 */
	public function AppErrorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
	{
		if (Configuration::AUTOLOG_ERRORS) {
			$errorData = "\nError: $errorNumber\nMessage: $errorMessage\nFile: $errorFile\nLine: $errorLine";
			Logger::LogError("AppErrorHandler", $errorData);
		}
		try {
			Actions::renderError();
		} catch (\Throwable $th) {
			Actions::clearOutputBuffer();
			http_response_code(500);
		} finally {
			die();
		}
	}
}
