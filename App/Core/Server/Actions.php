<?php

namespace App\Core\Server;

use App\Core\Application\Configuration;
use App\Core\Application\SharedConsts;
use App\Core\Framework\Classes\Strings;
use App\Core\Framework\Classes\LanguageManager;
use App\Core\Exceptions\AppException;
use App\Core\Exceptions\ViewException;
use LogicException;
use InvalidArgumentException;
use App\Core\Server\Logger;

class Actions
{
	public static function redirect($URL)
	{
		header('location: ' . $URL);
	}

	public static function rootRedirect($URL)
	{
		header('location: //' . self::getRootURL() . $URL);
	}

	public static function printLocalized($key)
	{
		return LanguageManager::getInstance()->get($key);
	}

	public static function getDisplayLang()
	{
		if (Router::getUserLanguage() == "default") {
			return Configuration::APP_LANG_DISPLAY;
		} else {
			return Router::getUserLanguage();
		}
	}

	public static function requireView($route, ?array $Params = null)
	{
		if (!is_null($Params)) {
			try {
				foreach ($Params as $key => $value) {
					$$key = $value;
				}
			} catch (\Exception $e) {
				throw new LogicException($e->getMessage(), 0);
			}
		}
		if (file_exists('Views/' . $route)) {
			try {
				require_once('Views/' . $route);
			} catch (\Throwable $th) {
				throw new ViewException($th->getMessage(), $th->getCode(), $th->getPrevious());
			}
		} else {
			if (Configuration::LOG_ERRORS) {
				Logger::LogError("IO Exception", self::printLocalized(Strings::VIEW_NOT_FOUND));
			}
			throw new InvalidArgumentException(self::printLocalized(Strings::VIEW_NOT_FOUND) . SharedConsts::SPACE . $route . "'", 404);
		}
	}

	public static function requireController($route)
	{
		if (file_exists('App/Controllers/' . $route)) {
			try {
				require_once('App/Controllers/' . $route);
			} catch (\Throwable $th) {
				throw new AppException($th->getMessage(), $th->getCode(), $th->getPrevious());
			}
		} else {
			if (Configuration::LOG_ERRORS) {
				Logger::LogError("IO Exception", self::printLocalized(Strings::CONTROLLER_NOT_FOUND));
			}
			throw new InvalidArgumentException(self::printLocalized(Strings::CONTROLLER_NOT_FOUND) . $route . "'", 404);
		}
	}

	public static function renderNotFound()
	{
		self::clearOutputBuffer();
		http_response_code(404);
		self::requireView(SharedConsts::PATH_VIEW_NOT_FOUND);
	}

	public static function renderError(int $code = 500)
	{
		self::clearOutputBuffer();
		http_response_code($code);
		self::requireView(SharedConsts::PATH_VIEW_ERROR);
	}

	public static function clearOutputBuffer()
	{
		if (ob_get_level() > 0) {
			ob_end_clean();
		}
	}

	public static function getRootURL()
	{
		return Configuration::LOCAL_ENVIRONMENT ? $_SERVER['SERVER_NAME'] . Configuration::PATH_URL : Configuration::PATH_URL;
	}

	public static function printScript($NombreArchivo)
	{
		return Configuration::APP_ROOT_PATH . self::getRootURL() . 'Resources/Scripts/' . $NombreArchivo . SharedConsts::STR_VERSION_PARAM . Configuration::APP_VERSION;
	}

	public static function printCSS($NombreArchivo)
	{
		return Configuration::APP_ROOT_PATH . self::getRootURL() . 'Resources/Styles/' . $NombreArchivo . SharedConsts::STR_VERSION_PARAM . Configuration::APP_VERSION;
	}

	public static function printResource($Route, $printVersion = false)
	{
		return Configuration::APP_ROOT_PATH . self::getRootURL() . 'Resources/' . $Route . ($printVersion ? SharedConsts::STR_VERSION_PARAM . Configuration::APP_VERSION : "");
	}

	public static function printFile($Route, $printVersion = false)
	{
		return Configuration::APP_ROOT_PATH . self::getRootURL() . "Files/{$Route}" . ($printVersion ? SharedConsts::STR_VERSION_PARAM . Configuration::APP_VERSION : "");
	}

	public static function printRoute(?string $Route = null)
	{
		return  Configuration::APP_ROOT_PATH . self::getRootURL() . $Route;
	}
}
