<?php
namespace App\Apis\v1\Sample;

use App\Core\Application\SharedConsts;
use App\Core\Framework\Abstracts\Api;
use App\Core\Framework\Enumerables\RequestMethods;
use App\Core\Framework\Structures\APIResponse;
use App\Core\Framework\Enumerables\Channels;
use App\Modules\Index\APIs_Module;

class SampleAPI extends Api{

	public function Main(...$args)
	{
		self::setChannel(Channels::BETA);
		if (!$this->isRequestMethodAllowed(RequestMethods::GET)) {
			http_response_code(SharedConsts::HTTP_RESPONSE_METHOD_NOT_ALLOWED);
			$this->buildResponse(new APIResponse(SharedConsts::HTTP_RESPONSE_METHOD_NOT_ALLOWED, "Method not allowed"));
			return;
		}
		if ($args["version"] != "v1") {
			http_response_code(SharedConsts::HTTP_RESPONSE_BAD_REQUEST);
			$this->buildResponse(new APIResponse(SharedConsts::HTTP_RESPONSE_BAD_REQUEST, "Invalid version"));
			return;
		}
		$this->buildResponse(new APIResponse(SharedConsts::HTTP_RESPONSE_OK, "Hello from Sample Api", ["API Channel" => self::getChannel()->name, "Module Channel" => self::getModuleChannel()->name]));
	}

	public static function getParentModule()
	{
		return APIs_Module::class;
	}

	public static function getModuleChannel(): Channels
	{
		return self::getParentModule()::getChannel();
	}
}