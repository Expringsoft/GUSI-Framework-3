<?php

namespace App\Core\Framework\Interfaces;

use App\Core\Framework\Structures\Operation;

interface Expectable
{
	public function get():mixed;

	public function set($object):void;

	public function toBe($expected):Operation;

	public function notToBe($expected):Operation;

	public function toBeStrictly($expected):Operation;

	public function notToBeStrictly($expected):Operation;

	public function toBeType($expected):Operation;

	public function toBeInstanceOf($expected):Operation;

	public function toBeNumeric():Operation;

	public function toBeNumericGreaterThan(float|int $expected):Operation;

	public function toBeNumericLessThan(float|int $expected):Operation;

	public function toBeNumericGreaterThanOrEqual(float|int $expected):Operation;

	public function toBeNumericLessThanOrEqual(float|int $expected):Operation;

	public function toBeLongerThan(int $expected):Operation;

	public function toBeShorterThan(int $expected):Operation;

	public function toBeEmpty():Operation;

	public function toBeFalsy():Operation;

	public function toBeTruthy():Operation;

	public function toBeNull():Operation;

	public function toBeUndefined():Operation;

	public function toBeDefined():Operation;

	public function toBeArray():Operation;

	public function toHaveKey($key):Operation;

	public function toHaveKeys(array $keys):Operation;

	public function toHaveProperty($property):Operation;

	public function toHaveProperties(array $properties):Operation;

	public function toHaveMethod($method):Operation;

	public function toHaveLength(int $expected):Operation;

	public function toHaveLengthGreaterThan(int $expected):Operation;

	public function toHaveLengthLessThan(int $expected):Operation;

	public function toHaveLengthGreaterThanOrEqual(int $expected):Operation;

	public function toHaveLengthLessThanOrEqual(int $expected):Operation;

	public function toMatch($regex):Operation;

	public function toContain($item):Operation;

	public function toHaveReturned($value):Operation;

	public function toHaveThrown():Operation;

	public function toHaveThrownWith($expected):Operation;

	public function toBeInstanceOfAny(array $expected):Operation;
}
