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
use App\Core\Server\Router;

class Actions
{
	public static function redirect($URL)
	{
		header('location: ' . $URL);
	}

	public static function rootRedirect($URL)
	{
		header('location: ' . Router::getInstance()->getBaseUrl() . $URL);
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

	public static function printScript($fileName)
	{
		$fileName = 'Scripts/' . $fileName;
		return Router::getInstance()->getBaseUrl() . 'public/' . Configuration::APP_VERSION . '/' . $fileName;
	}

	public static function printCSS($fileName)
	{
		$fileName = 'Styles/' . $fileName;
		return Router::getInstance()->getBaseUrl() . 'public/' . Configuration::APP_VERSION . '/' . $fileName;
	}

	public static function printResource($Route)
	{
		return Router::getInstance()->getBaseUrl() . 'public/' . Configuration::APP_VERSION . '/' . $Route;
	}

	public static function printFile($Route, $printVersion = false)
	{
		return Router::getInstance()->getBaseUrl() . Configuration::APP_STORAGE_FOLDER . $Route . ($printVersion ? SharedConsts::STR_VERSION_PARAM . Configuration::APP_VERSION : "");
	}

	public static function printRoute(?string $Route = null)
	{
		return Router::getInstance()->getBaseUrl() . $Route;
	}
}