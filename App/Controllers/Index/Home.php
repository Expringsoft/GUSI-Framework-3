<?php
namespace App\Controllers\Index;

use App\Core\Framework\Abstracts\Controller;
use App\Core\Framework\Classes\LanguageManager;
use App\Core\Server\Router;
use App\Modules\Index\Index_Module;

class Home extends Controller
{
	public function Main()
	{
		$this->setView('Default/Home.php');
	}

	public static function getParentModule()
	{
		return Index_Module::class;
	}
}