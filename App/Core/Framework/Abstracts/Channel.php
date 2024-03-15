<?php
namespace App\Core\Framework\Abstracts;

use App\Core\Framework\Enumerables\Channels;
use App\Core\Framework\Interfaces\Channelable;

abstract class Channel implements Channelable
{
	protected static $channels = [];

	public static function getChannel(): Channels
	{
		$class = static::class;
		return self::$channels[$class] ?? Channels::DEV;
	}

	public static function setChannel(Channels $channel)
	{
		$class = static::class;
		self::$channels[$class] = $channel;
	}
}