<?php
namespace App\Controllers\Index;

use App\Core\Framework\Abstracts\Controller;
use App\Core\Framework\Enumerables\Channels;
use App\Modules\Index\Index_Module;

class Home extends Controller
{
	public function Main(...$args)
	{
		// Set the view
		$this->setView('Default/Home.php');
	}

	public static function getParentModule(): string
	{
		return Index_Module::class;
	}

	public static function getModuleChannel(): Channels
	{
		return self::getParentModule()::getChannel();
	}
}