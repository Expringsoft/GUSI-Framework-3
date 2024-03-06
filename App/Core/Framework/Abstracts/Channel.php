<?php
namespace App\Core\Framework\Abstracts;

use App\Core\Framework\Enumerables\Channels;

abstract class Channel
{
	private Channels $channel;

	private function setChannel(Channels $Channel): void
	{
		$this->channel = $Channel;
	}
	
	public function getChannel(): Channels{
		return $this->channel;
	}
}