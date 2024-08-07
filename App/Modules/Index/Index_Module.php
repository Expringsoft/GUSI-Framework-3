<?php
namespace App\Modules\Index;

use App\Controllers\Index\Home;
use App\Core\Server\Router;
use App\Core\Framework\Abstracts\Module;

class Index_Module extends Module
{
	static function registerRoutes()
	{
		Router::getInstance()->addRoute('/', Home::class);
		Router::getInstance()->addRoute('/favicon.ico', [Home::class,'favicon']);
	}

	static function getFallback()
	{
		//TODO: implement fallback function
	}
}