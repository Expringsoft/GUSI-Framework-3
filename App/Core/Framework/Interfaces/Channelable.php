<?php
namespace App\Core\Framework\Interfaces;

use App\Core\Framework\Enumerables\Channels;

interface Channelable
{
	public static function getChannel(): Channels;

	public static function setChannel(Channels $channel);
}