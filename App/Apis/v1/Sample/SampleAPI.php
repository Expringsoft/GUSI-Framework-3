<?php
namespace App\Apis\v1\Sample;

use App\Core\Application\SharedConsts;
use App\Core\Framework\Abstracts\Api;
use App\Core\Framework\Enumerables\RequestMethods;
use App\Core\Framework\Structures\APIResponse;
use Random\RandomException;

class SampleAPI extends Api{

	public function Main(...$args)
	{
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
		$this->buildResponse(new APIResponse(SharedConsts::HTTP_RESPONSE_OK, "Hello from Sample Api"));
	}

	public static function getParentModule()
	{
		return \App\Modules\Index\APIs_Module::class;
	}
}