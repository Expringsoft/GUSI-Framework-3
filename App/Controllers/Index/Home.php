<?php
namespace App\Controllers\Index;

use App\Core\Framework\Abstracts\Controller;
use App\Core\Framework\Enumerables\Channels;
use App\Modules\Index\Index_Module;

class Home extends Controller
{
	public function Main(...$args)
	{
		$this::setChannel(Channels::PROD);

		echo self::getChannel()->name;
		echo self::getModuleChannel()->name;
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