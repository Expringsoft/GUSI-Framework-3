<?php

namespace App\Modules\Index;

use App\Apis\v1\Sample\SampleAPI;
use App\Core\Server\Router;
use App\Core\Framework\Abstracts\Module;

class APIs_Module extends Module
{
	static function registerRoutes()
	{
		Router::getInstance()->addRoute('/apis/{version}/sample', SampleAPI::class);
	}

	static function getFallback()
	{
		//TODO: implement fallback function
	}
}