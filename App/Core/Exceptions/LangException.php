<?php

namespace App\Core\Exceptions;

use Exception;
use Throwable;
use App\Core\Server\Logger;
use App\Core\Application\Configuration;

class LangException extends Exception
{

	public function __construct($message, $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		if (Configuration::AUTOLOG_EXCEPTIONS) {
			Logger::LogError("(Autologger)" . Logger::class, $this->__toString() . $this->getTraceAsString());
		}
	}

	public function __toString()
	{
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
