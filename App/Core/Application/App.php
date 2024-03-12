<?php

namespace App\Core\Application;

use App\Core\Exceptions\SecurityException;
use App\Core\Server\Actions;
use App\Core\Server\Logger;
use App\Core\Server\Router;
use App\Core\Server\Session;
use App\Modules\Index\Index_Module;

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
		header('Server: CustomApache');
		error_reporting(Configuration::DEBUG_ENABLED ? E_ALL : 0);
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
	 * Loads the modules by registering their routes.
	 */
	private function loadModules()
	{
		Index_Module::registerRoutes();
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

		if (Configuration::DEBUG_ENABLED) {
			echo "<pre>";
			echo "Exception occurred: " . $exception->getMessage() . "<br>";
			echo "File: " . $exception->getFile() . "<br>";
			echo "Line: " . $exception->getLine() . "<br>";
			echo "Trace: " . $exception->getTraceAsString() . "<br>";
			echo "</pre>";
		}
		Actions::renderError();
	}
}
