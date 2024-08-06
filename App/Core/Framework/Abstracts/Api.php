<?php

namespace App\Core\Framework\Abstracts;

use App\Core\Application\Configuration;
use App\Core\Server\Router;
use App\Core\Application\SharedConsts;
use App\Core\Framework\Classes\Strings;
use App\Core\Framework\Enumerables\Channels;
use App\Core\Framework\Enumerables\RequestMethods;
use App\Core\Framework\Structures\APIResponse;
use App\Core\Server\Actions;
use App\Core\Server\Logger;
use PDOException;

abstract class Api extends Channel
{
	public $Response;

	public function __construct($Method = "Main", $args = [])
	{
		$this->setHeader("Content-Type", "application/json; charset=UTF-8");
		$this->setHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE");
		$this->setHeader("Access-Control-Allow-Headers", "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
		$this->setHeader("Access-Control-Max-Age", "600");

		$this->Response = new APIResponse(SharedConsts::HTTP_RESPONSE_NO_CONTENT, Actions::printLocalized(Strings::OPERATION_NO_RESULTS));
		
		try {
			$this->$Method(...$args);
		} catch (\Throwable $th) {
			if (Configuration::LOG_ERRORS) {
				$Message = $th->getMessage() . ".\nStack:\n" . $th->getTraceAsString() . ".\n■ Line: " . $th->getLine() . ', on: ' . $th->getFile();
				$thCode = null;
				if ($th instanceof PDOException) {
					$thCode = $th->errorInfo[1];
				} else {
					$thCode = $th->getCode();
				}
				Logger::LogError(self::class, "[{$thCode}]: {$Message}");
			}
			http_response_code(500);
			$this->buildResponse(new APIResponse(SharedConsts::HTTP_RESPONSE_INTERNAL_SERVER_ERROR, Actions::printLocalized(Strings::API_UNHANDLED_EXCEPTION)));
		} finally {
			$this->printResponse();
		}
	}

	abstract public function Main(...$args);

	abstract public static function getParentModule();

	abstract public static function getModuleChannel(): Channels;

	public function setHeader($name, $value)
	{
		header($name . ': ' . $value);
	}

	public function buildResponse(APIResponse $Response)
	{
		$this->Response = $Response;
	}

	public function printResponse()
	{
		echo $this->Response->__toJSON();
	}

	public function isRequestMethodAllowed(RequestMethods $Needed)
	{
		return $_SERVER['REQUEST_METHOD'] == $Needed->value;
	}

	public function hasGETParameters(array $Keys)
	{
		$GetParams = Router::getInstance()->getParameters()['GET'];
		foreach ($Keys as $Key) {
			if (!isset($GetParams[$Key])) {
				return false;
			}
		}
		return true;
	}

	public function hasPOSTParameters(array $Keys)
	{
		$PostParams = Router::getInstance()->getParameters()['POST'];
		foreach ($Keys as $Key) {
			if (!isset($PostParams[$Key])) {
				return false;
			}
		}
		return true;
	}
}