<?php

namespace App\Core\Framework\Abstracts;

abstract class SingletonInstance
{
	private static $instances = [];

	protected function __construct(){}

	public static function getInstance(): static
	{
		$class = static::class;

		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = new static();
		}

		return self::$instances[$class];
	}
}