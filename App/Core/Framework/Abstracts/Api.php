<?php

namespace App\Core\Framework\Abstracts;

use App\Core\Server\Router;
use App\Core\Application\SharedConsts;

abstract class Api
{
	public $Version;
	public $MinVersion;
	public $Request;

	public $Response = ['code' => SharedConsts::HTTP_RESPONSE_OK, 'msg' => "Success", 'data' => array()];

	public function __construct($Version = "1.0", $MinVersion = "1.0", $CacheAge = "120")
	{
		header('Content-Type: application/json');
		header('Cache-Control: max-age=' . $CacheAge . ', must-revalidate');
		header('API-Version: ' . $Version);
		header('API-MinVersion: ' . $MinVersion);

		$RequestInit = new Router();
		$this->Request = $RequestInit->createRequest();

		$this->Version = $Version;
		$this->MinVersion = $MinVersion;

		$this->Main();
		$this->printResponse();
	}

	abstract public function Main();

	public function buildResponse($Code, $Message, ?array $Data = null)
	{
		$this->Response['code'] = $Code;
		$this->Response['msg'] = $Message;
		$this->Response['data'] = $Data;
	}

	public function printResponse()
	{
		echo json_encode($this->Response);
	}

	public function isRequestMethodAllowed($Needed)
	{
		return $_SERVER['REQUEST_METHOD'] == $Needed;
	}

	public function hasGETParameters(array $Keys)
	{
		$GetParams = $this->Request->getParameters()['GET'];
		foreach ($Keys as $Key) {
			if (!isset($GetParams[$Key])) {
				return false;
			}
		}
		return true;
	}

	public function hasPOSTParameters(array $Keys)
	{
		$PostParams = $this->Request->getParameters()['POST'];
		foreach ($Keys as $Key) {
			if (!isset($PostParams[$Key])) {
				return false;
			}
		}
		return true;
	}

	public function getValueDescriptor($Key, array $ArrayAssoc)
	{
		if (isset($ArrayAssoc[$Key])) {
			return $ArrayAssoc[$Key];
		} else {
			return false;
		}
	}
}