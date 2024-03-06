<?php
namespace App\Core\Framework\Abstracts;

use App\Core\Framework\Enumerables\Channels;
use App\Core\Framework\Interfaces\Modulable;

abstract class Module implements Modulable
{
	private Channels $channel;

	private function setChannel(Channels $channel)
	{
		$this->channel = Channels::BETA;
	}

	public static function getChannel()
	{
		return self::$channel;
	}

	abstract static function getFallback();
}